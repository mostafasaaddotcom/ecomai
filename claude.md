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
- **Component Library**: Flux UI 2.1 (Premium Livewire components)
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

### DevOps
- **Containerization**: Docker (development & production configurations)
- **Web Server**: Nginx + PHP-FPM
- **Background Processing**: Queue worker + scheduler containers

---

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

---

## Database Schema

### Users Table
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('phone')->nullable();
    $table->decimal('credit', 10, 2)->default(0);
    $table->string('locale', 5)->default('en');  // Language preference
    $table->rememberToken();

    // Two-factor authentication
    $table->text('two_factor_secret')->nullable();
    $table->text('two_factor_recovery_codes')->nullable();
    $table->timestamp('two_factor_confirmed_at')->nullable();

    $table->timestamps();
});
```

**Relationships**:
- `hasMany(Product::class)`
- `hasMany(PersonalAccessToken::class)` (Sanctum tokens)

### Products Table
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description_user')->nullable();  // User-provided description
    $table->text('description_ai')->nullable();    // AI-generated description
    $table->string('main_image_url')->nullable();
    $table->enum('type', ['physical', 'digital'])->default('physical');
    $table->timestamps();
});
```

**Relationships**:
- `belongsTo(User::class)`
- `hasOne(ProductAnalysis::class)`
- `hasMany(ProductCopy::class)`
- `hasMany(ProductImage::class)`

### Product Analyses Table
```php
Schema::create('product_analyses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->text('core_function_and_use')->nullable();
    $table->json('features')->nullable();          // Array of features
    $table->json('benefits')->nullable();          // Array of benefits
    $table->json('problems')->nullable();          // Customer problems solved
    $table->json('goals')->nullable();             // Customer goals
    $table->json('emotions')->nullable();          // Customer emotions
    $table->json('objections')->nullable();        // Customer objections
    $table->json('faqs')->nullable();              // [{question: "", answer: ""}]
    $table->timestamps();
});
```

**Relationships**:
- `belongsTo(User::class)`
- `belongsTo(Product::class)`

### Product Copies Table
```php
Schema::create('product_copies', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->string('angle')->nullable();                    // Marketing angle
    $table->enum('type', ['ugc', 'expert', 'background_voice']);
    $table->string('formula');                              // AIDA, BAB, PAS, etc.
    $table->string('language');                             // egyptian, saudi, etc.
    $table->string('tone')->nullable();                     // Tone of voice
    $table->text('content');                                // Generated copy text
    $table->string('voice_url_link')->nullable();           // Audio file URL
    $table->timestamps();
});
```

**Relationships**:
- `belongsTo(User::class)`
- `belongsTo(Product::class)`

### Product Images Table
```php
Schema::create('product_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->enum('type', ['product_only', 'lifestyle', 'ugc_scene', 'expert', 'other']);
    $table->text('prompt')->nullable();                     // AI generation prompt
    $table->string('image_url')->nullable();
    $table->string('aspect_ratio')->default('9:16');
    $table->boolean('is_ai_generated')->default(false);
    $table->string('reference_id')->nullable()->unique();   // For webhook tracking
    $table->enum('status', ['prompt_generated', 'image_generating', 'completed', 'failed'])
        ->default('completed');
    $table->timestamps();
});
```

**Relationships**:
- `belongsTo(User::class)`
- `belongsTo(Product::class)`

### Personal Access Tokens Table
```php
Schema::create('personal_access_tokens', function (Blueprint $table) {
    $table->id();
    $table->morphs('tokenable');                           // User relationship
    $table->string('name');
    $table->string('token', 64)->unique();
    $table->text('abilities')->nullable();                 // ["user:access"] or ["admin:*"]
    $table->timestamp('last_used_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->timestamps();
});
```

### Migration Order
Execute migrations in this order to maintain foreign key integrity:
1. `create_users_table`
2. `create_cache_table`
3. `create_jobs_table`
4. `create_products_table`
5. `create_personal_access_tokens_table`
6. `create_product_analyses_table`
7. `create_product_copies_table`
8. `create_product_images_table`
9. `add_two_factor_columns_to_users_table`
10. `add_locale_to_users_table`

---

## Core Features

### 1. Product Management

**Location**: `app/Livewire/Products/`

**Features**:
- Create products (physical/digital)
- Upload main product image
- Provide user description and/or AI-generated description
- Edit product details
- View product with all associated AI data
- Delete products (cascades to analyses, copies, images)

**Key Files**:
- `Index.php:15` - Product listing with search/filter
- `Create.php:23` - Product creation form
- `Edit.php:31` - Product editing
- `Show.php:42` - Product detail view

### 2. AI Product Analysis

**Location**: `app/Livewire/Products/Analysis.php`

**Workflow**:
1. User navigates to product analysis page
2. Clicks "Generate Analysis" button
3. System sends webhook to N8N: `{N8N_BASE_URL}/generate-product-analysis`
4. N8N processes with AI and returns data via webhook
5. `ProductAnalysis` model updated with results
6. User can edit any analysis field
7. User can regenerate analysis anytime

**Analysis Components**:
- Core function and use (text)
- Features (array)
- Benefits (array)
- Customer problems solved (array)
- Customer goals (array)
- Customer emotions (array)
- Customer objections (array)
- FAQs (array of {question, answer} objects)

**Key Endpoints**:
- Generate: `POST {N8N_BASE_URL}/generate-product-analysis`
- View: `GET /api/v1/products/{id}/analysis`
- Update: `PUT /api/v1/product-analyses/{id}`

### 3. AI Copywriting

**Location**: `app/Livewire/Products/Copywriting.php`

**Supported Languages/Dialects**:
- `egyptian` - Egyptian Arabic
- `saudi` - Saudi Arabian Arabic
- `gulf` - Gulf Arabic
- `levantine` - Levantine Arabic
- `moroccan` - Moroccan Arabic
- `msa` - Modern Standard Arabic

**Copywriting Formulas**:
- **AIDA**: Attention, Interest, Desire, Action
- **BAB**: Before, After, Bridge
- **PAS**: Problem, Agitate, Solution
- **FAB**: Features, Advantages, Benefits
- **4Ps**: Picture, Promise, Prove, Push
- **QUEST**: Qualify, Understand, Educate, Stimulate, Transition

**Copy Types**:
- **UGC**: User-Generated Content style
- **Expert**: Expert/authority voice
- **Background Voice**: Narrator/background voice

**Workflow**:
1. User selects language, formulas, types, and quantities (1-10 each)
2. System sends request to N8N: `{N8N_BASE_URL}/generate-product-copies`
3. N8N generates multiple copy variations
4. System creates `ProductCopy` records via API callback
5. User can edit content, delete copies, or add voice URL links

**Key Fields**:
- `content` - Generated marketing text
- `voice_url_link` - Optional URL to audio recording
- `angle` - Marketing angle used
- `tone` - Tone of voice

### 4. AI Image Generation

**Location**: `app/Livewire/Products/Images.php`

**Image Types**:
- **Product Only**: Clean product shots
- **Lifestyle**: Product in lifestyle context
- **UGC Scene**: User-generated content style
- **Expert**: Professional/expert demonstration
- **Other**: Manual uploads (non-AI)

**Supported Countries**:
- Egypt
- Saudi Arabia
- Algeria
- Morocco
- Jordan

**Workflow**:
1. User selects country, image types, aspect ratio, prompt language
2. System creates `ProductImage` records with status `prompt_generated`
3. System sends request to N8N: `{N8N_BASE_URL}/generate-product-images`
4. N8N generates prompts, updates status to `image_generating`
5. N8N generates images, updates via webhook with image URLs
6. Final status: `completed` or `failed`

**Regeneration**:
1. User clicks regenerate on existing image
2. Can optionally update the prompt
3. System sends to N8N: `{N8N_BASE_URL}/regenerate-product-image`
4. Image updated via webhook callback

**Key Features**:
- Download images
- Custom prompts in Arabic or English
- Multiple aspect ratios (9:16 default)
- Status tracking throughout generation
- Reference ID for webhook correlation

### 5. Multi-language Support

**Languages**: Arabic (RTL) + English (LTR)

**Implementation**:
- Middleware: `app/Http/Middleware/SetLocale.php:18`
- User preference stored in `users.locale` column
- Translation files: `resources/lang/{ar,en}/`
- RTL support via Tailwind logical properties (ms-, me-, start-, end-)

**Translation Files**:
- `auth.php` - Authentication strings
- `messages.php` - General UI messages
- `products.php` - Product-related strings
- `settings.php` - Settings page strings
- `validation.php` - Form validation messages
- `passwords.php` - Password reset strings

**Switching Languages**:
- Component: `app/Livewire/LanguageSwitcher.php:12`
- Updates user preference in database
- Persists across sessions

### 6. API Token System

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
```

#### 2. Copywriting Generation

**Endpoint**: `{N8N_BASE_URL}/generate-product-copies`

**Request Payload**:
```json
{
  "product_id": 123,
  "product_name": "...",
  "product_description": "...",
  "analysis": { /* ProductAnalysis data */ },
  "language": "egyptian",
  "formulas": ["AIDA", "BAB", "PAS"],
  "types": ["ugc", "expert"],
  "quantity_per_type": 3,
  "callback_url": "https://yourapp.com/api/v1/products/123/copies"
}
```

**Response Flow**:
1. N8N receives request
2. Generates multiple copy variations (formulas × types × quantity)
3. Creates `ProductCopy` records via API callback
4. Each copy created separately via POST to callback URL

**Callback Payload** (per copy):
```json
{
  "product_id": 123,
  "type": "ugc",
  "formula": "AIDA",
  "language": "egyptian",
  "content": "Generated marketing copy...",
  "angle": "Marketing angle used"
}
```

#### 3. Image Generation

**Endpoint**: `{N8N_BASE_URL}/generate-product-images`

**Request Payload**:
```json
{
  "product_id": 123,
  "product_name": "...",
  "product_description": "...",
  "analysis": { /* ProductAnalysis data */ },
  "country": "egypt",
  "types": ["product_only", "lifestyle", "ugc_scene"],
  "aspect_ratio": "9:16",
  "prompt_language": "english",
  "callback_url": "https://yourapp.com/api/v1/product-images/webhook",
  "reference_ids": [
    {"type": "product_only", "reference_id": "uuid-1"},
    {"type": "lifestyle", "reference_id": "uuid-2"}
  ]
}
```

**Response Flow** (multi-stage):

**Stage 1 - Prompt Generation**:
```json
{
  "reference_id": "uuid-1",
  "status": "prompt_generated",
  "prompt": "A professional photo of..."
}
```

**Stage 2 - Image Generating**:
```json
{
  "reference_id": "uuid-1",
  "status": "image_generating"
}
```

**Stage 3 - Completed**:
```json
{
  "reference_id": "uuid-1",
  "status": "completed",
  "image_url": "https://storage.../image.png"
}
```

**Or Failed**:
```json
{
  "reference_id": "uuid-1",
  "status": "failed",
  "error": "Error message"
}
```

#### 4. Image Regeneration

**Endpoint**: `{N8N_BASE_URL}/regenerate-product-image`

**Request Payload**:
```json
{
  "reference_id": "uuid-1",
  "product_name": "...",
  "type": "lifestyle",
  "country": "egypt",
  "prompt": "Updated custom prompt...",
  "aspect_ratio": "9:16",
  "callback_url": "https://yourapp.com/api/v1/product-images/webhook"
}
```

**Response**: Same multi-stage webhook flow as image generation

### Webhook Security

**Incoming Webhooks**: Currently no authentication (consider adding HMAC signature verification)

**Outgoing Requests**: Add custom headers if needed:
```php
Http::post($n8nUrl, [
    'headers' => [
        'X-App-Signature' => hash_hmac('sha256', $payload, config('app.key'))
    ]
]);
```

---

## API Reference

### Authentication

**Method**: Bearer token (Laravel Sanctum)

**Header**:
```
Authorization: Bearer {token}
```

**Token Types**:
- Regular: `["user:access"]` - Access own resources only
- Admin: `["admin:*"]` - Access all resources

### Base URL

`{APP_URL}/api/v1`

### Endpoints

#### Products

**Get Product**
```
GET /products/{product}
```
**Authorization**: Owner or admin
**Response**: Product with relationships (analysis, copies, images)

**Update Product AI Description**
```
PUT /products/{product}
```
**Authorization**: Owner or admin
**Body**:
```json
{
  "description_ai": "AI-generated description"
}
```

#### Product Analysis

**Get Analysis**
```
GET /products/{product}/analysis
```
**Authorization**: Owner or admin
**Response**: ProductAnalysis object

**Update Analysis**
```
PUT /product-analyses/{productAnalysis}
```
**Authorization**: Owner or admin
**Body**:
```json
{
  "core_function_and_use": "...",
  "features": ["feature 1", "feature 2"],
  "benefits": ["benefit 1"],
  "problems": ["problem 1"],
  "goals": ["goal 1"],
  "emotions": ["emotion 1"],
  "objections": ["objection 1"],
  "faqs": [{"question": "...", "answer": "..."}]
}
```

#### Product Copies

**List Copies**
```
GET /products/{product}/copies
```
**Authorization**: Owner or admin
**Response**: Array of ProductCopy objects

**Create Copy**
```
POST /products/{product}/copies
```
**Authorization**: Owner or admin
**Body**:
```json
{
  "type": "ugc",
  "formula": "AIDA",
  "language": "egyptian",
  "content": "Marketing copy text",
  "angle": "Marketing angle",
  "tone": "Conversational",
  "voice_url_link": "https://..."
}
```

**Update Copy**
```
PUT /product-copies/{productCopy}
```
**Authorization**: Owner or admin
**Body**: Same as create (all fields optional)

**Delete Copy**
```
DELETE /product-copies/{productCopy}
```
**Authorization**: Owner or admin

#### Product Images

**List Images**
```
GET /products/{product}/images
```
**Authorization**: Owner or admin
**Response**: Array of ProductImage objects

**Create Image**
```
POST /products/{product}/images
```
**Authorization**: Owner or admin
**Body**:
```json
{
  "type": "lifestyle",
  "prompt": "Custom prompt",
  "image_url": "https://...",
  "aspect_ratio": "9:16",
  "is_ai_generated": true,
  "reference_id": "uuid",
  "status": "completed"
}
```

**Get Image by Reference ID**
```
GET /product-images/reference/{referenceId}
```
**Authorization**: None (used by N8N webhooks)
**Response**: ProductImage object

**Update Image**
```
PUT /product-images/{productImage}
```
**Authorization**: Owner or admin
**Body**: Same as create (all fields optional)

**Webhook Callback** (N8N → App)
```
POST /product-images/webhook
```
**Authorization**: None
**Body**:
```json
{
  "reference_id": "uuid",
  "status": "completed",
  "prompt": "Generated prompt",
  "image_url": "https://..."
}
```

### Error Responses

**401 Unauthorized**:
```json
{
  "message": "Unauthenticated."
}
```

**403 Forbidden**:
```json
{
  "message": "This action is unauthorized."
}
```

**404 Not Found**:
```json
{
  "message": "Resource not found."
}
```

**422 Unprocessable Entity**:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

---

## Development Workflows

### Adding a New Feature

1. **Plan the Feature**
   - Identify required models, migrations, controllers
   - Determine if Livewire component or API endpoint needed
   - Consider authorization requirements

2. **Create Migration**
   ```bash
   php artisan make:migration create_feature_table
   ```
   Edit migration file, then run:
   ```bash
   php artisan migrate
   ```

3. **Create Model**
   ```bash
   php artisan make:model Feature
   ```
   Add relationships, fillable fields, casts

4. **Create Livewire Component** (for web UI)
   ```bash
   php artisan make:livewire Features/Index
   ```
   Use Volt SFC style (see `resources/views/livewire/` examples)

5. **Create API Controller** (for API access)
   ```bash
   php artisan make:controller Api/V1/FeatureController
   ```
   Add resource methods, authorization checks

6. **Add Routes**
   - Web: `routes/web.php`
   - API: `routes/api.php` (v1 prefix)

7. **Add Translations**
   - Create keys in `resources/lang/ar/features.php`
   - Create keys in `resources/lang/en/features.php`

8. **Test**
   - Manual testing in browser
   - API testing with Postman/curl
   - Consider writing tests in `tests/Feature/`

### Creating New API Endpoints

**Example: Add a "Duplicate Product" endpoint**

1. **Add Route** (`routes/api.php`):
   ```php
   Route::post('/products/{product}/duplicate', [ProductController::class, 'duplicate'])
       ->middleware('auth:sanctum');
   ```

2. **Add Controller Method** (`app/Http/Controllers/Api/V1/ProductController.php`):
   ```php
   public function duplicate(Product $product)
   {
       // Authorization check
       if (!Auth::user()->canAccessResource($product)) {
           return response()->json(['message' => 'Unauthorized'], 403);
       }

       // Duplicate logic
       $newProduct = $product->replicate();
       $newProduct->name = $product->name . ' (Copy)';
       $newProduct->user_id = Auth::id();
       $newProduct->save();

       // Copy relationships if needed
       if ($product->analysis) {
           $newAnalysis = $product->analysis->replicate();
           $newAnalysis->product_id = $newProduct->id;
           $newAnalysis->save();
       }

       return new ProductResource($newProduct->load('analysis'));
   }
   ```

3. **Test**:
   ```bash
   curl -X POST https://yourapp.com/api/v1/products/123/duplicate \
     -H "Authorization: Bearer {token}" \
     -H "Accept: application/json"
   ```

### Adding AI Workflows

**Example: Add a "Product Suggestions" AI feature**

1. **Create N8N Workflow**
   - Create workflow in N8N dashboard
   - Add webhook trigger node
   - Add AI processing nodes (OpenAI, Claude, etc.)
   - Add HTTP request node to callback

2. **Add Webhook Endpoint to Config** (`config/app.php`):
   ```php
   'n8n' => [
       'base_url' => env('N8N_BASE_URL'),
       'endpoints' => [
           'product_analysis' => 'generate-product-analysis',
           'product_copies' => 'generate-product-copies',
           'product_images' => 'generate-product-images',
           'product_suggestions' => 'generate-product-suggestions', // New
       ],
   ],
   ```

3. **Create Service Method** (e.g., in `ProductService`):
   ```php
   public function generateSuggestions(Product $product)
   {
       $url = config('app.n8n.base_url') .
              config('app.n8n.endpoints.product_suggestions');

       $response = Http::post($url, [
           'product_id' => $product->id,
           'name' => $product->name,
           'description' => $product->description_user ?? $product->description_ai,
           'callback_url' => route('api.v1.products.suggestions.webhook'),
       ]);

       return $response->json();
   }
   ```

4. **Add Livewire Component** or **API Endpoint**
   - Trigger workflow from UI or API
   - Display loading state
   - Handle webhook callback
   - Update UI when complete

### Working with Livewire Components

**Volt SFC Style** (Single File Component):

```php
<?php
// resources/views/livewire/features/index.blade.php

use Livewire\Volt\Component;
use App\Models\Feature;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function with(): array
    {
        return [
            'features' => Feature::query()
                ->where('user_id', Auth::id())
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(10),
        ];
    }

    public function delete($featureId)
    {
        $feature = Feature::findOrFail($featureId);

        if ($feature->user_id !== Auth::id()) {
            abort(403);
        }

        $feature->delete();

        $this->dispatch('feature-deleted');
    }
}
?>

<div>
    <flux:heading size="xl">{{ __('features.title') }}</flux:heading>

    <flux:input
        wire:model.live="search"
        placeholder="{{ __('features.search') }}"
    />

    <div class="space-y-4">
        @foreach($features as $feature)
            <flux:card>
                <h3>{{ $feature->name }}</h3>
                <flux:button wire:click="delete({{ $feature->id }})">
                    {{ __('common.delete') }}
                </flux:button>
            </flux:card>
        @endforeach
    </div>

    {{ $features->links() }}
</div>
```

**Key Concepts**:
- Use `wire:model.live` for real-time input binding
- Use `wire:click` for method calls
- Use `$this->dispatch()` for events
- Use `with()` method to pass data to view
- Authorization in methods (check `user_id`)

### Adding Translations

**Pattern**: Always add translations in both languages simultaneously

**Example**: Add "features" translations

1. **Arabic** (`resources/lang/ar/features.php`):
   ```php
   <?php
   return [
       'title' => 'المميزات',
       'search' => 'بحث...',
       'create' => 'إنشاء ميزة',
       'edit' => 'تعديل الميزة',
       'delete_confirm' => 'هل أنت متأكد من حذف هذه الميزة؟',
   ];
   ```

2. **English** (`resources/lang/en/features.php`):
   ```php
   <?php
   return [
       'title' => 'Features',
       'search' => 'Search...',
       'create' => 'Create Feature',
       'edit' => 'Edit Feature',
       'delete_confirm' => 'Are you sure you want to delete this feature?',
   ];
   ```

3. **Usage in Blade**:
   ```blade
   {{ __('features.title') }}
   ```

4. **Usage in Livewire/PHP**:
   ```php
   $message = __('features.delete_confirm');
   ```

**Translation Key Structure**:
- Use dot notation: `file.key`
- Group related keys in same file
- Use descriptive key names
- Maintain consistent structure across languages

### Database Migrations

**Best Practices**:

1. **Always use foreign keys with cascade**:
   ```php
   $table->foreignId('user_id')->constrained()->cascadeOnDelete();
   ```

2. **Use appropriate column types**:
   ```php
   $table->enum('status', ['pending', 'completed', 'failed']);
   $table->json('metadata');
   $table->text('long_content');
   $table->decimal('price', 10, 2);
   ```

3. **Add indexes for frequently queried columns**:
   ```php
   $table->index('user_id');
   $table->index(['user_id', 'status']);
   ```

4. **Use nullable() for optional fields**:
   ```php
   $table->string('optional_field')->nullable();
   ```

5. **Always include timestamps**:
   ```php
   $table->timestamps();
   ```

**Running Migrations**:
```bash
# Fresh install
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Rollback all & re-run
php artisan migrate:fresh

# With seeding
php artisan migrate:fresh --seed
```

---

## Common Tasks & Examples

### Creating Admin Tokens

**Using Artisan Command**:
```bash
# Basic usage (interactive)
php artisan token:create-admin

# With options
php artisan token:create-admin \
  --name="Production API Token" \
  --user=admin@example.com \
  --expires=2025-12-31

# With 90-day expiration
php artisan token:create-admin \
  --name="Temp Admin Token" \
  --expires="+90 days"
```

**Command Location**: `app/Console/Commands/CreateAdminToken.php:15`

**Token Output**:
```
Admin token created successfully!

Token: 1|AbCdEfGhIjKlMnOpQrStUvWxYz1234567890
Name: Production API Token
User: admin@example.com
Abilities: admin:*
Expires: 2025-12-31 23:59:59

IMPORTANT: Save this token now. You won't be able to see it again!
```

### Testing API Endpoints

**Using curl**:

```bash
# Get product
curl -X GET https://yourapp.com/api/v1/products/123 \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"

# Update product AI description
curl -X PUT https://yourapp.com/api/v1/products/123 \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"description_ai": "New AI description"}'

# Create product copy
curl -X POST https://yourapp.com/api/v1/products/123/copies \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "ugc",
    "formula": "AIDA",
    "language": "egyptian",
    "content": "Your marketing copy here"
  }'

# List product images
curl -X GET https://yourapp.com/api/v1/products/123/images \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

**Using Postman**:
1. Create new collection
2. Add bearer token in Authorization tab
3. Set base URL variable: `{{base_url}}/api/v1`
4. Create requests for each endpoint
5. Use environment variables for token

### Generating Product Analysis

**Programmatically** (from Livewire/Controller):

```php
use Illuminate\Support\Facades\Http;

public function generateAnalysis(Product $product)
{
    $url = config('app.n8n.base_url') . 'generate-product-analysis';

    $response = Http::post($url, [
        'product_id' => $product->id,
        'name' => $product->name,
        'description_user' => $product->description_user,
        'description_ai' => $product->description_ai,
        'type' => $product->type,
        'main_image_url' => $product->main_image_url,
        'callback_url' => route('api.v1.products.analysis.show', $product),
    ]);

    if ($response->successful()) {
        return ['status' => 'processing'];
    }

    return ['status' => 'error', 'message' => $response->body()];
}
```

**Handling Webhook Callback** (API Controller):

```php
public function updateFromWebhook(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'core_function_and_use' => 'nullable|string',
        'features' => 'nullable|array',
        'benefits' => 'nullable|array',
        'problems' => 'nullable|array',
        'goals' => 'nullable|array',
        'emotions' => 'nullable|array',
        'objections' => 'nullable|array',
        'faqs' => 'nullable|array',
    ]);

    $analysis = ProductAnalysis::updateOrCreate(
        ['product_id' => $validated['product_id']],
        $validated
    );

    return response()->json($analysis, 200);
}
```

### Image Upload Implementation

**Controller** (`app/Http/Controllers/ImageUploadController.php:22`):

```php
public function uploadMainImage(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $path = $request->file('image')->store('products', 'public');
    $url = Storage::url($path);

    return response()->json([
        'success' => true,
        'url' => $url
    ]);
}
```

**Frontend (JavaScript)** (`resources/views/livewire/products/create.blade.php`):

```javascript
function uploadImage(file) {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('/upload-main-image', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update Livewire component
            @this.set('mainImageUrl', data.url);
        }
    });
}
```

**Note**: Uses AJAX instead of Livewire's built-in file upload for better UX

### Queue Job Management

**Running Queue Worker**:
```bash
# Development
php artisan queue:work --tries=3

# Production (via Supervisor)
php artisan queue:work --tries=3 --timeout=90
```

**Checking Failed Jobs**:
```bash
# List failed jobs
php artisan queue:failed

# Retry specific job
php artisan queue:retry {id}

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

**Creating Queued Jobs**:
```bash
php artisan make:job ProcessProductImage
```

```php
<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessProductImage implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Product $product
    ) {}

    public function handle()
    {
        // Process image
    }
}
```

**Dispatching Jobs**:
```php
ProcessProductImage::dispatch($product);

// With delay
ProcessProductImage::dispatch($product)->delay(now()->addMinutes(5));

// On specific queue
ProcessProductImage::dispatch($product)->onQueue('images');
```

---

## Configuration & Environment

### Required Environment Variables

```env
# Application
APP_NAME="Ecom Ai"
APP_ENV=local                    # local, production
APP_KEY=base64:...               # Generate with: php artisan key:generate
APP_DEBUG=true                   # false in production
APP_URL=https://yourapp.com

# AI Integration
N8N_BASE_URL="https://n8n.srv871797.hstgr.cloud/webhook/"

# Localization
APP_LOCALE=en                    # en or ar (default language)
APP_FALLBACK_LOCALE=en
APP_TIMEZONE=UTC

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecom_ai
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Session & Cache
SESSION_DRIVER=database          # database, redis, file
SESSION_LIFETIME=120
CACHE_STORE=database             # database, redis, file

# Queue
QUEUE_CONNECTION=database        # database, redis, sync

# Mail
MAIL_MAILER=smtp                 # smtp, log, mailgun, etc.
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@ecom-ai.com
MAIL_FROM_NAME="${APP_NAME}"

# Fortify
FORTIFY_HOME=/dashboard          # Redirect after login
```

### Key Configuration Files

#### `config/app.php`
```php
'locale' => env('APP_LOCALE', 'en'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

// N8N Integration
'n8n' => [
    'base_url' => env('N8N_BASE_URL', 'https://n8n.srv871797.hstgr.cloud/webhook/'),
],
```

#### `config/fortify.php`
```php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

#### `config/sanctum.php`
```php
'expiration' => null,  // Tokens never expire (or set days)
'middleware' => [
    'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
],
```

#### `config/filesystems.php`
```php
'default' => env('FILESYSTEM_DISK', 'local'),

'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

**Setup Storage Symlink**:
```bash
php artisan storage:link
```

---

## Deployment

### Docker Deployment

**Development**:
```bash
# Build and start
docker-compose -f docker-compose-dev.yml up -d

# Run migrations
docker-compose -f docker-compose-dev.yml exec app php artisan migrate

# Install dependencies
docker-compose -f docker-compose-dev.yml exec app composer install
docker-compose -f docker-compose-dev.yml exec app npm install
docker-compose -f docker-compose-dev.yml exec app npm run dev
```

**Production**:
```bash
# Build and start
docker-compose -f docker-compose-production.yml up -d

# Run migrations
docker-compose -f docker-compose-production.yml exec app php artisan migrate --force

# Optimize
docker-compose -f docker-compose-production.yml exec app php artisan config:cache
docker-compose -f docker-compose-production.yml exec app php artisan route:cache
docker-compose -f docker-compose-production.yml exec app php artisan view:cache

# Build assets
docker-compose -f docker-compose-production.yml exec app npm run build
```

**Services Included**:
- `app`: Nginx + PHP-FPM
- `mysql`: MySQL 8.0 database
- `redis`: Redis cache (optional)
- `queue`: Queue worker (php artisan queue:work)
- `scheduler`: Cron scheduler (php artisan schedule:work)

**Volumes**:
- `./storage:/var/www/html/storage` - Persistent storage
- `./bootstrap/cache:/var/www/html/bootstrap/cache` - Cache

### Traditional Deployment

**Requirements**:
- PHP 8.2+ with extensions: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- MySQL 8.0+
- Nginx or Apache
- Composer
- Node.js 18+ & NPM
- Supervisor (for queue workers)

**Steps**:

1. **Clone & Install**:
   ```bash
   git clone https://github.com/yourrepo/ecom-ai.git
   cd ecom-ai
   composer install --optimize-autoloader --no-dev
   npm install
   npm run build
   ```

2. **Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   # Edit .env with production values
   ```

3. **Database**:
   ```bash
   php artisan migrate --force
   ```

4. **Storage**:
   ```bash
   php artisan storage:link
   chmod -R 775 storage bootstrap/cache
   ```

5. **Optimize**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Web Server** (Nginx example):
   ```nginx
   server {
       listen 80;
       server_name yourapp.com;
       root /var/www/ecom-ai/public;

       add_header X-Frame-Options "SAMEORIGIN";
       add_header X-Content-Type-Options "nosniff";

       index index.php;

       charset utf-8;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location = /favicon.ico { access_log off; log_not_found off; }
       location = /robots.txt  { access_log off; log_not_found off; }

       error_page 404 /index.php;

       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.(?!well-known).* {
           deny all;
       }
   }
   ```

7. **Queue Worker** (Supervisor):
   ```ini
   [program:ecom-ai-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/ecom-ai/artisan queue:work --tries=3 --timeout=90
   autostart=true
   autorestart=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/var/www/ecom-ai/storage/logs/worker.log
   ```

8. **Scheduler** (Crontab):
   ```cron
   * * * * * cd /var/www/ecom-ai && php artisan schedule:run >> /dev/null 2>&1
   ```

### N8N Setup

**Requirements**:
1. Running N8N instance
2. Create workflows for:
   - Product analysis generation
   - Copywriting generation
   - Image generation
   - Image regeneration
3. Configure webhook URLs in .env

**N8N Workflow Template** (Product Analysis):
```
Webhook Trigger
  ↓
Extract Product Data
  ↓
AI Processing (OpenAI/Claude)
  ↓
Format Response
  ↓
HTTP Request (Callback to App)
```

---

## Code Conventions & Best Practices

### File Organization

**Controllers**:
- Web controllers: `app/Http/Controllers/` (rarely used with Livewire)
- API controllers: `app/Http/Controllers/Api/V1/`
- Versioned API: Always use version prefix (v1, v2, etc.)

**Livewire Components**:
- Organized by feature: `app/Livewire/Products/`, `app/Livewire/Settings/`
- Views in same structure: `resources/views/livewire/products/`
- Use Volt SFC style (single file component)

**Models**:
- Root level: `app/Models/`
- Always include relationships
- Use `$fillable` or `$guarded`
- Cast JSON columns: `protected $casts = ['features' => 'array']`

**Routes**:
- Web routes: `routes/web.php` (Livewire pages)
- API routes: `routes/api.php` (RESTful endpoints)
- Auth routes: `routes/auth.php` (Fortify)
- Group by middleware: `auth`, `auth:sanctum`, `guest`

### Naming Conventions

**Database Tables**: `plural_snake_case`
- ✅ `products`, `product_analyses`, `product_copies`
- ❌ `product`, `productAnalysis`

**Models**: `SingularPascalCase`
- ✅ `Product`, `ProductAnalysis`, `ProductCopy`
- ❌ `Products`, `product`

**Controllers**: `PascalCaseController`
- ✅ `ProductController`, `ProductAnalysisController`
- ❌ `productsController`, `Product_Controller`

**Livewire Components**: `PascalCase` (nested with /)
- ✅ `Products/Index`, `Products/Create`, `Settings/Profile`
- ❌ `products-index`, `ProductsIndex`

**Methods**: `camelCase`
- ✅ `generateAnalysis()`, `canAccessResource()`
- ❌ `GenerateAnalysis()`, `can_access_resource()`

**Variables**: `camelCase`
- ✅ `$productAnalysis`, `$mainImageUrl`
- ❌ `$product_analysis`, `$MainImageUrl`

**Translation Keys**: `snake_case`
- ✅ `products.create_product`, `messages.success`
- ❌ `products.createProduct`, `messages.Success`

### Authorization Patterns

**In Models**:
```php
public function canAccessResource($resource): bool
{
    // Admin tokens can access everything
    if (Auth::user()?->tokenCan('admin:*')) {
        return true;
    }

    // Users can only access their own resources
    return $resource->user_id === $this->id;
}
```

**In Controllers**:
```php
public function show(Product $product)
{
    if (!Auth::user()->canAccessResource($product)) {
        abort(403, 'Unauthorized');
    }

    return new ProductResource($product);
}
```

**In Livewire**:
```php
public function delete($productId)
{
    $product = Product::findOrFail($productId);

    if ($product->user_id !== Auth::id()) {
        abort(403);
    }

    $product->delete();
}
```

**Policy-Based** (alternative):
```bash
php artisan make:policy ProductPolicy --model=Product
```

```php
// app/Policies/ProductPolicy.php
public function view(User $user, Product $product)
{
    return $user->tokenCan('admin:*') || $product->user_id === $user->id;
}
```

### Translation Structure

**File Structure**:
```
resources/lang/
├── ar/
│   ├── auth.php
│   ├── messages.php
│   ├── products.php
│   ├── settings.php
│   └── validation.php
└── en/
    ├── auth.php
    ├── messages.php
    ├── products.php
    ├── settings.php
    └── validation.php
```

**Key Structure** (example: `products.php`):
```php
return [
    'title' => 'Products',
    'create' => 'Create Product',
    'edit' => 'Edit Product',
    'delete' => 'Delete Product',

    // Grouped by context
    'fields' => [
        'name' => 'Product Name',
        'description' => 'Description',
        'type' => 'Type',
    ],

    'types' => [
        'physical' => 'Physical Product',
        'digital' => 'Digital Product',
    ],

    'messages' => [
        'created' => 'Product created successfully',
        'updated' => 'Product updated successfully',
        'deleted' => 'Product deleted successfully',
    ],
];
```

**Usage**:
```blade
{{ __('products.title') }}
{{ __('products.fields.name') }}
{{ __('products.messages.created') }}
```

### API Resource Transformers

**Always use API Resources for consistent responses**:

```bash
php artisan make:resource ProductResource
```

```php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description_user' => $this->description_user,
            'description_ai' => $this->description_ai,
            'main_image_url' => $this->main_image_url,
            'type' => $this->type,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Relationships (when loaded)
            'analysis' => new ProductAnalysisResource($this->whenLoaded('analysis')),
            'copies' => ProductCopyResource::collection($this->whenLoaded('copies')),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
```

**Usage**:
```php
return new ProductResource($product);
return ProductResource::collection($products);
```

### Error Handling

**In Controllers**:
```php
try {
    $product = Product::findOrFail($id);
    return new ProductResource($product);
} catch (ModelNotFoundException $e) {
    return response()->json(['message' => 'Product not found'], 404);
} catch (\Exception $e) {
    Log::error('Error fetching product', ['error' => $e->getMessage()]);
    return response()->json(['message' => 'Internal server error'], 500);
}
```

**In Livewire**:
```php
try {
    $this->product->save();
    $this->dispatch('product-saved');
    session()->flash('success', __('products.messages.updated'));
} catch (\Exception $e) {
    Log::error('Error saving product', ['error' => $e->getMessage()]);
    session()->flash('error', __('messages.error'));
}
```

**Logging**:
```php
// Levels: debug, info, notice, warning, error, critical, alert, emergency
Log::info('Product analysis generated', ['product_id' => $product->id]);
Log::error('N8N webhook failed', ['error' => $response->body()]);
```

---

## Additional Resources

### Documentation Files

- **ADMIN_TOKEN_GUIDE.md** - Complete guide to creating and using admin tokens
- **ARABIC_TRANSLATION_GUIDE.md** - Translation workflow and guidelines
- **README.docker.md** - Docker setup and deployment instructions

### Useful Commands

```bash
# Development
php artisan serve                    # Start dev server
php artisan tinker                   # REPL for testing
php artisan pail                     # Real-time log streaming
npm run dev                          # Watch assets

# Database
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed     # Fresh migration with seeding
php artisan db:seed                  # Run seeders only

# Cache Management
php artisan cache:clear              # Clear application cache
php artisan config:clear             # Clear config cache
php artisan route:clear              # Clear route cache
php artisan view:clear               # Clear compiled views
php artisan optimize:clear           # Clear all caches

# Queue
php artisan queue:work               # Start queue worker
php artisan queue:failed             # List failed jobs
php artisan queue:retry all          # Retry all failed jobs

# Production Optimization
php artisan config:cache             # Cache config
php artisan route:cache              # Cache routes
php artisan view:cache               # Compile views
php artisan optimize                 # Run all optimizations

# Custom Commands
php artisan token:create-admin       # Create admin API token
```

### External Dependencies

**N8N Instance**: Required for AI features
- Self-hosted or cloud N8N
- Configure webhooks for each AI workflow
- Set `N8N_BASE_URL` in environment

**Storage**: File uploads and generated images
- Local: `storage/app/public`
- Or configure S3/DigitalOcean Spaces in `config/filesystems.php`

**Queue Backend**: For background processing
- Database driver (default, no config needed)
- Or Redis for better performance

---

## Quick Start Checklist

### New Developer Onboarding

- [ ] Clone repository
- [ ] Install dependencies (`composer install`, `npm install`)
- [ ] Copy `.env.example` to `.env` and configure
- [ ] Generate app key (`php artisan key:generate`)
- [ ] Create database and configure `.env`
- [ ] Run migrations (`php artisan migrate`)
- [ ] Link storage (`php artisan storage:link`)
- [ ] Build assets (`npm run dev`)
- [ ] Start dev server (`php artisan serve`)
- [ ] Configure N8N webhook URL in `.env`
- [ ] Create test user account
- [ ] Create admin token for API testing
- [ ] Review this documentation

### Production Deployment Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Configure mail server
- [ ] Set secure `APP_KEY`
- [ ] Run migrations (`php artisan migrate --force`)
- [ ] Optimize application (`php artisan optimize`)
- [ ] Build production assets (`npm run build`)
- [ ] Configure web server (Nginx/Apache)
- [ ] Set up queue workers (Supervisor)
- [ ] Set up scheduler (Cron)
- [ ] Configure backups
- [ ] Set up monitoring/logging
- [ ] Configure N8N production webhooks
- [ ] Test all critical features
- [ ] Set up SSL certificate

---

## Contact & Support

For issues, feature requests, or contributions, please refer to the project repository or contact the development team.

**Version**: 1.0
**Last Updated**: October 2025
