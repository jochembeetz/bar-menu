# Bar Menu

A modern bar menu management system built with Laravel and GraphQL, featuring a comprehensive API for managing products, categories, and ingredients.

## 🍹 Features

- **GraphQL API** - Modern, type-safe API using Lighthouse GraphQL
- **Product Reads** - Read operations for menu items
- **Category Organization** - Hierarchical menu categorization
- **Ingredient Tracking** - Detailed ingredient management with types (base, optional, add-on)
- **Pagination** - Efficient data loading with cursor-based pagination
- **Soft Deletes** - Safe data management with soft delete functionality
- **Testing** - Comprehensive test suite with PHPUnit
- **Docker Support** - Containerized development environment with Laravel Sail

## 🛠 Tech Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **API**: Regular Laravel
- **GraphQL**: GraphQL with Lighthouse
- **Database**: MySQL 8.0
- **Frontend**: Vite + Tailwind CSS
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint, PHPStan (Larastan)
- **Documentation**: L5-Swagger
- **Containerization**: Docker + Laravel Sail

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0 or Docker
- Git

## 🚀 Quick Start

### Option 1: Local Development

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd bar-menu
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database or stay on SQLite**
   Update your `.env` file with your database credentials or do nothing:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bar_menu
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Start the development server**
   ```bash
   # Start all services (Laravel, Vite, Queue, Logs)
   composer run dev
   
   # Or start just the API server
   composer run dev:api
   ```
8. **Access the application**
   - Application: http://localhost
   - GraphiQL: http://localhost/graphiql
   - Swagger: http://localhost/api/documentation
   - Database: localhost:3306

### Option 2: Docker Development

1. **Clone and setup environment**
   ```bash
   git clone <repository-url>
   cd bar-menu
   cp env.docker
   ```

2. **Start Docker containers**
   ```bash
   composer run dev:docker
   ```

3. **Install dependencies and setup database**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail npm install
   ./vendor/bin/sail artisan migrate
   ./vendor/bin/sail artisan db:seed
   ```

4. **Access the application**
   - Application: http://localhost
   - GraphiQL: http://localhost/graphiql
   - Swagger: http://localhost/api/documentation
   - Database: localhost:3306

## 📚 API Documentation

### GraphQL Endpoint
- **URL**: `/graphql`
- **Playground**: `/graphiql`

### Key Queries

#### List Categories
```graphql
query {
  categories(first: 10, page: 1) {
    data {
      id
      name
      slug
      description
    }
    paginatorInfo {
      currentPage
      lastPage
      total
    }
  }
}
```

#### List Products
```graphql
query {
  products(first: 10, page: 1) {
    data {
      id
      name
      slug
      description
      price_in_cents
      categories {
        id
        name
      }
      ingredients {
        id
        name
        type
      }
    }
    paginatorInfo {
      currentPage
      lastPage
      total
    }
  }
}
```

#### List Ingredients
```graphql
query {
  ingredients(first: 10, page: 1) {
    data {
      id
      name
      slug
      description
      type
    }
    paginatorInfo {
      currentPage
      lastPage
      total
    }
  }
}
```

#### Check examples in example.graphql

## 🗄 Database Schema

### Core Tables

- **categories** - Menu categories with sort order
- **products** - Menu items with pricing
- **ingredients** - Product ingredients
- **category_product** - Many-to-many relationship between categories and products
- **ingredient_product** - Many-to-many relationship between products and ingredients with type classification

### Relationships

- Categories ↔ Products (Many-to-Many with sort_order)
- Products ↔ Ingredients (Many-to-Many with type: base, optional, add-on)

## 🧪 Testing

### Run all tests

```bash
composer test
```

### Run specific test suites

```bash
# Feature tests
php artisan test --testsuite=Feature

# GraphQL tests
php artisan test tests/Feature/GraphQL/

# Consistency tests
php artisan test tests/Feature/Consistency/
```

### Code quality checks

```bash
composer run lint
```

## 📦 Available Scripts

### Development

- `composer dev` - Start all development services
- `composer dev:api` - Start API server only
- `composer dev:docker` - Start Docker environment

### Code Quality

- `composer lint` - Run Pint and PHPStan
- `composer test` - Run PHPUnit tests

### Documentation

- `composer run swagger` - Generate API documentation

## 🔧 Configuration

### GraphQL Configuration

- Schema: `graphql/schema.graphql`
- Resolvers: `app/GraphQL/Resolvers/`
- Types: `app/GraphQL/Types/`

## 🏗 Project Structure

```
bar-menu/
├── app/
│   ├── GraphQL/          # GraphQL resolvers and types
│   ├── Models/           # Eloquent models
│   ├── Services/         # Business logic services
│   └── Http/             # Controllers and middleware
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── graphql/
│   └── schema.graphql    # GraphQL schema definition
├── tests/
│   ├── Feature/          # Feature tests
│   └── Unit/             # Unit tests
├── resources/            # Frontend assets
└── routes/               # Route definitions
```
