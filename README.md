# Vigil

Self-hosted exception tracking and log viewing for Laravel applications. Monitor errors and logs across all your apps from a single dashboard.

Vigil is a lightweight alternative to Sentry, Bugsnag, and Flare designed for developers who want to keep their error data on their own infrastructure.

## Features

- **Multi-app monitoring** - Track exceptions and logs from multiple Laravel apps in one place
- **Exception grouping** - Duplicate exceptions are grouped by fingerprint (class + file + line)
- **Stack trace viewer** - Collapsible frames with source code snippets
- **Log viewer** - Filter logs by level, channel, search, and date range with inline expandable detail
- **Batched log shipping** - Logs are buffered during the request lifecycle and sent in a single HTTP call after the response, with zero impact on response time
- **Request context** - URL, method, headers, body, and authenticated user info
- **Environment metadata** - PHP version, Laravel version, hostname, app environment
- **Status management** - Mark exceptions as resolved or ignored; auto-reopens if they recur
- **Search and filter** - Filter by status, search by class name, message, or file
- **Configurable log retention** - Set retention per-project or use the global default; old logs are automatically pruned daily
- **Setup wizard** - Browser-based first-run setup, no CLI needed
- **Dark theme** - Built with Tailwind CSS

## Architecture

Vigil has two parts:

| Component | Description |
|-----------|-------------|
| **Vigil Server** (this repo) | Laravel app with dashboard and API for receiving exceptions and logs |
| **[Vigil Client](https://github.com/byronfichardt/vigil-client)** | Composer package installed in your Laravel apps |

## Requirements

- PHP 8.4+
- SQLite (default, zero config) or any Laravel-supported database
- Composer

## Quick Start

### 1. Clone and install

```bash
git clone https://github.com/byronfichardt/vigil.git
cd vigil
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Run migrations

```bash
php artisan migrate
```

### 3. Start the server

```bash
php artisan serve
```

### 4. Create your account

Visit `http://localhost:8000` - the setup wizard will guide you through creating your admin account.

### 5. Create a project

Click "New Project" in the dashboard. You'll get an API key to use in your client apps.

### 6. Install the client

In each Laravel app you want to monitor:

```bash
composer require vigil/client
```

Add to your `.env`:

```env
VIGIL_URL=https://your-vigil-server.com
VIGIL_KEY=your-project-api-key
```

That's it. Exceptions are automatically captured and logs at `warning` level and above are forwarded to Vigil.

## Configuration

### Log Retention

By default, log entries are pruned after 30 days. You can configure this globally via your `.env`:

```env
VIGIL_LOG_RETENTION_DAYS=30
```

Or set a custom retention per-project in the project settings page.

The pruning runs daily via the scheduler. Make sure your cron is set up:

```bash
* * * * * cd /path-to-vigil && php artisan schedule:run >> /dev/null 2>&1
```

You can also run it manually:

```bash
php artisan vigil:prune-logs
```

## Deployment

### Docker / Kamal

Vigil includes a production-ready Dockerfile and Kamal deployment config.

```bash
cp config/deploy.yml.example config/deploy.yml
# Edit config/deploy.yml with your server details
```

The Dockerfile builds a multi-stage image with PHP-FPM, Nginx, and Supervisor. SQLite data persists via a volume mount.

See `config/deploy.yml.example` for the full configuration template.

### Important: SQLite Persistence

When running in Docker, mount the SQLite database file to persist data across deploys:

```yaml
# In your deploy.yml or docker-compose.yml
volumes:
  - /data/vigil/database.sqlite:/var/www/html/database/database.sqlite
```

Create the file on the host first:

```bash
touch /data/vigil/database.sqlite
```

### Using PostgreSQL or MySQL

Vigil defaults to SQLite but supports any Laravel-compatible database. Update your `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=vigil
DB_USERNAME=vigil
DB_PASSWORD=secret
```

## API

### Ingest Exception

```
POST /api/exceptions
```

**Headers:**
- `X-Vigil-Key: your-project-api-key`
- `Content-Type: application/json`

**Body:**

```json
{
  "exception_class": "App\\Exceptions\\PaymentFailedException",
  "message": "Payment gateway returned error",
  "file": "/var/www/app/Services/PaymentService.php",
  "line": 42,
  "stack_trace": [
    {
      "file": "/var/www/app/Services/PaymentService.php",
      "line": 42,
      "class": "App\\Services\\PaymentService",
      "function": "charge",
      "code_snippet": { "40": "...", "41": "...", "42": "..." }
    }
  ],
  "request_url": "https://myapp.com/checkout",
  "request_method": "POST",
  "request_headers": {},
  "request_body": {},
  "user_info": { "id": 1, "email": "user@example.com" },
  "environment": "production",
  "hostname": "web-01",
  "php_version": "8.4.0",
  "laravel_version": "12.0",
  "occurred_at": "2025-01-15T10:30:00+00:00"
}
```

**Response:** `201 Created`

```json
{
  "message": "Exception recorded.",
  "group_id": 1,
  "occurrence_id": 5
}
```

### Ingest Logs (Batch)

```
POST /api/logs
```

**Headers:**
- `X-Vigil-Key: your-project-api-key`
- `Content-Type: application/json`

**Body:**

```json
{
  "logs": [
    {
      "level": "warning",
      "channel": "stack",
      "message": "Slow query detected",
      "context": { "query": "SELECT ...", "time_ms": 1500 },
      "extra": {},
      "logged_at": "2025-01-15T10:30:00+00:00"
    }
  ],
  "environment": "production",
  "hostname": "web-01",
  "request_url": "https://myapp.com/api/users",
  "request_method": "GET"
}
```

The `logs` array accepts up to 200 entries per request. Request-level metadata (`environment`, `hostname`, `request_url`, `request_method`) is shared across all entries in the batch.

**Response:** `201 Created`

```json
{
  "message": "Logs recorded.",
  "count": 1
}
```

**Rate limit:** 300 requests per minute per API key (shared across both endpoints).

## Performance

Vigil is designed to have minimal impact on your applications:

- **Client overhead:** ~2-5ms per exception (synchronous HTTP with 2s timeout, 1s connect timeout)
- **Log shipping is non-blocking:** Logs are buffered in memory during the request and flushed in a single HTTP call via terminable middleware, after the response has been sent to the user
- **Zero overhead on happy path:** The exception reporter only runs when an exception is thrown; the log handler only appends to an in-memory array
- **If Vigil is down:** The client silently fails - your app is never affected
- **Sensitive data redaction:** Passwords, tokens, and credit card fields are automatically redacted before sending
- **Server-side:** Exception grouping uses database indexes for fast lookups; log bulk inserts minimize write overhead

## Limitations

- **Laravel only** - The client SDK is built for Laravel. Other PHP frameworks or languages are not currently supported.
- **No real-time updates** - The dashboard requires a page refresh to see new exceptions (no WebSocket/SSE push)
- **No notifications** - No Slack, email, or webhook notifications (planned for a future release)
- **Single-user auth** - The setup wizard creates one account. Multi-user with roles is not yet supported.
- **SQLite concurrency** - Under very high write loads (hundreds of exceptions per second), SQLite may become a bottleneck. Switch to PostgreSQL for high-traffic deployments.

## License

MIT
