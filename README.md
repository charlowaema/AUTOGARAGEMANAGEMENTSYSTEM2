# 🔧 AGMS — Auto Garage Management System

A full-featured Laravel 10+ garage management system built with **Blade + Tailwind CSS**, with role-based authentication.

---

## 🔐 Authentication

AGMS uses a custom auth system (inspired by Laravel Breeze) with **3 roles**:

| Role | Access |
|------|--------|
| **Administrator** | Everything — including user management |
| **Technician** | Service bay, checklists, parts |
| **Receptionist** | Customers, vehicles, service entry |

**No self-registration** — only admins can create accounts.

### Default Login (after seeding)
```
Email:    admin@agms.local
Password: password
```
> ⚠ Change immediately after first login via **Account → Change Password**

---

## 📋 Features

| Module | Description |
|--------|-------------|
| **Dashboard** | Stats overview, recent services, vehicles due for service |
| **Garage Entry** | Search returning customers by plate or register new vehicle |
| **Service Bay** | Interactive checklist (Regular/Full), parts logging |
| **Parts Inventory** | Stock management, restock, low-stock alerts |
| **Customers** | Driver profiles, vehicle history |
| **Vehicles** | Fleet registry with make/model lookup |
| **Reports** | Printable service reports with cost breakdown |

### Service Types
- **Regular Service** — Every 5,000 km or 90 days (5 checklist items)
- **Full Service** — Every 10,000 km or 180 days (19 checklist items)

---

## 🚀 Installation

### Requirements
- PHP 8.1+
- Composer
- MySQL / PostgreSQL / SQLite
- Node.js (optional — Tailwind is loaded via CDN)

### Setup Steps

```bash
# 1. Create a new Laravel project and replace with AGMS files
composer create-project laravel/laravel agms
cd agms

# 2. Copy all AGMS files into the project
#    (overwrite the default files with the AGMS versions)

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Set database credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agms
DB_USERNAME=root
DB_PASSWORD=your_password

# 5. Run migrations and seed lookup data
php artisan migrate --seed

# 6. Serve the application
php artisan serve
```

Visit: **http://localhost:8000**

---

## 📁 File Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php
│   ├── VehicleController.php
│   ├── CustomerController.php
│   ├── ServiceRecordController.php
│   └── PartController.php
└── Models/
    ├── Vehicle.php
    ├── VehicleMake.php
    ├── VehicleModel.php
    ├── Customer.php
    ├── ServiceRecord.php
    ├── ServiceChecklistItem.php
    ├── ServicePart.php
    ├── ChecklistTemplate.php
    └── Part.php

database/
├── migrations/
│   ├── ..._create_vehicle_makes_table.php
│   ├── ..._create_vehicle_models_table.php
│   ├── ..._create_customers_table.php
│   ├── ..._create_vehicles_table.php
│   ├── ..._create_parts_table.php
│   ├── ..._create_service_records_table.php
│   ├── ..._create_checklist_tables.php
│   └── ..._create_service_parts_table.php
└── seeders/
    └── DatabaseSeeder.php         ← Seeds makes, models, parts

resources/views/
├── layouts/app.blade.php          ← Main layout with sidebar
├── dashboard.blade.php
├── vehicles/  (index, create, edit, show)
├── customers/ (index, create, edit, show)
├── services/  (index, create, bay, show, report)
└── parts/     (index, create, edit)

routes/web.php
```

---

## 🗄️ Database Schema

```
vehicle_makes  ──┐
vehicle_models ──┼──► vehicles ──► customers
                 │        │
                 │        └──► service_records ──► service_checklist_items
                 │                     │
                 │                     └──► service_parts ──► parts
                 └─────────────────────────────────────────────────────┘
```

---

## 🔄 Service Workflow

```
1. Garage Entry → Search by plate number
   ├── Returning? → Show vehicle & service history
   └── New? → Register vehicle & customer first

2. Open Service → Select type (Regular/Full)
   → Auto-generates checklist from template
   → Calculates next service date & mileage

3. Service Bay
   ├── Complete checklist items (Pending/Done/N/A)
   └── Add parts used (deducts from inventory)

4. Close Service → Enter labour cost
   → Generates printable report with totals
   → Updates vehicle mileage
```

---

## ⚙️ Customisation

### Add More Vehicle Makes/Models
Edit `DatabaseSeeder.php` or add via database directly.

### Add Checklist Items
Edit `ChecklistTemplate::regularItems()` and `ChecklistTemplate::fullItems()` in `app/Models/ChecklistTemplate.php`.

### Change Currency
Replace `KES` in blade views with your local currency symbol.

### Authentication
Add Laravel Breeze or Jetstream:
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
```

---

## 📄 License
MIT — Free to use, modify, and distribute.
