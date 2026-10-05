# Asynchronous Queue Architecture

## 1. Asynchronous Ingestion Workflow

To achieve sub-millisecond redirect latency, database insertions for click telemetry are strictly decoupled from the HTTP redirect lifecycle:

```mermaid
sequenceDiagram
    autonumber
    actor User as User Browser
    participant API as Redirect Engine
    participant RedisQ as Redis Queue (analytics)
    participant Worker as Background Worker
    participant MySQL as MySQL Database

    User->>API: GET /lar10x
    API->>RedisQ: Dispatch RecordUrlClickJob
    API-->>User: HTTP 302 Found (Location: https://laravel.com)
    
    Note over User,API: User is redirected immediately without waiting for DB writes

    RedisQ->>Worker: Dequeue Job Payload
    Worker->>MySQL: INSERT INTO url_clicks (...)
    Worker->>MySQL: UPDATE urls SET click_count = click_count + 1 WHERE id = ?
```

---

## 2. Resilience, Retries & Dead-Letter Handling

- **Queue Driver**: Redis.
- **Dedicated Queue**: `analytics` (segregated from default email or notification queues).
- **Retry Policy**:
  - `tries = 3`
  - `backoff = [5, 15, 30]` seconds (exponential backoff).
- **Worker Execution Command**:
  ```bash
  php artisan queue:work redis --queue=analytics,default --sleep=3 --tries=3 --timeout=60
  ```
- **Failure Logging**: Unrecoverable job failures are written to `failed_jobs` table with full stack trace and logged via Monolog.
