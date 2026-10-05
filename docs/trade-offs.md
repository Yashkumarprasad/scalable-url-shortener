# Architectural Trade-Offs & Decisions

| Decision | Selected Approach | Alternative Considered | Justification & Trade-Off |
| :--- | :--- | :--- | :--- |
| **Monolith vs. Microservices** | Modular Clean Monolith | Microservices (Go/Node) | A modular Laravel monolith eliminates distributed transaction complexity, network overhead, and DevOps overhead while allowing sub-second redirects and asynchronous queue offloading. |
| **HTTP 302 vs. HTTP 301** | HTTP 302 Found | HTTP 301 Moved Permanently | 301 allows client browsers to cache redirects permanently, which would bypass our click analytics and prevent real-time URL updates or deactivation. 302 ensures full observability and control. |
| **Queue vs. Direct Write** | Redis Queues for clicks | Direct MySQL Insert | Direct inserts add 15-40ms disk I/O latency to every redirect. Queues allow the redirect response to finish in <2ms while absorbing high-volume traffic bursts. |
| **Short Code Generation** | Random Base62 + Retry | Pre-generated Key Generation Service (KGS) | Random Base62 generation is simple, memory-free, and collision probability at 6 characters with 56.8B combinations is negligible. Auto-length expansion guarantees zero deadlock. |
