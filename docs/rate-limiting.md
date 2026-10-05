# Rate Limiting & Abuse Prevention

## 1. Rate Limiting Strategy Matrix

| Endpoint Area | Key Identifiers | Rate Limit | Config Variable | Response |
| :--- | :--- | :--- | :--- | :--- |
| **Authentication** (`/login`, `/register`) | Client IP Address | 10 requests / min | `RATE_LIMIT_AUTH_PER_MINUTE` | `429 Too Many Requests` |
| **URL Creation** (`POST /urls`) | Authenticated User ID / IP | 60 requests / min | `RATE_LIMIT_URL_CREATE_PER_MINUTE` | `429 Too Many Requests` |
| **Public Redirection** (`GET /{code}`) | Client IP Address | 120 requests / min | `RATE_LIMIT_REDIRECT_PER_MINUTE` | `429 Too Many Requests` |
| **Analytics Query** (`GET /analytics`) | Authenticated User ID / IP | 30 requests / min | `RATE_LIMIT_ANALYTICS_PER_MINUTE` | `429 Too Many Requests` |
| **Global API** (`/api/v1/*`) | User ID / IP | 60 requests / min | `RATE_LIMIT_API_PER_MINUTE` | `429 Too Many Requests` |

---

## 2. Dynamic Configuration

All rate limiting thresholds are centralized in `config/ratelimit.php` and can be customized per environment without code modifications.
