# GR8TECH Human Resources Information System

GR8TECH is a Laravel-based HRIS and payroll platform for managing employee records, attendance, schedules, requests, payroll processing, reports, documents, loans, and role-based employee services.

## Core Features

- Multi-company data isolation with an active-company context
- Employee, department, position, account, and document management
- Schedule templates, bulk scheduling, and calendar-based schedule management
- Time in/out, DTR import, attendance corrections, exception review, and audit markers
- Leave, overtime (OT), and Official Business (OB) requests and approvals
- Two-stage request expiration: an initial 24-hour review window and one final 24-hour re-request window
- Semi-monthly payroll periods, pre-payroll validation, generation, review, finalization, locking, payments, and payslips
- Payroll adjustments, statutory deductions, withholding tax, paid and unpaid leave, loans, and overtime computation
- Consolidated reports, exports, notifications, and role-based Universal Search
- Responsive light and dark interfaces

## Roles and Access

| Role | Main access |
|---|---|
| Employee | Personal attendance, schedule, Leave, OT, OB, payslips, loans, documents, and profile |
| Manager | Personal employee services plus authorized team review and management functions |
| HR | Workforce administration, attendance and request review, payroll operations, reports, and loan-type management |
| Admin | Authorized system-wide administration, developer tools, account management, and company-scoped operations |

Employee-linked Manager, HR, and Admin accounts use **My Portal** for their own requests and records. Users cannot approve or reject their own requests where review separation is required.

## Key Workflows

### Attendance to payroll

1. Define the payroll period and employee schedules.
2. Record attendance through time in/out, DTR import, or authorized correction.
3. Review incomplete logs, invalid durations, possible wrong schedules, rest-day duty, and unverified Leave/OT/OB markers.
4. Validate Attendance, Leave, Official Business, and Overtime.
5. Generate and review payroll.
6. Return the payroll for documented correction when necessary.
7. Finalize, lock, process payment, and release payslips.

Future scheduled dates are displayed as **Scheduled**, not **Absent**. Finalized or locked payroll periods protect their source attendance and request records from incompatible changes.

### Leave, OT, and Official Business

- Pending requests may be edited or cancelled by their owner, subject to payroll-period protection.
- Overlapping pending requests can be replaced only after explicit confirmation where supported.
- Approved requests cannot be replaced through the pending-request flow.
- An unattended request expires after 24 hours and may be re-requested once.
- The re-request receives a final 24-hour review window; no further re-request is allowed after final expiry.
- Approval, rejection, cancellation, expiry, reviewer, timestamps, and reasons are retained where modeled.

### Payroll deadlines and locks

- Periods support request, preparation, validation, and lock deadlines.
- Deadline states are displayed as Not Set, Open, Due Soon, or Overdue.
- Deadline extensions require an actor, timestamp, and reason.
- Deadlines guide and audit the workflow; finalization and locking remain deliberate authorized actions unless a controller explicitly blocks a transition.

## Technology Stack

- PHP 8.2+
- Laravel 12
- Blade, Alpine.js, and Tailwind CSS 3
- Vite 6
- MySQL or PostgreSQL for application data
- SQLite for automated tests where configured
- Font Awesome 6
- DomPDF, Laravel Excel, and PhpSpreadsheet for reports and exports

## Prerequisites

- PHP 8.2 or later with the required Laravel extensions
- Composer
- Node.js 18 or later and npm
- MySQL or PostgreSQL
- Git

## Local Installation

1. Clone the repository and enter the project directory.

   ```bash
   git clone https://github.com/jnsncrxx/GR8TECH.git
   cd GR8TECH
   ```

2. Install backend and frontend dependencies.

   ```bash
   composer install
   npm install
   ```

3. Create the environment file and application key.

   **PowerShell**

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   **Bash**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure the database and application URL in `.env`.

   ```env
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=payrolllaravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Create the schema and build the frontend assets.

   ```bash
   php artisan migrate
   npm run build
   ```

6. Start the application.

   ```bash
   composer run dev
   ```

   Alternatively, run Laravel and Vite separately:

   ```bash
   php artisan serve
   npm run dev
   ```

Queue workers must be running for queued notifications and jobs.

## Testing and Validation

Run the complete automated suite before opening or merging a pull request:

```bash
php artisan test
```

The validation-contract branch currently passes:

- 106 tests
- 334 assertions

The suite covers attendance normalization and exceptions, period deadlines and locks, company isolation, schedule validation, Leave/OT/OB behavior, payroll calculations, personal role portals, request expiration, reporting, and Universal Search access rules.

Build and cache checks:

```bash
npm run build
php artisan view:cache
```

## Production Checklist

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Use production-safe environment values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
```

Before release:

- Run migrations and the full test suite against a disposable or backed-up database.
- Confirm the active-company context and company-scoped filters.
- Confirm queue scheduling for notifications, reminders, and request-expiry sweeps.
- Protect `.env`, OAuth credentials, payroll exports, database backups, and employee documents.
- Do not deploy demo cleanup scripts or reference SQL dumps without review and backup.

## Contribution Workflow

1. Update your branch from `develop`.
2. Create a focused `feature/<branch-name>` branch.
3. Implement and test one coherent change set.
4. Use a clear conventional commit message, such as `fix: align validation and response contracts`.
5. Push the branch and open a pull request targeting `develop`.
6. Include a concise summary, changed behavior, and validation results in the pull-request description.

## Additional References

- [`README_timekeeping_import.md`](README_timekeeping_import.md) — DTR/timekeeping import guidance
- [`GMAIL_SMTP_SETUP.md`](GMAIL_SMTP_SETUP.md) — mail configuration
- [`EMAIL_TEMPLATES.md`](EMAIL_TEMPLATES.md) — notification email templates

## License

This project uses the MIT license declared in `composer.json`.
