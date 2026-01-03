# Car Rental Management System

Laravel 12 + Filament v4 application for managing car rentals.

## Setup

```bash
# Start services
./vendor/bin/sail up -d

# Setup database
./vendor/bin/sail artisan migrate --seed

# Create admin user
./vendor/bin/sail artisan make:filament-user

# Build frontend
./vendor/bin/sail npm run build
```

Access at `http://localhost` • Admin panel at `http://localhost/admin`

## Running the Application

```bash
./vendor/bin/sail up -d    # Start
./vendor/bin/sail stop     # Stop
```

## Common Commands

```bash
./vendor/bin/sail artisan test         # Run tests
./vendor/bin/sail bin pint              # Format code
./vendor/bin/sail artisan migrate      # Run migrations
./vendor/bin/sail artisan pail         # View logs
./vendor/bin/sail npm run dev          # Dev mode with hot reload
```

## Tech Stack

- Laravel 12
- PHP 8.2+
- Filament v4
- MySQL 8.4 (via Sail)
- Vite + Tailwind CSS v4
