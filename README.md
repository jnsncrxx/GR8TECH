# GR8TECH Payroll Management System

A comprehensive Laravel-based HRIS and payroll management system with employee management, attendance tracking, schedules, requests, payroll processing, and department organization.

## Features

- **Employee Management**: Complete CRUD operations for employee records
- **Department and Position Management**: Organize employees by company, department, and position
- **Attendance System**:
  - Time In/Out functionality
  - Daily attendance and timekeeping records
  - DTR import and attendance corrections
  - Schedule management and templates
  - Overtime, Leave, and Official Business requests
  - Attendance exception review before payroll generation
- **Payroll Management**:
  - Semi-monthly payroll periods
  - Pre-payroll validation, generation, review, finalization, and locking
  - Paid and unpaid leave, overtime, loans, deductions, payments, and payslips
- **Universal Search and Reports**: Role- and company-scoped records, consolidated reports, and exports
- **Real-time Clock**: Live time display using Philippine Standard Time
- **Responsive Design**: Desktop, tablet, and mobile layouts with light and dark modes
- **Role-based Access**: Employee, Manager, HR, and Admin access with personal My Portal functions
- **Multi-company Isolation**: Records and filter options follow the active company

## Tech Stack

- **Backend**: Laravel 12 and PHP 8.2+
- **Frontend**: Blade Templates, Alpine.js, and Tailwind CSS v3
- **Database**: MySQL/PostgreSQL; SQLite where configured for tests
- **Build Tool**: Vite 6
- **Icons**: Font Awesome 6
- **Reports and Exports**: DomPDF, Laravel Excel, and PhpSpreadsheet

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL/PostgreSQL
- Git

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/jnsncrxx/GR8TECH.git
cd GR8TECH
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Setup

For Bash:

```bash
cp .env.example .env
php artisan key:generate
```

For PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### 5. Database Configuration

Update your `.env` file with the correct application URL and database credentials:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payrolllaravel
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Run Database Migrations

```bash
php artisan migrate
```

### 7. Build Assets

```bash
npm run build
```

### 8. Start Development Server

Use the combined development command:

```bash
composer run dev
```

Or run Laravel and Vite separately:

```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

Run a queue worker when testing queued notifications, reminders, or jobs.

## Frontend Development

### Available NPM Scripts

```bash
npm run dev          # Start Vite dev server with hot reloading
npm run build        # Build for production
npm run watch        # Build and watch for changes
npm run hot          # Start dev server with host access
```

### Tailwind CSS Configuration

The project uses Tailwind CSS v3 with the following plugins:

- `@tailwindcss/forms` - Form styling
- `@tailwindcss/typography` - Typography utilities

### UI Conventions

- Responsive tables and cards
- Compact, scrollable data views where appropriate
- Consistent action icons, buttons, status colors, and modal styling
- Light and dark mode support across forms, tables, rows, backgrounds, and text

## Database Structure

### Key Tables

- `companies` - Company records and active-company scope
- `employees` - Employee information
- `departments` - Department data
- `positions` - Position data
- `attendance_records` - Daily attendance tracking
- `employee_schedules` - Employee schedule assignments
- `schedule_templates` - Reusable schedule definitions
- `overtime_requests` - Overtime requests and review history
- `leave_requests` - Leave requests and expiration state
- `official_business_requests` - Official Business requests
- `payroll_periods` - Payroll cutoff and workflow state
- `payrolls` - Generated payroll records
- `payments` - Payroll payment records
- `employee_loans` - Employee loan requests and balances

## Authentication & Roles

The system supports role-based access control:

- **Admin**: Authorized system administration, workforce, payroll, reports, accounts, and developer tools
- **HR**: Workforce management, attendance and request review, payroll operations, reports, and loan types
- **Manager**: Personal employee services and authorized team/request review; can review employee loans but cannot manage loan types
- **Employee**: Personal attendance, schedules, requests, payslips, loans, documents, and profile

Employee-linked Admin, HR, and Manager accounts use **My Portal** for their own employee records and requests. Users cannot approve or reject their own requests where review separation is required.

## Request and Payroll Rules

- Pending Leave, OT, and OB requests may be edited or cancelled by their owner when the related payroll period is not protected.
- Requests expire after an initial 24-hour window and may be re-requested once for a final 24-hour window.
- Final expiry prevents another re-request.
- Future scheduled dates display as **Scheduled**, not **Absent**.
- Attendance, Leave, OB, and OT must pass pre-payroll validation before payroll generation.
- Finalized and locked payroll periods protect overlapping attendance and requests from incompatible changes.
- Payroll deadlines provide status, reminders, extensions, and audit information; finalization and locking remain authorized actions.

## Testing

Before opening or merging a pull request, run:

```bash
php artisan test
npm run build
```

Ensure all automated tests pass and the frontend builds successfully.

## Responsive Design

- **Desktop**: Full-featured interface with tables and management tools
- **Mobile**: Card-based and responsive layouts for touch interaction
- **Tablet**: Optimized layouts for medium screens
- **Dark Mode**: Consistent dark backgrounds, fields, tables, status cards, borders, and readable text

## Deployment

### Production Build

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Variables for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

Before deployment, run migrations and tests on a disposable or backed-up database, confirm queue scheduling, and protect `.env`, database backups, payroll exports, OAuth credentials, and employee documents.

## Contributing

1. Update your local `develop` branch.
2. Create a focused feature branch (`git switch -c feature/amazing-feature`).
3. Commit your changes (`git commit -m "feat: add amazing feature"`).
4. Push the branch (`git push --set-upstream origin feature/amazing-feature`).
5. Open a pull request targeting `develop`.
6. Include a short summary, changed behavior, and validation results.

## Additional References

- [`README_timekeeping_import.md`](README_timekeeping_import.md) - DTR/timekeeping import guidance
- [`GMAIL_SMTP_SETUP.md`](GMAIL_SMTP_SETUP.md) - SMTP configuration
- [`EMAIL_TEMPLATES.md`](EMAIL_TEMPLATES.md) - Notification email templates

## Learning Laravel

Laravel has extensive official documentation and a broad learning ecosystem:

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Bootcamp](https://bootcamp.laravel.com)
- [Laracasts](https://laracasts.com)

## Security Vulnerabilities

Do not disclose security vulnerabilities publicly. Report them privately to the repository maintainers with reproduction steps and affected components.

## License

This project uses the MIT license declared in `composer.json`.
