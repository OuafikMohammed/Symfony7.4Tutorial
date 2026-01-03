# Symfony 7.4 Complete Course Project

A comprehensive, **production-ready** course project demonstrating **URL generation**, **User registration**, **CRUD operations**, **Enums**, and **best practices** in **Symfony 7.4**.

## 📚 What This Project Teaches

This project is a **complete real-world learning platform** that covers:

- ✅ **URL Generation Patterns** - All the ways to generate URLs in Symfony
- ✅ **User Registration & Authentication** - Complete workflow with validation
- ✅ **Email Verification & Password Reset** - Secure token-based flows
- ✅ **CRUD Operations** - Create, Read, Update, Delete with Doctrine ORM
- ✅ **Enums & Type Safety** - PHP 8.1+ Enums with automatic conversion
- ✅ **Database Integration** - Doctrine ORM, Migrations, Repositories
- ✅ **Service Layer** - Dependency injection, email notifications
- ✅ **API Patterns** - REST API with HATEOAS links
- ✅ **Security Best Practices** - Token validation, signed URLs, CSRF protection

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Symfony 7.4
- Docker & Docker Compose (optional)
- Composer

### Installation

1. **Clone or navigate to the project**
```bash
cd my_project_directory
```

2. **Install dependencies**
```bash
composer install
```

3. **Configure environment**
```bash
cp .env .env.local
# Edit .env.local with your database credentials
```

4. **Create and run migrations**
```bash
php bin/console doctrine:migrations:migrate
```

5. **Start the development server**
```bash
php bin/console server:run
# Or use: symfony serve
```

6. **Access the application**
- **User Registration:** http://localhost:8000/user/register
- **Blog Posts:** http://localhost:8000/posts
- **Lucky Page:** http://localhost:8000/lucky/number/5

## 📁 Project Structure & Concepts Covered

```
my_project_directory/
├── src/
│   ├── Controller/
│   │   ├── LuckyController.php                 # Basic controller example
│   │   ├── PostController.php                  # CRUD operations
│   │   ├── OrderController.php                 # Enum example
│   │   ├── UserRegistrationController.php      # Complete auth flow
│   │   ├── UrlGenerationBlogController.php     # URL generation patterns
│   │   └── Api/                                # API endpoints
│   ├── Entity/
│   │   ├── User.php                            # User entity with validations
│   │   └── Post.php                            # Blog post entity
│   ├── Enum/
│   │   └── OrderStatusEnum.php                 # Type-safe enum example
│   ├── Repository/
│   │   └── PostRepository.php                  # Custom queries
│   ├── Service/
│   │   ├── EmailNotificationService.php        # Email service with URL generation
│   │   ├── SignedUrlService.php                # Cryptographic URL signing
│   │   └── UrlGenerationConfiguration.php      # Advanced patterns
│   ├── Command/
│   │   └── UrlGenerationCommand.php            # Console commands
│   └── Kernel.php
├── templates/
│   ├── base.html.twig                          # Base layout
│   ├── user/                                   # User registration templates
│   │   ├── register.html.twig
│   │   ├── verify_error.html.twig
│   │   ├── password_reset_request.html.twig
│   │   ├── password_reset_form.html.twig
│   │   └── dashboard.html.twig
│   ├── post/                                   # CRUD templates
│   │   ├── index.html.twig
│   │   ├── show.html.twig
│   │   ├── create.html.twig
│   │   └── edit.html.twig
│   └── lucky/                                  # Examples
├── config/
│   ├── routes.yaml                             # Main routes
│   ├── services.yaml                           # Service configuration
│   └── packages/
│       ├── doctrine.yaml                       # Database config
│       ├── security.yaml                       # Security config
│       └── ... (other configs)
├── migrations/                                 # Database migrations
├── public/
│   └── index.php                              # Application entry point
└── docs/ (Documentation files)
    ├── 00_START_HERE.md                        # Quick overview
    ├── INDEX.md                                # Navigation hub
    ├── README_URL_GENERATION.md                # URL generation summary
    ├── VISUAL_QUICK_START.md                   # Visual guide
    ├── URL_GENERATION_GUIDE.md                 # Complete tutorial
    ├── URL_GENERATION_QUICK_REFERENCE.md       # Cheat sheet
    ├── URL_GENERATION_LEARNING_PATH.md         # Learning roadmap
    ├── VISUAL_URL_GENERATION_GUIDE.md          # Flowcharts & diagrams
    ├── CRUD_TESTING_GUIDE.md                   # CRUD operations guide
    └── ENUM_GUIDE.md                           # Enum tutorial
```

## 🎯 Core Topics & Examples

### 1. 🔗 URL Generation

**Documentation:** [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md) | [URL_GENERATION_QUICK_REFERENCE.md](URL_GENERATION_QUICK_REFERENCE.md)

**Controllers:** 
- [UrlGenerationBlogController.php](src/Controller/UrlGenerationBlogController.php) - Basic patterns
- [UserRegistrationController.php](src/Controller/UserRegistrationController.php) - Real-world flow

**Key Patterns:**
- Absolute vs Relative URLs
- Route parameters
- Query parameters
- Signed URLs for security
- Multi-language URL generation

**Example:**
```php
// Generate URL in controller
$url = $this->generateUrl('blog_list', ['id' => 123]);

// Generate absolute URL (for emails)
$url = $this->generateUrl(
    'user_verify_email',
    ['token' => $token],
    UrlGeneratorInterface::ABSOLUTE_URL
);

// Generate in service
$link = $this->urlGenerator->generate('verify_email', [], ABSOLUTE_URL);
```

### 2. 👤 User Registration & Authentication

**Documentation:** [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md) | [VISUAL_URL_GENERATION_GUIDE.md](VISUAL_URL_GENERATION_GUIDE.md)

**Controller:** [UserRegistrationController.php](src/Controller/UserRegistrationController.php)

**Entity:** [User.php](src/Entity/User.php)

**Features:**
- User registration with validation
- Email verification with tokens
- Password reset workflow
- Dashboard access
- Session management

**Routes:**
- `GET /user/register` - Registration form
- `POST /user/register` - Handle registration
- `GET /user/verify/{userId}/{token}` - Email verification
- `GET /user/password-reset` - Password reset request
- `POST /user/password-reset` - Send reset email
- `GET /user/password-reset/{userId}/{token}` - Reset form
- `POST /user/password-reset/{userId}/{token}` - Update password
- `GET /user/dashboard` - User dashboard

### 3. 📝 CRUD Operations (Blog Posts)

**Documentation:** [CRUD_TESTING_GUIDE.md](CRUD_TESTING_GUIDE.md)

**Controller:** [PostController.php](src/Controller/PostController.php)

**Entity:** [Post.php](src/Entity/Post.php)

**Templates:** [templates/post/](templates/post/)

**Operations:**
- **Create:** Add new blog posts
- **Read:** Display all posts or single post
- **Update:** Edit existing posts
- **Delete:** Remove posts

**Routes:**
- `GET /posts` - List all posts
- `GET /posts/{id}` - Show single post
- `GET /posts/create` - Show create form
- `POST /posts/create` - Save new post
- `GET /posts/{id}/edit` - Show edit form
- `POST /posts/{id}/edit` - Update post
- `POST /posts/{id}/delete` - Delete post

### 4. 📦 PHP Enums with Type Safety

**Documentation:** [ENUM_GUIDE.md](ENUM_GUIDE.md)

**Enum:** [OrderStatusEnum.php](src/Enum/OrderStatusEnum.php)

**Controller:** [OrderController.php](src/Controller/OrderController.php)

**Benefits:**
- Type-safe status values
- Automatic validation
- IDE autocomplete
- Cleaner code

**Example:**
```php
// Define enum
enum OrderStatusEnum: string {
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
}

// Use in controller
#[Route('/orders/{status}')]
public function listByStatus(OrderStatusEnum $status): Response
{
    // $status is automatically converted and validated
    // Invalid values return 404
}
```

### 5. 🔐 Security & Signed URLs

**Documentation:** [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md)

**Service:** [SignedUrlService.php](src/Service/SignedUrlService.php)

**Features:**
- Cryptographic URL signing
- Expiration handling
- Token validation
- Secure email links

**Example:**
```php
// Create signed URL
$signedUrl = $this->signedUrlService->create(
    'user_verify_email',
    ['userId' => $user->getId()],
    3600 // 1 hour expiration
);

// Verify signed URL
if ($this->signedUrlService->verify($signedUrl)) {
    // Safe to process
}
```

### 6. 🔧 Services & Dependency Injection

**Documentation:** [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md)

**Services:**
- [EmailNotificationService.php](src/Service/EmailNotificationService.php) - Email generation
- [SignedUrlService.php](src/Service/SignedUrlService.php) - URL signing
- [UrlGenerationConfiguration.php](src/Service/UrlGenerationConfiguration.php) - Configuration

**Patterns:**
- Constructor injection
- Type-hinting
- Auto-wiring
- Service reusability

### 7. 💾 Database with Doctrine ORM

**Entities:**
- [User.php](src/Entity/User.php)
- [Post.php](src/Entity/Post.php)

**Features:**
- Automatic migrations
- Repository queries
- Entity relationships
- Validation annotations

### 8. 🎮 Console Commands

**Documentation:** [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md)

**Command:** [UrlGenerationCommand.php](src/Command/UrlGenerationCommand.php)

**Usage:**
```bash
php bin/console app:generate-urls [--count=10]
```

### 9. 🌐 API Endpoints (Bonus)

**Patterns:**
- HATEOAS links
- Absolute URLs for API clients
- JSON responses
- REST conventions

### 10. 🎨 UI & Styling with Tailwind CSS

**Templates:** [templates/](templates/)

**Styling:**
- Tailwind CSS CDN integration
- Modern, responsive design
- Professional UI components
- Inter font family

**Features:**
- Clean, minimalist layout
- Mobile-responsive design
- Gradient backgrounds
- Shadow and spacing utilities
- Form styling with Tailwind
- Navigation with active states
- Success/error message styling
- Card-based layouts

**What's Styled:**
- Base layout with navigation
- User registration forms
- CRUD post management
- Dashboard pages
- Blog listing pages
- Error pages
- Lucky number pages

## � Complete Documentation Guide

### 🎯 Choose Your Learning Path

| Learning Style | Start Here | Time | Best For |
|---|---|---|---|
| **Visual Learner** | [VISUAL_QUICK_START.md](VISUAL_QUICK_START.md) | 10 min | Diagrams, flowcharts, visual examples |
| **Beginner** | [00_START_HERE.md](00_START_HERE.md) | 5 min | Quick overview of what's included |
| **Quick Reference** | [URL_GENERATION_QUICK_REFERENCE.md](URL_GENERATION_QUICK_REFERENCE.md) | 10 min | Code snippets and common patterns |
| **Structured Path** | [URL_GENERATION_LEARNING_PATH.md](URL_GENERATION_LEARNING_PATH.md) | 15 min | Step-by-step progression |
| **Complete Tutorial** | [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md) | 40 min | In-depth explanations |
| **Navigation** | [INDEX.md](INDEX.md) | 10 min | Find what you need quickly |

### 📖 Documentation Files (In Root Directory)

#### 1. [00_START_HERE.md](00_START_HERE.md)
- **What's included** in this project
- **Quick overview** of all files
- **Best for**: First-time visitors
- **Read time**: ~5 minutes

#### 2. [INDEX.md](INDEX.md)
- **Navigation hub** for all documentation
- **Learning paths** by experience level
- **Coverage matrix** of topics
- **Best for**: Finding specific topics
- **Read time**: ~10 minutes

#### 3. [README_URL_GENERATION.md](README_URL_GENERATION.md)
- **URL generation summary** and overview
- **Common tasks** with examples
- **Security checklist**
- **Which file to use when**
- **Best for**: Understanding URL generation basics
- **Read time**: ~7 minutes

#### 4. [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md) ⭐ **Main Tutorial**
- **Complete step-by-step guide** to URL generation
- **Controller examples** with 6 patterns
- **Service layer examples**
- **Console command examples**
- **Signed URLs & security**
- **Common mistakes & solutions**
- **Best for**: Deep learning
- **Read time**: ~40 minutes

#### 5. [URL_GENERATION_QUICK_REFERENCE.md](URL_GENERATION_QUICK_REFERENCE.md) ⭐ **Cheat Sheet**
- **Quick code snippets** for common tasks
- **URL type reference table**
- **Parameter types & examples**
- **Configuration patterns**
- **Testing examples**
- **Troubleshooting guide**
- **Best for**: Coding reference while working
- **Read time**: ~10 minutes

#### 6. [URL_GENERATION_LEARNING_PATH.md](URL_GENERATION_LEARNING_PATH.md)
- **Structured 6-level progression** from beginner to advanced
- **Common workflows** with step-by-step instructions
- **What to learn in each stage**
- **Verification checklist** for each level
- **Pro tips and FAQ**
- **Best for**: Organized learning
- **Read time**: ~15 minutes

#### 7. [VISUAL_QUICK_START.md](VISUAL_QUICK_START.md)
- **ASCII flowcharts** and diagrams
- **Visual request/response flow**
- **Code templates** ready to use
- **10-minute quick reference**
- **Best for**: Visual learners
- **Read time**: ~10 minutes

#### 8. [VISUAL_URL_GENERATION_GUIDE.md](VISUAL_URL_GENERATION_GUIDE.md)
- **Complete user registration flow** with diagrams
- **Password reset flow** with visuals
- **Real-world example** step-by-step
- **Best for**: Understanding complete workflows
- **Read time**: ~15 minutes

#### 9. [CRUD_TESTING_GUIDE.md](CRUD_TESTING_GUIDE.md)
- **CRUD operations** testing guide
- **Blog post management** example
- **Database setup** instructions
- **Manual testing steps** for each operation
- **Best for**: Testing CRUD features
- **Read time**: ~20 minutes

#### 10. [ENUM_GUIDE.md](ENUM_GUIDE.md)
- **PHP 8.1+ Enums** tutorial
- **Type safety** advantages
- **OrderStatusEnum** example
- **How to use** in controllers
- **Best for**: Learning PHP Enums
- **Read time**: ~15 minutes

#### 11. [MANIFEST.md](MANIFEST.md)
- **Complete file listing** of all created files
- **Statistics** and metrics
- **Content overview** of all topics
- **Best for**: Getting full picture
- **Read time**: ~10 minutes

#### 12. [UPDATED_DOCUMENTATION_SUMMARY.md](UPDATED_DOCUMENTATION_SUMMARY.md)
- **Latest updates** and improvements
- **Feature summary**
- **Change log**

### 🎓 Concept-Based Navigation

#### URL Generation Concepts
- **Basic URL Generation** → [URL_GENERATION_QUICK_REFERENCE.md](URL_GENERATION_QUICK_REFERENCE.md)
- **Advanced Patterns** → [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md)
- **Real-World Example** → [VISUAL_URL_GENERATION_GUIDE.md](VISUAL_URL_GENERATION_GUIDE.md)
- **Learning Path** → [URL_GENERATION_LEARNING_PATH.md](URL_GENERATION_LEARNING_PATH.md)

#### User Registration & Authentication
- **Complete Flow** → [VISUAL_URL_GENERATION_GUIDE.md](VISUAL_URL_GENERATION_GUIDE.md)
- **Code Example** → [src/Controller/UserRegistrationController.php](src/Controller/UserRegistrationController.php)
- **Step-by-Step** → [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md)

#### CRUD Operations
- **Testing Guide** → [CRUD_TESTING_GUIDE.md](CRUD_TESTING_GUIDE.md)
- **Code Example** → [src/Controller/PostController.php](src/Controller/PostController.php)
- **Entities** → [src/Entity/Post.php](src/Entity/Post.php)

#### Database & Doctrine ORM
- **Entity Definition** → [src/Entity/User.php](src/Entity/User.php), [src/Entity/Post.php](src/Entity/Post.php)
- **Repository Queries** → [src/Repository/PostRepository.php](src/Repository/PostRepository.php)
- **Migrations** → [migrations/](migrations/)

#### Enums & Type Safety
- **Enum Tutorial** → [ENUM_GUIDE.md](ENUM_GUIDE.md)
- **Code Example** → [src/Enum/OrderStatusEnum.php](src/Enum/OrderStatusEnum.php)
- **Controller Usage** → [src/Controller/OrderController.php](src/Controller/OrderController.php)

#### Services & Dependency Injection
- **Email Service** → [src/Service/EmailNotificationService.php](src/Service/EmailNotificationService.php)
- **Signed URLs** → [src/Service/SignedUrlService.php](src/Service/SignedUrlService.php)
- **Configuration** → [src/Service/UrlGenerationConfiguration.php](src/Service/UrlGenerationConfiguration.php)

#### Security Best Practices
- **Signed URLs** → [src/Service/SignedUrlService.php](src/Service/SignedUrlService.php)
- **Token Generation** → [src/Controller/UserRegistrationController.php](src/Controller/UserRegistrationController.php)
- **Password Hashing** → [src/Entity/User.php](src/Entity/User.php)

## 🔑 Key Concepts

### URL Generation Types

```php
// ABSOLUTE_PATH (default) - /user/register
$url = $this->generateUrl('user_register');

// ABSOLUTE_URL (full domain) - https://example.com/user/register
$url = $this->generateUrl('user_register', [], UrlGeneratorInterface::ABSOLUTE_URL);

// NETWORK_PATH (protocol-relative) - //example.com/user/register
$url = $this->generateUrl('user_register', [], UrlGeneratorInterface::NETWORK_PATH);
```

### Route Parameters

```php
// Simple route
#[Route('/register', name: 'user_register')]

// Route with parameters
#[Route('/verify/{userId}/{token}', name: 'user_verify_email')]

// Generate URL with parameters
$url = $this->generateUrl('user_verify_email', [
    'userId' => 123,
    'token' => 'abc123',
]);
// Result: /user/verify/123/abc123
```

### Query Parameters

```php
// Extra parameters become query strings
$url = $this->generateUrl('user_settings', [
    'section' => 'account',
    'theme' => 'dark',
]);
// Result: /user/settings?section=account&theme=dark
```

## 🧪 Testing & Validation

### Run Migrations (Database Setup)
```bash
php bin/console doctrine:migrations:migrate
```

### Test User Registration
1. Navigate to: http://localhost:8000/user/register
2. Fill in the registration form
3. Check email verification (in dev mode, check browser console)
4. Complete password reset flow

### Test CRUD Operations (Blog Posts)
1. Navigate to: http://localhost:8000/posts
2. Create a new post
3. Edit existing post
4. Delete a post

See [CRUD_TESTING_GUIDE.md](CRUD_TESTING_GUIDE.md) for detailed testing steps.

### Run Unit Tests
```bash
php bin/phpunit
```

### Use Testing Scripts
```bash
# Windows PowerShell
.\test_crud.ps1

# Linux/Mac Bash
./test_crud.sh
```

### Test Console Commands
```bash
# Run URL generation command
php bin/console app:generate-urls --count=5
```

## � Documentation Files Matrix

| File | Purpose | Key Topics | Best For |
|------|---------|-----------|----------|
| [INDEX.md](INDEX.md) | Navigation hub | All topics, learning paths | Finding what to read |
| [00_START_HERE.md](00_START_HERE.md) | Overview | Project contents, file list | New users |
| [README_URL_GENERATION.md](README_URL_GENERATION.md) | URL gen summary | Overview, quick start, common tasks | Quick reference |
| [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md) | Complete guide | All URL patterns, step-by-step | Deep learning |
| [URL_GENERATION_QUICK_REFERENCE.md](URL_GENERATION_QUICK_REFERENCE.md) | Cheat sheet | Code snippets, tables, troubleshooting | While coding |
| [URL_GENERATION_LEARNING_PATH.md](URL_GENERATION_LEARNING_PATH.md) | Learning roadmap | 6-level progression, workflows | Structured learning |
| [VISUAL_QUICK_START.md](VISUAL_QUICK_START.md) | Visual guide | Diagrams, flowcharts, templates | Visual learners |
| [VISUAL_URL_GENERATION_GUIDE.md](VISUAL_URL_GENERATION_GUIDE.md) | Real-world flow | Complete user registration flow | Understanding workflows |
| [CRUD_TESTING_GUIDE.md](CRUD_TESTING_GUIDE.md) | CRUD testing | Post management, testing steps | Testing CRUD |
| [ENUM_GUIDE.md](ENUM_GUIDE.md) | Enum tutorial | PHP 8.1 Enums, type safety | Learning Enums |
| [MANIFEST.md](MANIFEST.md) | File inventory | All files, statistics | Getting full picture |

## 🔑 Key Concepts

### URL Generation Types

```php
// ABSOLUTE_PATH (default) - /user/register
$url = $this->generateUrl('user_register');

// ABSOLUTE_URL (full domain) - https://example.com/user/register
$url = $this->generateUrl('user_register', [], UrlGeneratorInterface::ABSOLUTE_URL);

// NETWORK_PATH (protocol-relative) - //example.com/user/register
$url = $this->generateUrl('user_register', [], UrlGeneratorInterface::NETWORK_PATH);
```

### Route Parameters

```php
// Simple route
#[Route('/register', name: 'user_register')]

// Route with parameters
#[Route('/verify/{userId}/{token}', name: 'user_verify_email')]

// Generate URL with parameters
$url = $this->generateUrl('user_verify_email', [
    'userId' => 123,
    'token' => 'abc123',
]);
// Result: /user/verify/123/abc123
```

### Query Parameters

```php
// Extra parameters become query strings
$url = $this->generateUrl('user_settings', [
    'section' => 'account',
    'theme' => 'dark',
]);
// Result: /user/settings?section=account&theme=dark
```

### Entity Relationships

```php
// One User has Many Posts
#[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user')]
private Collection $posts;

// Generate URL with object
$url = $this->generateUrl('blog_post', [
    'id' => $post->getId(),
]);
```

### Service Dependency Injection

```php
class EmailService {
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private MailerInterface $mailer,
    ) {}
    
    public function sendEmail() {
        $link = $this->urlGenerator->generate('verify_email', [], ABSOLUTE_URL);
        // Send email with link
    }
}
```

### Signed URLs

```php
// Create signed URL with expiration
$signedUrl = $this->signedUrlService->create(
    'user_verify_email',
    ['userId' => $user->getId()],
    3600 // 1 hour
);

// Verify before using
if ($this->signedUrlService->verify($signedUrl)) {
    // Process verified request
}
```

### Type-Safe Enums

```php
// Define enum
enum OrderStatusEnum: string {
    case Pending = 'pending';
    case Paid = 'paid';
    case Shipped = 'shipped';
}

// Use in controller - automatic validation
#[Route('/orders/{status}')]
public function listByStatus(OrderStatusEnum $status): Response
{
    // $status is guaranteed to be valid
    // Invalid values automatically return 404
}
```

## �️ Configuration Files

### Database Configuration
**File:** [config/packages/doctrine.yaml](config/packages/doctrine.yaml)

Default uses SQLite. To use MySQL/PostgreSQL:
1. Update `DATABASE_URL` in `.env.local`
2. Run migrations: `php bin/console doctrine:migrations:migrate`

### Routing Configuration
**File:** [config/routes.yaml](config/routes.yaml) and [config/routes/controllers.yaml](config/routes/controllers.yaml)

All application routes are defined using PHP attributes in controllers.

### Services Configuration
**File:** [config/services.yaml](config/services.yaml)

Symfony auto-wires services by type-hinting in constructors. No manual configuration needed.

### Security Configuration
**File:** [config/packages/security.yaml](config/packages/security.yaml)

Configured with CSRF protection and session management.

## 🔒 Security Best Practices Demonstrated

- ✅ **Password Hashing** - Uses `PASSWORD_BCRYPT`
- ✅ **Token Generation** - Random 32-byte secure tokens
- ✅ **URL Signing** - Cryptographic signing with expiration
- ✅ **Email Verification** - Token-based verification flow
- ✅ **CSRF Protection** - Built-in Symfony protection
- ✅ **Input Validation** - Request parameter validation
- ✅ **Secure Redirects** - Validated redirect URLs

**⚠️ Important Note:** This is an educational project. For production use:
- Implement comprehensive input validation
- Configure proper email delivery (SMTP)
- Use environment-specific security configurations
- Add rate limiting and throttling
- Implement proper token storage in database
- Enforce HTTPS
- Add security headers
- Implement monitoring and logging

## 💻 File Quick Reference

### Controllers
- [LuckyController.php](src/Controller/LuckyController.php) - Simple example
- [PostController.php](src/Controller/PostController.php) - CRUD operations
- [OrderController.php](src/Controller/OrderController.php) - Enum usage
- [UserRegistrationController.php](src/Controller/UserRegistrationController.php) - Complete auth flow
- [UrlGenerationBlogController.php](src/Controller/UrlGenerationBlogController.php) - URL patterns

### Entities
- [User.php](src/Entity/User.php) - User with validation
- [Post.php](src/Entity/Post.php) - Blog post entity

### Services
- [EmailNotificationService.php](src/Service/EmailNotificationService.php) - Email with URLs
- [SignedUrlService.php](src/Service/SignedUrlService.php) - URL signing
- [UrlGenerationConfiguration.php](src/Service/UrlGenerationConfiguration.php) - Patterns

### Repository
- [PostRepository.php](src/Repository/PostRepository.php) - Custom queries

### Enum
- [OrderStatusEnum.php](src/Enum/OrderStatusEnum.php) - Type-safe status

### Command
- [UrlGenerationCommand.php](src/Command/UrlGenerationCommand.php) - CLI examples

### Templates
- [templates/base.html.twig](templates/base.html.twig) - Base layout
- [templates/user/](templates/user/) - User registration templates
- [templates/post/](templates/post/) - CRUD templates
- [templates/lucky/](templates/lucky/) - Example templates

### Configuration
- [config/routes.yaml](config/routes.yaml) - Route definitions
- [config/services.yaml](config/services.yaml) - Service configuration
- [config/packages/doctrine.yaml](config/packages/doctrine.yaml) - Database config

## 📞 Troubleshooting & Getting Help

### Common Issues

**Q: Can't connect to database?**
- A: Check `.env.local` DATABASE_URL setting
- Run: `php bin/console doctrine:database:create`

**Q: Migrations fail?**
- A: Check if database exists first
- Run: `php bin/console doctrine:migrations:status`

**Q: Email verification not working?**
- A: In dev mode, check browser console for links
- Enable debug bar: `APP_DEBUG=true` in .env

**Q: Routes not working?**
- A: Clear cache: `php bin/console cache:clear`
- Check routing: `php bin/console debug:router`

### Documentation by Topic

| Topic | Quick Start | Full Guide | Reference |
|-------|-------------|-----------|-----------|
| **URL Generation** | [VISUAL_QUICK_START.md](VISUAL_QUICK_START.md) | [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md) | [URL_GENERATION_QUICK_REFERENCE.md](URL_GENERATION_QUICK_REFERENCE.md) |
| **User Registration** | [README.md](README.md#-user-registration) | [VISUAL_URL_GENERATION_GUIDE.md](VISUAL_URL_GENERATION_GUIDE.md) | [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md) |
| **CRUD Operations** | [CRUD_TESTING_GUIDE.md](CRUD_TESTING_GUIDE.md) | [src/Controller/PostController.php](src/Controller/PostController.php) | - |
| **Enums** | [ENUM_GUIDE.md](ENUM_GUIDE.md) | [src/Enum/OrderStatusEnum.php](src/Enum/OrderStatusEnum.php) | - |
| **Database** | [README_URL_GENERATION.md](README_URL_GENERATION.md) | [config/packages/doctrine.yaml](config/packages/doctrine.yaml) | - |
| **Services** | [URL_GENERATION_GUIDE.md](URL_GENERATION_GUIDE.md) | [src/Service/](src/Service/) | - |

### More Help
- [Symfony Official Documentation](https://symfony.com/doc/current/routing.html)
- [Symfony Best Practices](https://symfony.com/doc/current/best_practices.html)
- [Doctrine ORM Documentation](https://www.doctrine-project.org/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)

## 📝 Project Features

✅ **Core Features:**
- Symfony 7.4 with latest features
- Attribute-based routing (#[Route])
- Doctrine ORM with auto-migrations
- Service dependency injection
- Type-safe enums (PHP 8.1+)
- Full CRUD example with blog posts

✅ **Advanced Features:**
- Complete user registration & authentication
- Email verification with tokens
- Secure password reset workflow
- Signed URLs with expiration
- API endpoints with HATEOAS links
- Console commands for batch operations
- CSRF protection
- Session management

✅ **Developer Experience:**
- Comprehensive documentation (12+ files)
- Multiple learning paths (beginner to advanced)
- Working code examples for every pattern
- Testing guides and scripts
- Docker support for development
- Asset pipeline with Webpack/AssetMapper

## 🚀 Getting Started

### For Visual Learners
1. Read [VISUAL_QUICK_START.md](VISUAL_QUICK_START.md) (10 min)
2. View diagrams in [VISUAL_URL_GENERATION_GUIDE.md](VISUAL_URL_GENERATION_GUIDE.md)
3. Run the application and see it in action

### For Code-First Learners
1. Start with [src/Controller/PostController.php](src/Controller/PostController.php) (simple CRUD)
2. Then examine [src/Controller/UserRegistrationController.php](src/Controller/UserRegistrationController.php) (complex flow)
3. Study [src/Service/EmailNotificationService.php](src/Service/EmailNotificationService.php) (service pattern)

### For Documentation Learners
1. Read [00_START_HERE.md](00_START_HERE.md) (overview)
2. Follow [URL_GENERATION_LEARNING_PATH.md](URL_GENERATION_LEARNING_PATH.md) (structured progression)
3. Reference [URL_GENERATION_QUICK_REFERENCE.md](URL_GENERATION_QUICK_REFERENCE.md) while coding

### For Everyone
1. Install dependencies: `composer install`
2. Setup database: `php bin/console doctrine:migrations:migrate`
3. Run server: `php bin/console server:run`
4. Visit: http://localhost:8000

## 📄 License

This project is provided for educational purposes.

## 🤝 Contributing

This is an educational project. Feel free to fork, modify, and use it for your learning!

---

**Ready to Start?** 🎉

- **First time?** → Start with [00_START_HERE.md](00_START_HERE.md)
- **Want to code?** → Jump to [src/Controller/PostController.php](src/Controller/PostController.php)
- **Prefer visuals?** → See [VISUAL_QUICK_START.md](VISUAL_QUICK_START.md)
- **Need guidance?** → Follow [URL_GENERATION_LEARNING_PATH.md](URL_GENERATION_LEARNING_PATH.md)
- **Quick reference?** → Use [URL_GENERATION_QUICK_REFERENCE.md](URL_GENERATION_QUICK_REFERENCE.md)
© Made By Ouafik Mohammed 