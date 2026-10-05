# Scalability & Growth Strategy

## Scaling Tier Progression

```
[Phase 1: 1K - 10K Users]
Single VPS / Container (Nginx + PHP-FPM + MySQL + Redis)

[Phase 2: 100K Users]
Load Balancer (HAProxy / ALB) -> Multi-instance App Nodes -> Dedicated Redis Cluster -> MySQL Primary + Read Replica

[Phase 3: 1M Users]
Geo-distributed Anycast DNS -> Edge CDNs for Static Assets -> Read Replica Pool -> Partitioned Analytics Tables -> Auto-scaling Queue Workers

[Phase 4: 10M+ Users & Billions of Clicks]
Sharded URL Databases (Hash-based on Short Code) -> Kafka / Redpanda Click Stream -> ClickHouse / TimescaleDB for Analytics -> Global Redis Cluster
```

---

## Technical Scaling Solutions

1. **Short-Code Space Capacity**:
   - 6-character Base62 string provides: $62^6 = 56,800,235,584$ (56.8 Billion) unique combinations.
   - 7-character Base62 string provides: $62^7 = 3.52$ Trillion unique combinations.
2. **Database Partitioning for Analytics**:
   - Partition `url_clicks` table by `RANGE (YEAR(clicked_at) * 100 + MONTH(clicked_at))` to ensure indexing and drops remain ultra-fast.
3. **Dedicated Click Analytics Engine**:
   - For 100M+ monthly clicks, redirect workers stream raw events into ClickHouse or Amazon Timestream, while MySQL remains dedicated strictly to transactional metadata.
