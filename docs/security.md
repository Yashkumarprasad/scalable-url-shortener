# Security Implementation & Hardening

## 1. Authentication & Session Security
- **Laravel Sanctum Tokens**: Cryptographically hashed in the database (`personal_access_tokens`).
- **Immediate Revocation**: Explicit token deletion on logout.
- **Account State Verification**: Suspended or inactive users are rejected at both middleware and controller levels.

---

## 2. Input Sanitation & Protection
- **URL Scheme Restriction**: Strictly enforces `http://` and `https://` schemes. Explicitly blocks `javascript:`, `data:`, `file:`, `vbscript:` to prevent stored XSS or SSRF vector attacks.
- **Reserved Short Code Filtering**: Prohibits creation of alias clashes with reserved system keywords (`api`, `admin`, `health`, `docs`, `login`, `logout`, etc.).
- **SQL Injection Defense**: 100% parameterized queries via Laravel Eloquent and Query Builder.

---

## 3. Credential & Audit Sanitization
- Passwords hashed using Bcrypt (`BCRYPT_ROUNDS=12` in production, `4` in test).
- Passwords and tokens excluded from logging and serialization via `$hidden` model attributes.
- Non-sensitive health endpoint ensures zero internal IP/credential leakage.
