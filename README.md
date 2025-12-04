# tpApiAdvanced

A Symfony 7.4 REST API project featuring JWT authentication, product management, and user authentication.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [API Documentation](#api-documentation)
- [Project Structure](#project-structure)

## Requirements

- **PHP**: >= 8.4
- **Composer**: 2.x
- **Database**: SQLite (default) or PostgreSQL
- **Extensions**:
  - ext-ctype
  - ext-iconv
  - ext-pdo
  - ext-sqlite3 (for SQLite)
  - ext-pgsql (for PostgreSQL)

## Installation

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd tpApiAdvanced
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Generate JWT keys**:
   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```
   This will create the private and public keys in `config/jwt/`.

## Configuration

1. **Environment Configuration**:
   
   Copy the `.env` file and customize it for your environment:
   ```bash
   cp .env .env.local
   ```

   Edit `.env.local` and configure:
   ```env
   APP_ENV=dev
   APP_SECRET=your-secret-key-here
   
   # Database Configuration
   # For SQLite (default):
   DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
   
   # For PostgreSQL:
   # DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
   ```

2. **JWT Configuration**:
   
   The JWT keys are generated in step 3 of installation. The bundle configuration is already set up in `config/packages/lexik_jwt_authentication.yaml`.

## Database Setup

1. **Create the database** (not necessary if you are using SQLite):
   ```bash
   php bin/console doctrine:database:create
   ```

2. **Run migrations**:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

3. **Load fixtures (optional)**:
   
   To populate the database with sample data:
   ```bash
   php bin/console doctrine:fixtures:load
   ```

## Running the Application

### Development Server

Start the Symfony development server:
```bash
symfony server:start
```

Or use PHP's built-in server:
```bash
php -S localhost:8000 -t public/
```

The API will be available at `http://localhost:8000`.

## API Documentation

### Authentication Endpoints

#### Login
```http
POST /login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response**:
```json
{
  "message": "Identification successful",
  "tokens": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "def50200..."
  }
}
```

#### Refresh Token
```http
POST /token/refresh
Content-Type: application/json

{
  "refresh_token": "def50200..."
}
```

**Response**:
```json
{
  "message": "Refresh successful",
  "tokens": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "def50200..."
  }
}
```

#### Logout
```http
POST /logout
Content-Type: application/json

{
  "refreshToken": "def50200..."
}
```

**Response**:
```json
{
  "message": "Logout successful"
}
```

### Product Endpoints

#### List Products
```http
GET /products
Authorization: Bearer <access_token>
```

**Response**:
```json
{
  "items": [
    {
      "id": 1,
      "label": "Product Name",
      "price": 29.99,
      "stock": 100,
      "createdAt": "2024-01-01T00:00:00+00:00",
    },
     {
      "id": 2,
      "label": "Product Name",
      "price": 29.99,
      "stock": 100,
      "createdAt": "2024-01-01T00:00:00+00:00",
    }
  ]
}
```

#### Pagination and limit
To get a paginated result, use the `cursor` and `limit` parameters.
```http
GET /products?cursor=5&limit=10
Authorization: Bearer <access_token>
```
`cursor` is the first product id and `limit` is the number of product returned.

#### Sorting
To get a sorted result, use the `sort` paramter.
```http
GET /products?sort=stock,-price
Authorization: Bearer <access_token>
```
These are the sortable fields : `'id', 'label', 'price', 'stock', 'createdAt'` (defined in `App\Model\ProductFilter::SORTABLE_FIELDS`)
You can sort over multiple fields, separated by comma in order of sorting priority.
Default sorting direction is ascending, you can also define descending direction by adding `-` before the field.

#### Relations
To add product relations to the result, use the `include` parameter.
```http
GET /products?include=category
Authorization: Bearer <access_token>
```

**Response**
```json
{
  "items": [
    {
      "id": 1,
      "label": "Product Name",
      "price": 29.99,
      "stock": 100,
      "createdAt": "2024-01-01T00:00:00+00:00",
      "category": {
        "label": "category1",
        "description": "this is the first category"
      }
    },
     {
      "id": 2,
      "label": "Product Name",
      "price": 29.99,
      "stock": 100,
      "createdAt": "2024-01-01T00:00:00+00:00"
      "category": {
        "label": "category2",
        "description": "this is the second category"
      }
    }
  ]
}
```

#### Filtering
To get a filtered result, use the parameters defined in `App\Model\ProductFilter::FILTER_LABELS` which are : `'category', 'price-lte', 'price-gte', 'price-lt', 'price-gt'`
```http
GET /products?category=category1&price-lt=200
Authorization: Bearer <access_token>
```
This example returns the products of `category1` that have price which is lesser than `200`

#### Field projection
You can choose which fields are included in the result using the `fields` parameter
```http
GET /products?fields=label,stock
Authorization: Bearer <access_token>
```
The available fields are : `'id', 'label', 'price', 'stock', 'createdAt'` (defined in `App\Model\ProductFilter::PROJECTABLE_FIELDS`)

## Project Structure

```
tpApiAdvanced/
├── bin/                 # Executable files (console, phpunit)
├── config/              # Application configuration
│   ├── packages/        # Bundle configurations
│   └── routes/          # Routing configuration
├── migrations/          # Database migrations
├── src/
│   ├── Controller/      # HTTP controllers
│   ├── DataFixtures/    # Database fixtures
│   ├── Entity/          # Doctrine entities
│   ├── Model/           # Data models
│   ├── Repository/      # Doctrine repositories
│   └── Services/        # Business logic services
├── tests/               # Test files
└── var/                 # Cache, logs, and generated files
```

## License

Do whatever you want
