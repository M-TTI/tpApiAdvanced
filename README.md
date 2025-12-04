# tpApiAdvanced

A Symfony 7.4 REST API project featuring JWT authentication, product management, and user authentication.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Docker Setup](#docker-setup)
- [Project Structure](#project-structure)

## Requirements

- **PHP**: >= 8.2
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

1. **Create the database**:
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

### Production

For production deployment, configure your web server (Apache/Nginx) to point to the `public/` directory.

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

**Query Parameters**:
- `category`: Filter by category ID
- `minPrice`: Minimum price filter
- `maxPrice`: Maximum price filter
- `label`: Filter by product label (partial match)

**Response**:
```json
{
  "items": [
    {
      "id": 1,
      "label": "Product Name",
      "price": 29.99,
      "stock": 100,
      "category": {
        "id": 1,
        "name": "Category Name"
      },
      "createdAt": "2024-01-01T00:00:00+00:00",
      "updatedAt": "2024-01-01T00:00:00+00:00"
    }
  ]
}
```

## Testing

Run the test suite using PHPUnit:

```bash
# Run all tests
php bin/phpunit

# Run tests with coverage
php bin/phpunit --coverage-html coverage/
```

## Docker Setup

The project includes Docker Compose configuration for easy setup.

### Using Docker

1. **Start the services**:
   ```bash
   docker-compose up -d
   ```

2. **Install dependencies**:
   ```bash
   docker-compose exec php composer install
   ```

3. **Generate JWT keys**:
   ```bash
   docker-compose exec php php bin/console lexik:jwt:generate-keypair
   ```

4. **Setup database**:
   ```bash
   docker-compose exec php php bin/console doctrine:database:create
   docker-compose exec php php bin/console doctrine:migrations:migrate
   docker-compose exec php php bin/console doctrine:fixtures:load
   ```

### Docker Services

- **Database**: PostgreSQL 16 (accessible on port 5432)
  - Default credentials: `app` / `!ChangeMe!`
  - Database name: `app`

## Project Structure

```
tpApiAdvanced/
├── assets/              # Frontend assets (Stimulus controllers, CSS)
├── bin/                 # Executable files (console, phpunit)
├── config/              # Application configuration
│   ├── packages/        # Bundle configurations
│   └── routes/          # Routing configuration
├── migrations/          # Database migrations
├── public/              # Web root directory
│   └── index.php        # Front controller
├── src/
│   ├── Controller/      # HTTP controllers
│   ├── DataFixtures/    # Database fixtures
│   ├── Entity/          # Doctrine entities
│   ├── Model/           # Data models
│   ├── Repository/      # Doctrine repositories
│   └── Services/        # Business logic services
├── templates/           # Twig templates
├── tests/               # Test files
├── translations/        # Translation files
└── var/                 # Cache, logs, and generated files
```

## Key Features

- **JWT Authentication**: Secure token-based authentication with refresh tokens
- **User Management**: User registration and authentication
- **Product Management**: CRUD operations for products with filtering capabilities
- **Category System**: Product categorization
- **RESTful API**: Clean REST API design
- **Doctrine ORM**: Database abstraction and entity management
- **Symfony 7.4**: Latest Symfony framework features
- **Docker Support**: Easy containerized deployment

## Development

### Clear Cache

```bash
php bin/console cache:clear
```

### Create a New Entity

```bash
php bin/console make:entity
```

### Create a Migration

```bash
php bin/console make:migration
```

### Debug Routes

```bash
php bin/console debug:router
```

## License

This project is proprietary software.
