# Caching Strategy & Redis Design

## 1. Key Design & Namespace Structure

| Key Pattern | Purpose | Value Payload | Default TTL |
| :--- | :--- | :--- | :--- |
| `url:short:{short_code}` | Direct short-code resolution | JSON payload (`id`, `original_url`, `is_active`, `expires_at`) | 24 Hours (86,400s) |
| `url:alias:{custom_alias}` | Custom alias resolution | JSON payload (`id`, `original_url`, `is_active`, `expires_at`) | 24 Hours (86,400s) |
| `url:clicks:{url_id}` | Fast in-memory counter | Atomic integer | Persistent / synced |

---

## 2. Cache Invalidation Patterns

1. **Write-Through Pre-Warming**:
   - When a user creates a new short URL, it is persisted to MySQL and immediately written to Redis.
   - Result: 100% cache hit rate on first access.
2. **Explicit Eviction on Mutation**:
   - When a URL is updated (e.g. deactivated, title edited, new alias) or deleted, both `url:short:{code}` and `url:alias:{alias}` are evicted via `Cache::forget()`.
3. **Graceful Degradation**:
   - If Redis becomes unreachable, `UrlCacheService` catches connection exceptions and falls back transparently to indexed MySQL queries.
