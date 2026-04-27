# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 e-commerce backend application with:
- **Admin Panel**: Filament 5 for all CRUD and management operations
- **API**: Public RESTful API (no authentication required)
- **Database**: MySQL with hierarchical categories, product variants, and inventory tracking

Note: Vue 3 + Inertia.js dependencies are present from the starter kit but are not actively used. Filament handles all admin interface needs.

## Development Commands

```bash
# Initial setup (first time only)
composer setup

# Development (runs server, queue, and Vite)
composer dev
composer dev:ssr  # With SSR enabled

# Code quality - Backend
composer lint        # Laravel Pint (PHP formatter)
composer lint:check  # Check Pint without fixing

# Code quality - Frontend
npm run lint         # ESLint
npm run format       # Prettier
npm run types:check  # TypeScript type checking

# Testing
composer test        # Run PHPUnit tests (includes lint check)
composer ci:check    # Full CI check (lint, format, types, test)
php artisan test     # Run tests directly

# Single test
php artisan test --filter testMethodName

# Frontend build
npm run build        # Production build
npm run dev          # Vite dev server
npm run build:ssr    # SSR build

# Database
php artisan migrate             # Run migrations
php artisan migrate:fresh       # Fresh migration with data reset
php artisan make:migration      # Create new migration
```

## Architecture

### Technology Stack

- **Laravel 12** - Backend framework
- **Filament 5** - Full admin panel with forms, tables, and relation managers (accessible at `/admin`)
- **MySQL** - Database

### Database Architecture

**Core Relationships:**
- **Categories**: Self-referencing hierarchy (`parent_id` → `id`)
- **Products** → belongsTo → **Category** and **Brand**
- **Products** → hasMany → **ProductVariant**
- **ProductVariant** → belongsTo → **Product**
- **ProductVariant** → many-to-many → **AttributeValue** (for sizes, colors, etc.)
- **Orders** → hasMany → **OrderItems**
- **Images**: Polymorphic (`imageable_type`, `imageable_id`) attached to Categories, Products, Brands, and Variants

**Inventory System:**
- Stock quantity computed from `ProductInventoryMovement` records
- Movement types: Purchase, Sale, Return, Damaged (enum-based)
- Automatic stock calculation in `ProductVariant::getStockAttribute()`

### API Structure

Routes defined in `routes/api.php` with versioning (`/api/v1/...`).

**Endpoints:**
- `/api/v1/categories` - Index and show (read-only)
- `/api/v1/brands` - Index and show (read-only)
- `/api/v1/products` - Index and show (read-only)
- `/api/v1/orders` - Full CRUD
- `/api/v1/home/*` - Latest products, categories, brands endpoints

**Response Pattern:**
- Controllers return `response()->json()` with manually built arrays
- Image URLs generated using `Storage::url($path)`
- Use `collect()->map()` to transform collections for consistent JSON output
- For paginated responses, include `data`, `links`, and `meta` keys matching Laravel paginator format

### Filament Admin Panel

**Resource Organization Pattern:**
- Resources located in `app/Filament/Resources/{ModelName}/`
- Each resource follows a structured pattern:
  - `{Model}Resource.php` - Main resource class
  - `Schemas/{Model}Form.php` - Form schema with validation
  - `Schemas/{Model}Infolist.php` - View/display schema
  - `Tables/{Model}sTable.php` - List table configuration
  - `Pages/` - List, Create, Edit, View pages

**Key Resources:**
- CategoryResource (hierarchical with parent/children)
- ProductResource (with variants repeater and nested attributes)
- OrderResource (with order status management)
- BrandResource
- AttributeTypeResource (with ValuesRelationManager)

### Key Design Patterns

1. **Polymorphic Images**: Single `Image` model attaches to any entity via `imageable()` morphTo relationship
2. **Enum-Based Logic**: PHP enums for `OrderStatus`, `MovementType`, `PaymentStatus`, `PaymentMethod`
   - Define business logic in enums, not models
   - Enums include `label()` and `color()` methods for Filament UI display
   - Example: `OrderStatus::Pending->label()` returns "Pending", `->color()` returns "warning"
3. **Computed Attributes**: Stock quantity computed from `ProductInventoryMovement` records, not stored directly
   - Formula: Purchase + Return - Sale - Damaged
   - Effective price falls back to product's `base_price` when variant price is null
4. **Hierarchical Categories**: Recursive `parent()` and `children()` relationships
5. **Filament Form Pattern**: Forms use `Tabs` for organization, `Repeater` for nested collections (variants, attributes), `Section` for grouping
6. **Manual JSON Responses**: Controllers build arrays manually and return `response()->json()`, giving explicit control over API output structure

## Important Conventions

### Migrations
- Always use `->unsignedBigInteger()` for foreign keys
- Define foreign key constraints explicitly (`->constrained()->cascadeOnDelete()`)
- Use `->default()` or `->nullable()` as appropriate

### Models
- Use `$casts` property for type casting (enums, dates, etc.)
- Define relationships with both model method and return type hint
- Clean up related data in `static::deleting()` event listener when using cascade deletes manually

### API Controllers
- Return `response()->json($data)` with manually built arrays
- For single items: `response()->json(['id' => $item->id, 'name' => $item->name, ...])`
- For collections: `response()->json(['data' => collect($items)->map(fn ($item) => [...])])`
- For paginated responses: include `data`, `links`, and `meta` keys
- Use proper status codes (200, 201, 404, etc.)

### Testing
- Write feature tests for API endpoints
- Write unit tests for model logic
- Run `composer test` before committing (includes linting)

## Common Tasks

### Adding a New API Endpoint
1. Add route in `routes/api.php` with proper prefix and middleware
2. Create/update controller in `app/Http/Controllers/Api/`
3. Build response arrays manually and return `response()->json()`
4. Add validation rules in FormRequest or controller
5. Test with `php artisan test --filter endpointName`

### Adding New Model
1. Create migration: `php artisan make:migration`
2. Create model: `php artisan make:model`
3. Define relationships and casts
4. Create Filament resource for admin
5. Create API controller if needed (with manual `response()->json()` responses)

### Modifying Database
1. Create migration: `php artisan make:migration alter_table_name --table=table_name`
2. Run migration: `php artisan migrate`
3. Update model `$fillable` and `$casts` if adding columns

### Creating New Filament Resource
1. Create resource: `php artisan make:filament-resource {Model}`
2. Organize into subdirectories: `Schemas/`, `Tables/`, `Pages/`
3. Extract form schema to `Schemas/{Model}Form.php`
4. Extract table config to `Tables/{Model}sTable.php`
5. Add relation managers if needed for nested data

## Configuration Files

- `routes/api.php` - REST API endpoints (versioned as `/api/v1/...`)
- `routes/web.php` - Basic web routes (Filament handles `/admin` automatically)
- `config/filament.php` - Filament panel configuration

## Testing Notes

- Tests use SQLite in-memory database (`:memory:`)
- Run `composer test` before committing (includes linting via Pint)
- Write feature tests for API endpoints (`tests/Feature/`)
- Write unit tests for model logic (`tests/Unit/`)
