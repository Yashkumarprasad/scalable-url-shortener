# Database Design & Optimization

## 1. Schema Design

```mermaid
erDiagram
    users ||--o{ urls : creates
    urls ||--o{ url_clicks : receives

    users {
        bigint id PK
        string name
        string email UK
        string password
        string role "USER, ADMIN"
        string status "ACTIVE, INACTIVE, SUSPENDED"
        timestamp email_verified_at
        timestamps created_at_updated_at
    }

    urls {
        bigint id PK
        bigint user_id FK
        text original_url
        string short_code UK "Indexed 32"
        string custom_alias UK "Indexed 64"
        string title
        timestamp expires_at "Indexed"
        boolean is_active "Indexed"
        bigint click_count "Atomic counter"
        timestamps created_at_updated_at
        timestamp deleted_at "Soft deletes"
    }

    url_clicks {
        bigint id PK
        bigint url_id FK "Indexed"
        string ip_address "IPv4/IPv6"
        text user_agent
        text referer
        string device_type "DESKTOP, MOBILE, TABLET, BOT"
        string country_code "ISO 3166-1"
        string country_name
        string city
        timestamp clicked_at "Indexed"
        timestamp created_at
    }
```

---

## 2. Strategic Indexing Strategy

1. **`urls.short_code` & `urls.custom_alias`**:
   - Unique B-Tree indexes. Enables O(log N) lookup on cache misses.
2. **`urls (is_active, expires_at)`**:
   - Compound index for high-speed availability validation.
3. **`urls (user_id, created_at)`**:
   - Compound index optimizing user dashboard pagination without filesort.
4. **`url_clicks (url_id, clicked_at)`**:
   - Compound index facilitating fast range queries for date filters (`from` and `to`).
5. **`url_clicks (url_id, device_type)` & `url_clicks (url_id, country_code)`**:
   - Indexes supporting real-time `COUNT(*)` aggregations without full table scans.

---

## 3. Separation of Core Metadata & Analytics Data

### Why separate `urls` and `url_clicks`?
- **Row Width & Memory Footprint**: Keeping `urls` lean ensures InnoDB buffer pool caches significantly more active URLs in RAM.
- **Write Contention**: Hundreds of concurrent clicks do not lock the core `urls` record. Clicks append linearly into `url_clicks` via asynchronous queue jobs.
- **Data Lifecycle Management**: Historical clicks can be archived or partitioned by time range without affecting active URL routing.
