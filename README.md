<p align="center">
  <img src="https://img.shields.io/badge/status-portfolio%20ready-brightgreen" alt="Portfolio Ready">
  <img src="https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Vue_3-4FC08D?logo=vue.js" alt="Vue 3">
  <img src="https://img.shields.io/badge/TypeScript-3178C6?logo=typescript" alt="TypeScript">
  <img src="https://img.shields.io/badge/Inertia.js-8B5CF6?logo=inertia" alt="Inertia.js">
  <img src="https://img.shields.io/badge/SQLite-003B57?logo=sqlite" alt="SQLite">
  <img src="https://img.shields.io/badge/license-MIT-blue" alt="License">
</p>

<p align="center">
  <a href="https://github.com/mofasa798/my-duit-v2">GitHub</a> ·
  <a href="#features">Features</a> ·
  <a href="#tech-stack">Tech Stack</a> ·
  <a href="#architecture">Architecture</a> ·
  <a href="#installation">Installation</a> ·
  <a href="#project-structure">Structure</a> ·
  <a href="#screenshots">Screenshots</a> ·
  <a href="#deployment">Deployment</a>
</p>

<br>

# MyDuit — Personal Finance Tracker 💰

**MyDuit** is a full-stack personal finance tracking application built with **Laravel 13**, **Vue 3**, and **Inertia.js**. It helps users manage their income and expenses through an intuitive dashboard, detailed analytics, and exportable financial reports.

> "Duit" is Indonesian slang for "money" — manage your money, your way.

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 🔐 **Authentication** | Login, register, password reset, email verification — powered by Laravel Breeze |
| 📊 **Dashboard** | AG Grid spreadsheet with inline editing (double-click to edit), quick transaction form |
| 💳 **Transactions** | Full CRUD with search, type/category/date filtering, paginated table |
| 📈 **Analytics** | Interactive pie (expense by category), bar (income vs expense), and line charts (daily trends) |
| 📋 **Reports** | Generate period-based reports, export to **XLSX / CSV / PDF**, or print directly |
| 📱 **Responsive** | Fully responsive layout — works on desktop, tablet, and mobile |
| ⚡ **SPA Experience** | Inertia.js drives fast page transitions without full browser reloads |

---

## 🛠️ Tech Stack

### Backend
| Technology | Purpose |
|------------|---------|
| **Laravel 13** | PHP web framework |
| **SQLite** | Lightweight, file-based database |
| **Laravel Breeze** | Authentication scaffolding |
| **Laravel Sanctum** | API token management |
| **maatwebsite/Laravel-Excel** | XLSX & CSV report exports |
| **barryvdh/laravel-dompdf** | PDF report exports |

### Frontend
| Technology | Purpose |
|------------|---------|
| **Vue 3** | Reactive UI framework |
| **TypeScript** | Type-safe JavaScript |
| **Inertia.js v2** | SPA routing via Laravel |
| **PrimeVue 4** | UI component library (Aura theme) |
| **Tailwind CSS 3** | Utility-first styling |
| **Pinia** | State management |
| **AG Grid** | Spreadsheet-like transaction table |
| **ApexCharts** | Interactive analytics charts |

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     Laravel Backend                      │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌────────┐ │
│  │ Routes   │→ │Controllers│→ │ Services │→ │ Models │ │
│  │ (web.php)│  │           │  │          │  │ (ORM)  │ │
│  └──────────┘  └──────────┘  └──────────┘  └────────┘ │
│                                        ↕                │
│                                    ┌────────┐          │
│                                    │ SQLite │          │
│                                    └────────┘          │
└───────────────────────┬─────────────────────────────────┘
                        │ Inertia.js (JSON over HTTP)
┌───────────────────────▼─────────────────────────────────┐
│                     Vue 3 SPA (Frontend)                 │
│  ┌──────────┐  ┌──────────┐  ┌───────────────────┐     │
│  │  Pages/  │→ │Components│→ │   Layouts/        │     │
│  │(Inertia) │  │  (reuse) │  │(AuthenticatedLayout)│     │
│  └──────────┘  └──────────┘  └───────────────────┘     │
│       ↕                                                    │
│  ┌──────────────────────────────────────────────────┐   │
│  │  Ziggy Routes · Pinia Stores · PrimeVue · AG Grid│   │
│  └──────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```

### Key Design Decisions
- **Zero database server needed** — SQLite keeps setup trivial
- **Services layer** — Controllers stay thin; business logic lives in `app/Services/`
- **Form Requests** — Validation and sanitization centralized in `app/Http/Requests/`
- **Policies** — Authorization gates for Transactions and Reports
- **No full-page reloads** — Inertia.js streams page data over JSON

---

## 🚀 Installation

### Prerequisites
- PHP 8.3+
- [Composer](https://getcomposer.org/)
- Node.js 18+
- npm

### Setup

```bash
# 1. Clone the repository
git clone https://github.com/mofasa798/my-duit-v2.git
cd my-duit-v2

# 2. Install PHP dependencies
composer install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Create and migrate the database (with sample data)
touch database/database.sqlite
php artisan migrate --seed

# 5. Install frontend dependencies & build for production
npm install
npm run build

# 6. Run the application (use two terminals)
php artisan serve                 # Terminal 1 — Laravel server (http://localhost:8000)
npm run dev                       # Terminal 2 — Vite dev server (HMR)
```

> **Note:** The root URL (`/`) redirects to the **login page**.  
> First time? Click **"Register here"** or use the seeded test account below.

### Test Credentials

| Field | Value |
|-------|-------|
| **Email** | `test@example.com` |
| **Password** | `password` |

---

## 📁 Project Structure

```
my-duit-v2/
├── app/
│   ├── Exports/              # XLSX/CSV export classes
│   ├── Http/
│   │   ├── Controllers/      # Route handlers
│   │   ├── Middleware/        # Auth & other middleware
│   │   └── Requests/          # Form validation
│   ├── Models/               # Eloquent models (User, Category, Transaction, Report)
│   ├── Policies/             # Authorization policies
│   ├── Providers/            # Service providers
│   └── Services/             # Business logic layer
├── config/                   # App configuration
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database schema
│   └── seeders/              # Test data seeders
├── docs/                     # 📁 Portfolio documentation
│   ├── erd.md                # Entity-Relationship Diagram (Mermaid)
│   └── folder-structure.md   # Annotated project tree
├── public/
│   └── screenshots/          # 📁 Portfolio screenshots
├── resources/
│   ├── css/
│   ├── js/
│   │   ├── Components/       # Reusable Vue components
│   │   ├── composables/      # Vue composables (useToast, etc.)
│   │   ├── Layouts/          # Page layouts
│   │   ├── Pages/            # Inertia page components
│   │   └── types/            # TypeScript interfaces
│   └── views/                # Blade templates (exports, app shell)
├── routes/
│   ├── web.php               # Main application routes
│   └── auth.php              # Authentication routes
├── storage/                  # Laravel storage (logs, cache, etc.)
├── tests/                    # PHPUnit test suite
├── DEPLOYMENT.md             # 📁 Deployment guide (VPS, Docker, Railway)
└── AI_CONTEXT.md             # Development roadmap (for AI agents)
```

---

## 📸 Screenshots

> *(Screenshots coming soon — capture from your local instance)*

| Page | Preview |
|------|---------|
| 🔐 Login | `public/screenshots/login.png` |
| 📊 Dashboard | `public/screenshots/dashboard.png` |
| 💳 Transactions | `public/screenshots/transactions.png` |
| 📈 Analytics | `public/screenshots/analytics.png` |
| 📋 Reports | `public/screenshots/reports.png` |

---

## 🚀 Deployment

See **[DEPLOYMENT.md](./DEPLOYMENT.md)** for detailed deployment guides including:

- 🖥️ **VPS** (Nginx + PHP-FPM) — traditional server setup
- 🐳 **Docker** — containerized deployment with Docker Compose
- ☁️ **Railway** — platform-as-a-service deployment

---

## 🧪 Running Tests

```bash
php artisan test
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

<p align="center">
  Made with ❤️ using Laravel, Vue, and Inertia
</p>
