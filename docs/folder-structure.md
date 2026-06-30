# Folder Structure

## MyDuit — Project Tree

```
my-duit-v2/
│
├── app/                              # Laravel application core
│   ├── Exports/
│   │   └── ReportExport.php          # XLSX/CSV export (FromView)
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AnalyticsController   # Analytics page with charts
│   │   │   ├── DashboardController   # Main dashboard + inline editing
│   │   │   ├── ProfileController     # User profile management
│   │   │   ├── ReportController      # Report CRUD + export
│   │   │   └── TransactionController  # Transaction CRUD
│   │   ├── Middleware/               # Auth & other middleware
│   │   └── Requests/
│   │       ├── StoreTransactionRequest
│   │       └── UpdateTransactionRequest
│   │
│   ├── Models/
│   │   ├── Category.php              # Income/Expense categories
│   │   ├── Report.php                # Generated financial reports
│   │   ├── Transaction.php           # Individual transactions
│   │   └── User.php                  # Authenticated user
│   │
│   ├── Policies/
│   │   ├── ReportPolicy.php          # Report authorization
│   │   └── TransactionPolicy.php     # Transaction authorization
│   │
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   │
│   └── Services/
│       ├── CategoryService.php       # Category query logic
│       ├── ReportService.php         # Report generation + listing
│       └── TransactionService.php    # Transaction query + filtering
│
├── bootstrap/                        # Laravel bootstrap & cache
│
├── config/                           # Application configuration
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── ...
│
├── database/
│   ├── factories/
│   │   ├── CategoryFactory.php
│   │   ├── TransactionFactory.php
│   │   └── UserFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_06_23_022230_create_categories_table.php
│   │   ├── 2026_06_23_022234_create_transactions_table.php
│   │   └── 2026_06_23_022238_create_reports_table.php
│   └── seeders/
│       ├── CategorySeeder.php
│       ├── DatabaseSeeder.php
│       └── TransactionSeeder.php
│
├── docs/                             # 📁 Portfolio documentation
│   ├── erd.md                        # ERD diagram (Mermaid)
│   └── folder-structure.md           # This file
│
├── public/
│   ├── build/                        # Vite production build
│   ├── screenshots/                  # 📁 Portfolio screenshots
│   ├── favicon.ico
│   ├── index.php                     # Laravel front controller
│   └── robots.txt
│
├── resources/
│   ├── css/
│   │   └── app.css                   # Tailwind CSS imports
│   ├── js/                           # Vue 3 + TypeScript frontend
│   │   ├── Components/
│   │   │   ├── ConfirmDialog.vue     # Reusable delete confirmation
│   │   │   ├── EmptyState.vue        # Empty state placeholder
│   │   │   ├── SkeletonLoader.vue    # Loading skeletons
│   │   │   ├── SpendingChart.vue     # ApexCharts wrapper
│   │   │   ├── ToastNotification.vue # Flash message toast
│   │   │   └── ...                   # Breeze components
│   │   ├── Layouts/
│   │   │   └── AuthenticatedLayout.vue # Main nav + app shell
│   │   ├── Pages/
│   │   │   ├── Welcome.vue           # Landing page (public)
│   │   │   ├── Dashboard.vue         # Main dashboard
│   │   │   ├── Dashboard/
│   │   │   │   ├── QuickTransactionForm.vue
│   │   │   │   └── TransactionTable.vue  # AG Grid component
│   │   │   ├── Transactions/
│   │   │   │   ├── Index.vue         # Transaction list + filters
│   │   │   │   ├── Create.vue        # New transaction form
│   │   │   │   └── Edit.vue          # Edit transaction form
│   │   │   ├── Analytics/
│   │   │   │   └── Index.vue         # Charts + summary cards
│   │   │   ├── Reports/
│   │   │   │   └── Index.vue         # Generate & list reports
│   │   │   ├── Profile/
│   │   │   │   └── Edit.vue          # User profile (Breeze)
│   │   │   └── Auth/                 # Breeze auth pages
│   │   ├── types/
│   │   │   ├── index.d.ts            # App type definitions
│   │   │   ├── global.d.ts           # Global type augmentations
│   │   │   └── vite-env.d.ts
│   │   ├── app.ts                    # Vue app bootstrap
│   │   └── bootstrap.ts              # Axios setup
│   └── views/
│       ├── app.blade.php             # Root Blade template
│       └── exports/
│           ├── report.blade.php      # XLSX export template
│           └── report_pdf.blade.php  # PDF export template
│
├── routes/
│   ├── auth.php                      # Auth routes (Breeze)
│   ├── console.php                   # Artisan commands
│   └── web.php                       # Main application routes
│
├── storage/                          # Laravel file storage
│
├── tests/
│   ├── Feature/                      # Feature tests
│   └── Unit/                         # Unit tests
│
├── vendor/                           # Composer dependencies
│
├── .env.example                      # Environment template
├── .gitignore
├── AI_CONTEXT.md                     # Dev roadmap (AI-friendly)
├── composer.json
├── package.json
├── tailwind.config.js
├── tsconfig.json
└── vite.config.js                    # Vite build config
```
