# Ecom AI - Project Documentation

## Project Overview

**Ecom AI** is a sophisticated SaaS e-commerce product management platform that leverages AI to help sellers optimize their product listings through intelligent analysis, copywriting, and image generation.

### Key Capabilities
- **AI Product Analysis**: Deep analysis of products to extract features, benefits, customer problems, goals, emotions, and objections
- **AI Copywriting**: Generate marketing copy in multiple Arabic dialects and MSA using proven copywriting formulas (AIDA, BAB, PAS, FAB, 4Ps, QUEST)
- **AI Image Generation**: Create professional product images (product-only, lifestyle, UGC scenes, expert shots) tailored to specific countries
- **Multi-language Support**: Full Arabic (RTL) and English (LTR) interface with user preferences
- **RESTful API**: Complete API access with token-based authentication for automation and integrations

### Target Users
- E-commerce sellers managing product catalogs
- Marketing teams creating localized content for Middle Eastern markets
- Developers integrating AI product optimization into existing platforms via API

---

## Technology Stack

### Backend
- **Framework**: Laravel 12.x (PHP 8.2+)
- **Authentication**: Laravel Fortify (web) + Sanctum (API tokens)
- **UI Framework**: Livewire 3 (Volt) for reactive components
- **Component Library**: Flux UI 2.1 (free Livewire components)
- **Queue System**: Database-backed queues for async processing
- **Cache/Session**: Database driver

### Frontend
- **CSS Framework**: Tailwind CSS 4
- **Build Tool**: Vite 7
- **JavaScript**: Minimal (Livewire handles most reactivity)
- **RTL Support**: Full right-to-left layout for Arabic

### Database
- **Primary Database**: MySQL 8.0
- **ORM**: Eloquent

### AI Integration
- **Orchestration**: N8N (external workflow automation platform)
- **Integration Method**: Webhook-based asynchronous processing
- **N8N Base URL**: Configurable via `N8N_BASE_URL` environment variable


## Project Structure

```
/home/mostafa/Documents/ecom-ai/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CreateAdminToken.php      # Create admin API tokens
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/V1/                   # RESTful API controllers
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── ProductAnalysisController.php
│   │   │   │   ├── ProductCopyController.php
│   │   │   │   └── ProductImageController.php
│   │   │   ├── Auth/                      # Authentication controllers
│   │   │   └── ImageUploadController.php  # AJAX image uploads
│   │   ├── Middleware/
│   │   │   └── SetLocale.php              # Language detection
│   │   └── Resources/                     # API response transformers
│   ├── Livewire/                          # Full-page Livewire components
│   │   ├── Auth/                          # Login, Register, etc.
│   │   ├── Products/                      # Product management pages
│   │   │   ├── Index.php                  # Product listing
│   │   │   ├── Create.php                 # Create product
│   │   │   ├── Edit.php                   # Edit product
│   │   │   ├── Show.php                   # View product details
│   │   │   ├── Analysis.php               # AI product analysis
│   │   │   ├── Copywriting.php            # AI copywriting
│   │   │   └── Images.php                 # AI image generation
│   │   ├── Settings/                      # User settings pages
│   │   ├── Dashboard.php                  # Main dashboard
│   │   └── LanguageSwitcher.php           # Language switcher
│   ├── Models/                            # Eloquent models
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── ProductAnalysis.php
│   │   ├── ProductCopy.php
│   │   └── ProductImage.php
│   └── Providers/
│       └── AppServiceProvider.php         # Service container bindings
├── config/                                # Configuration files
│   ├── app.php                            # App settings, N8N URL
│   ├── fortify.php                        # Auth features
│   ├── sanctum.php                        # API token config
│   └── filesystems.php                    # Storage config
├── database/
│   ├── migrations/                        # Database schema
│   ├── factories/                         # Model factories
│   └── seeders/                           # Database seeders
├── docker/                                # Docker configuration files
│   ├── nginx/                             # Nginx configs
│   └── php/                               # PHP-FPM configs
├── public/                                # Public web root
│   ├── index.php                          # Entry point
│   └── storage/                           # Symlink to storage/app/public
├── resources/
│   ├── css/
│   │   └── app.css                        # Tailwind + Flux styles
│   ├── js/
│   │   └── app.js                         # Minimal JS + Livewire setup
│   ├── lang/                              # Translations
│   │   ├── ar/                            # Arabic translations
│   │   └── en/                            # English translations
│   └── views/
│       ├── components/                    # Blade components
│       │   └── layouts/                   # Layout components
│       └── livewire/                      # Livewire views (Volt SFC)
├── routes/
│   ├── web.php                            # Web routes (Livewire)
│   ├── api.php                            # API routes (v1)
│   ├── auth.php                           # Authentication routes
│   └── console.php                        # Artisan commands
├── storage/
│   ├── app/                               # Application storage
│   ├── framework/                         # Framework cache/sessions
│   └── logs/                              # Application logs
├── tests/                                 # Test suites
├── .env                                   # Environment configuration
├── composer.json                          # PHP dependencies
├── package.json                           # Node dependencies
├── docker-compose-dev.yml                 # Docker development setup
├── docker-compose-production.yml          # Docker production setup
├── Dockerfile                             # Docker image definition
├── ADMIN_TOKEN_GUIDE.md                   # Admin token documentation
├── ARABIC_TRANSLATION_GUIDE.md            # Translation documentation
└── README.docker.md                       # Docker setup guide
```

### Key Design Patterns

- **MVC Architecture**: Controllers handle requests, Models represent data, Views render UI
- **Resource-Based API**: RESTful API organized around resources (Products, Analyses, Copies, Images)
- **Authorization via Ownership**: All resources belong to users; access controlled via `user_id` foreign keys
- **Webhook Integration**: Asynchronous AI processing via N8N webhooks
- **Multi-tenancy**: Soft multi-tenancy where users only access their own resources (unless admin)


### API Token System

**Implementation**: Laravel Sanctum

**Token Types**:

1. **Regular User Tokens** (`user:access` ability):
   - Access only own resources
   - Created via Settings UI or API
   - Standard CRUD operations

2. **Admin Tokens** (`admin:*` ability):
   - Full access to all users' resources
   - Created via Artisan command only
   - Bypass ownership checks

**Creating Admin Tokens**:
```bash
php artisan token:create-admin --name="My Admin Token" --user=admin@example.com
```

**Authorization Pattern**:
```php
// In User model
public function canAccessResource($resource): bool
{
    // Admin tokens can access everything
    if (Auth::user()->tokenCan('admin:*')) {
        return true;
    }

    // Regular users can only access their own resources
    return $resource->user_id === $this->id;
}
```

---

## AI Integration

### N8N Webhook Architecture

**Base URL**: Configured in `config/app.php:55` via `N8N_BASE_URL` environment variable

**Default**: `https://n8n.srv871797.hstgr.cloud/webhook/`

### Webhook Endpoints

#### 1. Product Analysis Generation

**Endpoint**: `{N8N_BASE_URL}/generate-product-analysis`

**Request Payload**:
```json
{
  "product_id": 123,
  "name": "Product Name",
  "description_user": "User description",
  "description_ai": "AI description",
  "type": "physical",
  "main_image_url": "https://...",
  "callback_url": "https://yourapp.com/api/v1/products/123/analysis"
}
```

**Response Flow**:
1. N8N receives request
2. Processes with AI (GPT-4, Claude, etc.)
3. Calls back to your API with analysis data
4. Updates `ProductAnalysis` record

**Callback Payload**:
```json
{
  "product_id": 123,
  "core_function_and_use": "...",
  "features": ["feature 1", "feature 2"],
  "benefits": ["benefit 1", "benefit 2"],
  "problems": ["problem 1"],
  "goals": ["goal 1"],
  "emotions": ["emotion 1"],
  "objections": ["objection 1"],
  "faqs": [
    {"question": "...", "answer": "..."}
  ]
}

**Version**: 1.0
**Last Updated**: October 2025
