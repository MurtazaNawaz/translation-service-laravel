# Translation Management API

High-performance, scalable API for managing translations across multiple languages with context tags.


## Features

- Multi-locale translations (e.g., en, fr, es)
- Tag translations for context (mobile, web, desktop)
- CRUD endpoints for translations
- Search translations by key, value, or tag
- JSON export endpoint
- Pagination for listing translations
- Token-based API authentication
- Optimized for 100k+ records


## Requirements

- PHP >= 8.2
- Composer
- PostgreSQL
- Redis


## Installation

1. Clone the repository:
```bash
git clone git@github.com:MurtazaNawaz/translation-service-laravel.git
cd trans-api


Install dependencies:

composer install


Copy environment file and set database/redis credentials:

cp .env.example .env


Generate application key:

php artisan key:generate


Run migrations and seed database:

php artisan migrate --seed


---

### **Step 6 — Add Running the API**
```markdown
## Running the API

Start local server:
```bash
php artisan serve


API base URL: http://127.0.0.1:8000/api


---

### **Step 7 — Add API endpoints table**
```markdown
## API Endpoints

| Method | Endpoint                   | Description                  |
|--------|----------------------------|------------------------------|
| GET    | /api/translations          | List translations (paginated)|
| GET    | /api/translations/{id}     | View single translation      |
| POST   | /api/translations          | Create translation           |
| PUT    | /api/translations/{id}     | Update translation           |
| DELETE | /api/translations/{id}     | Delete translation           |
| GET    | /api/translations/export   | Export all translations JSON |

