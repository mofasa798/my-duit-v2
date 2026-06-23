# Phase 3 — Transaction CRUD System

## Objective

Implement full CRUD (Create, Read, Update, Delete) for transactions, including search and filter functionality.

## Tech Stack

- **Backend**: Laravel (Controller, Service Layer, Form Request)
- **Frontend**: Vue 3 + Inertia.js + TypeScript
- **UI**: PrimeVue components + TailwindCSS

## Tasks

### Backend

- [ ] Create `TransactionController` with methods: `index`, `store`, `update`, `destroy`
- [ ] Create `TransactionService` for business logic (separate from controller)
- [ ] Create `StoreTransactionRequest` and `UpdateTransactionRequest` Form Requests
- [ ] Implement search by description/notes
- [ ] Implement filter by category, type (income/expense), and date range

### Validation Rules

- `amount` — required, numeric, min:0
- `category_id` — required, exists in categories table
- `transaction_date` — required, date format
- `type` — required, in: income, expense
- `description` — nullable, string

### Frontend

- [ ] Create transaction list page (`Pages/Transactions/Index.vue`)
- [ ] Create transaction form component (used for create & edit)
- [ ] Implement create, edit, and delete flows using Inertia
- [ ] Add search input and filter dropdowns

### Routes

- [ ] Define resource routes for transactions in `web.php`

## Acceptance Criteria

- User can create a new transaction (income or expense)
- User can view list of all their transactions
- User can edit an existing transaction
- User can delete a transaction
- User can search transactions by description
- User can filter transactions by category, type, and date range
- All inputs are validated server-side
- Transactions are scoped to the authenticated user
