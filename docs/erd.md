# Entity-Relationship Diagram (ERD)

## MyDuit — Database Schema

```mermaid
erDiagram
    USERS ||--o{ CATEGORIES : "has many"
    USERS ||--o{ TRANSACTIONS : "has many"
    USERS ||--o{ REPORTS : "has many"
    CATEGORIES ||--o{ TRANSACTIONS : "belongs to"

    USERS {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string remember_token
        timestamps
    }

    CATEGORIES {
        bigint id PK
        bigint user_id FK "nullable — global categories"
        string name
        enum type "income | expense"
        timestamps
    }

    TRANSACTIONS {
        bigint id PK
        bigint user_id FK
        bigint category_id FK
        decimal amount "15,2"
        text description "nullable"
        date date
        timestamps
    }

    REPORTS {
        bigint id PK
        bigint user_id FK
        date start_date
        date end_date
        decimal total_income "15,2"
        decimal total_expense "15,2"
        decimal balance "15,2"
        timestamps
    }
```

## Relationships Summary

| Relationship | Type | Details |
|---|---|---|
| User → Categories | One-to-Many | `user_id` is nullable — global categories available to all users |
| User → Transactions | One-to-Many | Cascade on delete |
| User → Reports | One-to-Many | Cascade on delete |
| Category → Transactions | One-to-Many | Cascade on delete |
| Transaction → Category | Many-to-One | Eager-loaded for display |

## Key Design Decisions

1. **`user_id` nullable on categories** — Allows global/shared categories (e.g., "Food", "Transport") while users can also create custom ones.
2. **`amount` stored as `decimal(15,2)`** — Sufficient precision for daily finance tracking.
3. **Reports are snapshots** — A `Report` record stores pre-computed `total_income`, `total_expense`, and `balance` rather than re-calculating on every view, making the reports page fast.
4. **SQLite database** — Zero-config, file-based database ideal for portfolio projects and personal use. The schema is fully portable to MySQL/PostgreSQL if needed.
