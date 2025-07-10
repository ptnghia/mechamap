# 💻 MechaMap Developer Guide

**Development Documentation for MechaMap Platform**  
**Laravel 10 + MySQL + Vue.js/React Frontend**  
**Target**: Developers, DevOps, Technical Contributors

---

## 🏗️ **ARCHITECTURE OVERVIEW**

### **🎯 System Architecture**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend API   │    │   Database      │
│                 │    │                 │    │                 │
│ • Next.js 15    │◄──►│ • Laravel 10    │◄──►│ • MySQL 8.0     │
│ • React 18      │    │ • PHP 8.2       │    │ • Redis Cache   │
│ • TypeScript    │    │ • Sanctum Auth  │    │ • File Storage  │
│ • Tailwind CSS  │    │ • Queue System  │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
        │                        │                        │
        ▼                        ▼                        ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   CDN Assets    │    │   File Storage  │    │   External APIs │
│                 │    │                 │    │                 │
│ • CloudFlare    │    │ • Local Storage │    │ • Stripe        │
│ • Image Optim   │    │ • AWS S3 Ready  │    │ • VNPay         │
│ • Static Files  │    │ • Secure DL     │    │ • Email Service │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### **🔧 Technology Stack**

#### **Backend Stack**
```php
🏗️ Core Framework:
- Laravel 10.x (PHP Framework)
- PHP 8.2+ (Programming Language)
- Composer (Dependency Management)

🗄️ Database:
- MySQL 8.0+ (Primary Database)
- Redis (Cache & Sessions)
- Laravel Eloquent ORM

🔐 Authentication & Security:
- Laravel Sanctum (API Authentication)
- Spatie Permissions (Role-Based Access)
- bcrypt Password Hashing

📦 Key Packages:
- spatie/laravel-permission (Role Management)
- laravel/sanctum (API Auth)
- intervention/image (Image Processing)
- maatwebsite/excel (Excel Export)
- barryvdh/laravel-cors (CORS Handling)
```

#### **Frontend Stack (Recommended)**
```typescript
⚛️ Core Framework:
- Next.js 15 (React Framework)
- React 18+ (UI Library)
- TypeScript (Type Safety)

🎨 UI & Styling:
- Tailwind CSS (Utility-First CSS)
- Headless UI (Accessible Components)
- React Hook Form (Form Management)
- Framer Motion (Animations)

🔧 Development Tools:
- ESLint (Code Linting)
- Prettier (Code Formatting)
- Jest (Unit Testing)
- Storybook (Component Development)
```

---

## 🚀 **DEVELOPMENT SETUP**

### **📋 Prerequisites**
```bash
# Required Software
- PHP 8.2+
- Composer 2.x
- Node.js 18+
- MySQL 8.0+
- Redis (optional but recommended)
- Git

# Development Tools
- VS Code / PhpStorm
- Postman / Insomnia (API testing)
- MySQL Workbench / phpMyAdmin
- Redis CLI
```

### **⚙️ Local Development Setup**

#### **Step 1: Clone Repository**
```bash
# Clone the repository
git clone https://github.com/your-org/mechamap-backend.git
cd mechamap-backend

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### **Step 2: Environment Configuration**
```bash
# Edit .env file
nano .env
```

```env
# Application
APP_NAME="MechaMap Local"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mechamap_local
DB_USERNAME=root
DB_PASSWORD=

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail (Development)
MAIL_MAILER=log
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null

# Payment (Test Keys)
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
VNPAY_MERCHANT_ID=test_merchant
VNPAY_HASH_SECRET=test_secret
```

#### **Step 3: Database Setup**
```bash
# Create database
mysql -u root -p
CREATE DATABASE mechamap_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit

# Install dependencies
composer install

# Run migrations
php artisan migrate

# Seed development data
php artisan db:seed --class=DevelopmentSeeder
```

#### **Step 4: Start Development Server**
```bash
# Start Laravel development server
php artisan serve

# Start queue worker (separate terminal)
php artisan queue:work

# Start Redis (if needed)
redis-server

# Your application is now running at:
# http://localhost:8000
```

---

## 🏗️ **PROJECT STRUCTURE**

### **📁 Laravel Backend Structure**
```
mechamap-backend/
├── app/
│   ├── Console/               # Artisan commands
│   ├── Http/
│   │   ├── Controllers/       # API Controllers
│   │   │   ├── Admin/        # Admin panel controllers
│   │   │   ├── Api/          # API endpoints
│   │   │   └── Auth/         # Authentication
│   │   ├── Middleware/       # Custom middleware
│   │   ├── Requests/         # Form request validation
│   │   └── Resources/        # API resources
│   ├── Models/               # Eloquent models
│   ├── Policies/             # Authorization policies
│   ├── Services/             # Business logic services
│   └── Providers/            # Service providers
├── config/                   # Configuration files
├── database/
│   ├── migrations/           # Database migrations
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories
├── resources/
│   ├── views/               # Blade templates (admin)
│   ├── lang/                # Localization files
│   └── js/                  # Frontend assets
├── routes/
│   ├── api.php              # API routes
│   ├── web.php              # Web routes
│   └── admin.php            # Admin routes
├── storage/                 # Storage & logs
├── tests/                   # Test suites
└── public/                  # Public assets
```

### **🎨 Key Directories Explained**

#### **`app/Http/Controllers/`**
```php
// API Controllers Structure
Api/
├── AuthController.php          # Authentication endpoints
├── UserController.php          # User management
├── ThreadController.php        # Forum threads
├── CommentController.php       # Thread comments
├── ProductController.php       # E-commerce products
├── OrderController.php         # Order management
├── PaymentController.php       # Payment processing
└── DownloadController.php      # Secure downloads

Admin/
├── DashboardController.php     # Admin dashboard
├── UserController.php          # User management
├── ModerationController.php    # Content moderation
├── SettingsController.php      # System settings
└── AnalyticsController.php     # Reports & analytics
```

#### **`app/Models/`**
```php
// Core Models
User.php                        # User accounts
Thread.php                      # Forum threads
Post.php                        # Forum posts
Comment.php                     # Thread comments
Product.php                     # E-commerce products
Order.php                       # Purchase orders
Payment.php                     # Payment transactions

// Relationship Models
ThreadBookmark.php              # User bookmarks
ThreadRating.php                # Thread ratings
OrderItem.php                   # Order line items
SecureDownload.php              # Download tracking
```

#### **`app/Services/`**
```php
// Business Logic Services
AuthService.php                 # Authentication logic
ForumService.php                # Forum operations
PaymentService.php              # Payment processing
DownloadService.php             # Secure download logic
ModerationService.php           # Content moderation
AnalyticsService.php            # Data analytics
NotificationService.php         # Email/SMS notifications
```

---

## 🔧 **DEVELOPMENT PATTERNS**

### **🎯 Code Organization Principles**

#### **Controller Pattern**
```php
<?php
// Example: ThreadController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateThreadRequest;
use App\Http\Resources\ThreadResource;
use App\Services\ForumService;
use Illuminate\Http\JsonResponse;

class ThreadController extends Controller
{
    public function __construct(
        private ForumService $forumService
    ) {}

    /**
     * Create a new thread
     */
    public function store(CreateThreadRequest $request): JsonResponse
    {
        try {
            $thread = $this->forumService->createThread(
                $request->validated(),
                auth()->user()
            );

            return response()->json([
                'success' => true,
                'data' => new ThreadResource($thread),
                'message' => 'Thread created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create thread'
            ], 500);
        }
    }
}
```

#### **Service Pattern**
```php
<?php
// Example: ForumService.php

namespace App\Services;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ForumService
{
    /**
     * Create a new thread with business logic
     */
    public function createThread(array $data, User $author): Thread
    {
        return DB::transaction(function () use ($data, $author) {
            // Create thread
            $thread = Thread::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'user_id' => $author->id,
                'forum_id' => $data['forum_id'],
                'thread_type' => $data['type'] ?? 'discussion',
            ]);

            // Create opening post
            $thread->posts()->create([
                'content' => $data['content'],
                'user_id' => $author->id,
                'is_opening_post' => true,
            ]);

            // Update forum statistics
            $this->updateForumStats($thread->forum_id);

            // Send notifications
            $this->notifyForumFollowers($thread);

            return $thread->load(['author', 'forum']);
        });
    }

    /**
     * Update forum statistics
     */
    private function updateForumStats(int $forumId): void
    {
        // Implementation here...
    }
}
```

#### **Model Relationship Pattern**
```php
<?php
// Example: Thread.php Model

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    protected $fillable = [
        'title', 'content', 'user_id', 'forum_id', 
        'thread_type', 'status', 'is_pinned', 'is_locked'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Thread belongs to a user (author)
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Thread belongs to a forum
     */
    public function forum(): BelongsTo
    {
        return $this->belongsTo(Forum::class);
    }

    /**
     * Thread has many posts
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->orderBy('created_at');
    }

    /**
     * Thread has many bookmarks
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(ThreadBookmark::class);
    }

    /**
     * Scope: Active threads only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope: Pinned threads first
     */
    public function scopeOrderByPinned($query)
    {
        return $query->orderBy('is_pinned', 'desc');
    }
}
```

---

## 🔌 **API DEVELOPMENT**

### **📡 API Design Principles**

#### **RESTful API Structure**
```
🔗 Authentication
POST   /api/auth/register       # User registration
POST   /api/auth/login          # User login
POST   /api/auth/logout         # User logout
POST   /api/auth/refresh        # Token refresh

👥 Users
GET    /api/users               # List users
GET    /api/users/{id}          # Get user details
PUT    /api/users/{id}          # Update user
DELETE /api/users/{id}          # Delete user

📝 Forum
GET    /api/threads             # List threads
POST   /api/threads             # Create thread
GET    /api/threads/{id}        # Get thread details
PUT    /api/threads/{id}        # Update thread
DELETE /api/threads/{id}        # Delete thread

💬 Comments
GET    /api/threads/{id}/posts  # Get thread posts
POST   /api/threads/{id}/posts  # Create post
PUT    /api/posts/{id}          # Update post
DELETE /api/posts/{id}          # Delete post

🛒 E-commerce
GET    /api/products            # List products
GET    /api/products/{id}       # Product details
POST   /api/cart/add            # Add to cart
GET    /api/cart                # Get cart
POST   /api/orders              # Create order
GET    /api/orders/{id}         # Order details
```

#### **Response Format Standards**
```json
// Success Response
{
    "success": true,
    "data": {
        "id": 123,
        "title": "Thread Title",
        "content": "Thread content...",
        "author": {
            "id": 456,
            "name": "John Doe",
            "username": "johndoe"
        }
    },
    "message": "Thread created successfully",
    "meta": {
        "timestamp": "2025-06-12T10:30:00Z",
        "version": "1.0"
    }
}

// Error Response
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid",
        "details": {
            "title": ["The title field is required"],
            "content": ["The content must be at least 50 characters"]
        }
    },
    "meta": {
        "timestamp": "2025-06-12T10:30:00Z",
        "version": "1.0"
    }
}

// Pagination Response
{
    "success": true,
    "data": [...],
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 150,
        "last_page": 8,
        "has_more": true
    }
}
```

### **🔐 Authentication & Authorization**

#### **Laravel Sanctum Setup**
```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),

// API Authentication Middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('threads', ThreadController::class);
    Route::apiResource('products', ProductController::class);
});
```

#### **Permission-based Authorization**
```php
// Using Policies
class ThreadPolicy
{
    public function update(User $user, Thread $thread): bool
    {
        return $user->id === $thread->user_id || 
               $user->hasRole(['admin', 'moderator']);
    }

    public function delete(User $user, Thread $thread): bool
    {
        return $user->hasRole(['admin', 'moderator']) ||
               ($user->id === $thread->user_id && $thread->posts()->count() <= 1);
    }
}

// In Controller
public function update(UpdateThreadRequest $request, Thread $thread)
{
    $this->authorize('update', $thread);
    
    // Update logic here...
}
```

---

## 🧪 **TESTING STRATEGY**

### **🔬 Testing Structure**
```
tests/
├── Feature/                    # Integration tests
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── RegistrationTest.php
│   ├── Forum/
│   │   ├── ThreadTest.php
│   │   └── CommentTest.php
│   └── Ecommerce/
│       ├── ProductTest.php
│       └── OrderTest.php
├── Unit/                       # Unit tests
│   ├── Models/
│   ├── Services/
│   └── Helpers/
└── TestCase.php               # Base test class
```

#### **Feature Test Example**
```php
<?php
// tests/Feature/Forum/ThreadTest.php

namespace Tests\Feature\Forum;

use App\Models\User;
use App\Models\Forum;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_thread()
    {
        $user = User::factory()->create();
        $forum = Forum::factory()->create();
        
        $threadData = [
            'title' => 'Test Thread Title',
            'content' => 'This is test content for the thread that is long enough to pass validation rules.',
            'forum_id' => $forum->id,
            'thread_type' => 'discussion'
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/threads', $threadData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'title', 'content', 'author'],
                'message'
            ]);

        $this->assertDatabaseHas('threads', [
            'title' => $threadData['title'],
            'user_id' => $user->id,
            'forum_id' => $forum->id
        ]);
    }

    /** @test */
    public function guest_cannot_create_thread()
    {
        $forum = Forum::factory()->create();
        
        $response = $this->postJson('/api/threads', [
            'title' => 'Test Thread',
            'content' => 'Test content',
            'forum_id' => $forum->id
        ]);

        $response->assertStatus(401);
    }
}
```

#### **Unit Test Example**
```php
<?php
// tests/Unit/Services/ForumServiceTest.php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Forum;
use App\Services\ForumService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumServiceTest extends TestCase
{
    use RefreshDatabase;

    private ForumService $forumService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->forumService = app(ForumService::class);
    }

    /** @test */
    public function it_creates_thread_with_opening_post()
    {
        $user = User::factory()->create();
        $forum = Forum::factory()->create();
        
        $threadData = [
            'title' => 'Test Thread',
            'content' => 'Test content for the thread',
            'forum_id' => $forum->id
        ];

        $thread = $this->forumService->createThread($threadData, $user);

        $this->assertInstanceOf(Thread::class, $thread);
        $this->assertEquals($threadData['title'], $thread->title);
        $this->assertEquals($user->id, $thread->user_id);
        $this->assertEquals(1, $thread->posts()->count());
        $this->assertTrue($thread->posts()->first()->is_opening_post);
    }
}
```

### **🚀 Running Tests**
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test tests/Feature/Forum

# Run with coverage
php artisan test --coverage

# Run parallel tests (faster)
php artisan test --parallel

# Watch mode (auto-run on file changes)
php artisan test --watch
```

---

## 🔄 **DATABASE DEVELOPMENT**

### **📊 Migration Development**

#### **Creating Migrations**
```bash
# Create migration
php artisan make:migration create_threads_table

# Create migration with model
php artisan make:model Thread -m

# Create migration for relationship table
php artisan make:migration create_thread_bookmarks_table
```

#### **Migration Example**
```php
<?php
// database/migrations/create_threads_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('forum_id')->constrained()->onDelete('cascade');
            
            $table->enum('status', ['published', 'draft', 'archived'])->default('published');
            $table->enum('thread_type', ['discussion', 'question', 'tutorial', 'showcase'])->default('discussion');
            
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_featured')->default(false);
            
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('reply_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('ratings_count')->default(0);
            
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['forum_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_pinned', 'last_activity_at']);
            $table->fullText(['title', 'content']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
```

### **🌱 Seeder Development**

#### **Factory Definition**
```php
<?php
// database/factories/ThreadFactory.php

namespace Database\Factories;

use App\Models\User;
use App\Models\Forum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6),
            'content' => $this->faker->paragraphs(3, true),
            'user_id' => User::factory(),
            'forum_id' => Forum::factory(),
            'status' => 'published',
            'thread_type' => $this->faker->randomElement(['discussion', 'question', 'tutorial']),
            'view_count' => $this->faker->numberBetween(0, 1000),
            'reply_count' => $this->faker->numberBetween(0, 50),
            'last_activity_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'average_rating' => $this->faker->randomFloat(2, 4.0, 5.0),
        ]);
    }
}
```

#### **Seeder Implementation**
```php
<?php
// database/seeders/DevelopmentSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Forum;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@mechamap.com',
            'role' => 'admin'
        ]);

        $moderator = User::factory()->create([
            'name' => 'Moderator User',
            'email' => 'moderator@mechamap.com',
            'role' => 'moderator'
        ]);

        // Create regular users
        $users = User::factory(50)->create();

        // Create forums with realistic data
        $forums = Forum::factory(10)->create();

        // Create threads with engagement
        $forums->each(function ($forum) use ($users) {
            Thread::factory(rand(5, 15))
                ->for($forum)
                ->for($users->random())
                ->create();
        });

        // Create some featured content
        Thread::factory(5)
            ->featured()
            ->for($forums->random())
            ->for($users->random())
            ->create();
    }
}
```

---

## 🚀 **DEPLOYMENT & DEVOPS**

### **🐳 Docker Development**

#### **Dockerfile**
```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

#### **Docker Compose**
```yaml
# docker-compose.yml
version: '3.8'

services:
  app:
    build: .
    container_name: mechamap-app
    volumes:
      - .:/var/www
    networks:
      - mechamap

  nginx:
    image: nginx:alpine
    container_name: mechamap-nginx
    ports:
      - "80:80"
    volumes:
      - .:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - mechamap

  mysql:
    image: mysql:8.0
    container_name: mechamap-mysql
    environment:
      MYSQL_DATABASE: mechamap
      MYSQL_ROOT_PASSWORD: secret
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"
    networks:
      - mechamap

  redis:
    image: redis:alpine
    container_name: mechamap-redis
    ports:
      - "6379:6379"
    networks:
      - mechamap

networks:
  mechamap:
    driver: bridge

volumes:
  mysql_data:
```

### **⚙️ CI/CD Pipeline**

#### **GitHub Actions**
```yaml
# .github/workflows/ci.yml
name: CI/CD Pipeline

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: secret
          MYSQL_DATABASE: mechamap_test
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.2
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, dom, filter, gd, iconv, json, mbstring
    
    - name: Install Composer dependencies
      run: composer install --no-progress --prefer-dist --optimize-autoloader
    
    - name: Copy environment file
      run: cp .env.example .env.testing
    
    - name: Generate application key
      run: php artisan key:generate --env=testing
    
    - name: Run migrations
      run: php artisan migrate --env=testing
    
    - name: Run tests
      run: php artisan test
    
    - name: Run code analysis
      run: ./vendor/bin/phpstan analyse

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
    - name: Deploy to production
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/mechamap
          git pull origin main
          composer install --no-dev --optimize-autoloader
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo systemctl reload nginx
```

---

## 🔧 **DEBUGGING & MONITORING**

### **🐛 Debugging Tools**

#### **Laravel Telescope (Development)**
```bash
# Install Telescope
composer require laravel/telescope --dev

# Publish assets
php artisan telescope:install
php artisan migrate

# Configure in .env
TELESCOPE_ENABLED=true
```

#### **Debug Configuration**
```php
// config/app.php
'debug' => env('APP_DEBUG', false),

// For development logging
'log_level' => env('LOG_LEVEL', 'debug'),

// In .env
APP_DEBUG=true
LOG_LEVEL=debug
LOG_CHANNEL=stack
```

### **📊 Performance Monitoring**

#### **Query Optimization**
```php
// Enable query logging in development
DB::enableQueryLog();

// Check queries after request
$queries = DB::getQueryLog();
dump($queries);

// Use Debugbar for development
composer require barryvdh/laravel-debugbar --dev
```

#### **Cache Optimization**
```php
// Cache configuration
Route::middleware('cache.headers:public;max_age=2628000')->group(function () {
    Route::get('/api/categories', [CategoryController::class, 'index']);
});

// Model caching
class Thread extends Model
{
    public function forum()
    {
        return $this->belongsTo(Forum::class)->remember(3600);
    }
}
```

---

## 📚 **CODING STANDARDS**

### **📝 PHP Coding Standards**

#### **PSR-12 Compliance**
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Forum service for handling thread operations
 */
class ForumService
{
    /**
     * Create a new thread
     */
    public function createThread(array $data, User $author): Thread
    {
        // Method implementation...
    }
    
    /**
     * Private helper method
     */
    private function validateThreadData(array $data): void
    {
        // Validation logic...
    }
}
```

#### **Code Documentation**
```php
/**
 * Create a new thread in the forum
 *
 * @param array $data Thread data containing title, content, forum_id
 * @param User $author The user creating the thread
 * @return Thread The created thread with relationships loaded
 * @throws \InvalidArgumentException When data is invalid
 * @throws \Exception When thread creation fails
 */
public function createThread(array $data, User $author): Thread
{
    // Implementation...
}
```

### **🎨 Frontend Standards (TypeScript)**
```typescript
// Type definitions
interface Thread {
  id: number;
  title: string;
  content: string;
  author: User;
  forum: Forum;
  createdAt: string;
  updatedAt: string;
}

// Component with proper typing
interface ThreadCardProps {
  thread: Thread;
  onBookmark?: (threadId: number) => void;
  className?: string;
}

export const ThreadCard: React.FC<ThreadCardProps> = ({
  thread,
  onBookmark,
  className
}) => {
  // Component implementation...
};

// API service with proper error handling
export const threadService = {
  async createThread(data: CreateThreadData): Promise<Thread> {
    try {
      const response = await apiClient.post<ApiResponse<Thread>>('/threads', data);
      return response.data.data;
    } catch (error) {
      throw new ApiError('Failed to create thread', error);
    }
  }
};
```

---

## 🔗 **USEFUL RESOURCES**

### **📖 Documentation Links**
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel API Resources](https://laravel.com/docs/eloquent-resources)
- [Spatie Permission Docs](https://spatie.be/docs/laravel-permission)
- [Laravel Sanctum Guide](https://laravel.com/docs/sanctum)

### **🛠️ Development Tools**
- [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)
- [Laravel Telescope](https://laravel.com/docs/telescope)
- [PHPStan](https://phpstan.org/) - Static Analysis
- [PHP CS Fixer](https://cs.symfony.com/) - Code Formatting

### **📱 Frontend Resources**
- [Next.js Documentation](https://nextjs.org/docs)
- [React Query](https://tanstack.com/query) - Data Fetching
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Headless UI](https://headlessui.dev/) - Components

---

**💻 Happy Coding!**  
**🚀 Build amazing features for the MechaMap community!**
