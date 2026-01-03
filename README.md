# Symfony 7.4 URL Generation & User Registration Course

A comprehensive, **production-ready** course project demonstrating URL generation, user registration, email verification, and password reset workflows in **Symfony 7.4**.

## 📚 What This Project Teaches

This project is a **complete real-world example** that covers:

- ✅ **URL Generation Patterns** - All the ways to generate URLs in Symfony
- ✅ **User Registration Flow** - Complete workflow with validation
- ✅ **Email Verification** - Send verification links in emails
- ✅ **Password Reset** - Secure password reset with tokens
- ✅ **Database Integration** - Doctrine ORM with User entity
- ✅ **Service Layer** - Email notifications with URL generation
- ✅ **API HATEOAS** - REST API with hypermedia links
- ✅ **Security Best Practices** - Token validation, signed URLs

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

6. **Visit the application**
```
http://localhost:8000/user/register
```

## 📁 Project Structure

```
my_project_directory/
├── src/
│   ├── Controller/
│   │   └── UserRegistrationController.php      # Main controller with URL examples
│   ├── Entity/
│   │   └── User.php                            # User database entity
│   ├── Service/
│   │   └── EmailNotificationService.php        # Email service with URL generation
│   ├── Repository/
│   │   └── PostRepository.php
│   └── Kernel.php
├── templates/
│   └── user/
│       ├── register.html.twig                  # Registration form
│       ├── register_success.html.twig
│       ├── verify_error.html.twig
│       ├── password_reset_request.html.twig
│       ├── password_reset_form.html.twig
│       └── dashboard.html.twig
├── config/
│   ├── routes.yaml                             # Main routes
│   ├── services.yaml                           # Service configuration
│   └── packages/
│       ├── doctrine.yaml
│       ├── security.yaml
│       └── ... (other configs)
├── migrations/                                 # Database migrations
├── public/
│   └── index.php                              # Application entry point
├── URL_GENERATION_GUIDE.md                    # Detailed URL generation guide
├── CRUD_TESTING_GUIDE.md                      # CRUD testing examples
├── ENUM_GUIDE.md                              # PHP Enum examples
└── README.md                                  # This file
```

## 🎯 Core Examples

### 1. User Registration Flow

**Route:** `GET /user/register` → `POST /user/register`

Shows:
- Generating form submission URLs
- Creating new users
- Generating tokens
- Sending verification emails

```php
// Controller/UserRegistrationController.php
$user = new User();
$user->setUsername($username);
$user->setEmail($email);

$verificationLink = $this->generateUrl(
    'user_verify_email',
    ['userId' => $user->getId(), 'token' => $token],
    UrlGeneratorInterface::ABSOLUTE_URL
);

$this->emailService->sendWelcomeEmail($email, $username, $verificationLink);
```

### 2. Email Verification

**Route:** `GET /user/verify/{userId}/{token}`

Shows:
- Retrieving route parameters
- Token validation
- Updating user status
- Redirecting to dashboard

### 3. Password Reset

**Routes:**
- `GET /user/password-reset` - Request form
- `POST /user/password-reset` - Send reset email
- `GET /user/password-reset/{userId}/{token}` - Reset form
- `POST /user/password-reset/{userId}/{token}` - Update password

Shows:
- Secure token generation
- Email with absolute URLs
- Token expiration handling
- Password hashing

### 4. Service Layer URL Generation

**File:** `Service/EmailNotificationService.php`

Shows:
- Injecting UrlGeneratorInterface
- Generating URLs outside controllers
- Building email content with URLs
- Using different URL types (ABSOLUTE_URL, NETWORK_PATH)

```php
class EmailNotificationService
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function sendWelcomeEmail(string $email, string $username, string $verificationLink): void
    {
        $emailBody = sprintf(
            "Welcome %s!\n\nClick to verify: %s",
            $username,
            $verificationLink
        );
    }
}
```

### 5. API with HATEOAS Links

**Route:** `GET /user/api/profile`

Shows:
- ABSOLUTE_URL for external clients
- HATEOAS _links pattern
- RESTful API principles

```php
$data = [
    'id' => 1,
    '_links' => [
        'self' => $this->generateUrl('api_user_profile', [], ABSOLUTE_URL),
        'edit' => $this->generateUrl('api_user_update', ['id' => 1], ABSOLUTE_URL),
    ],
];
```

## 📖 Learning Path

### Step 1: Understand Basic URL Generation
- Read: [`URL_GENERATION_QUICK_REFERENCE.md`](URL_GENERATION_QUICK_REFERENCE.md)
- Focus: `generateUrl()` method, route names, parameters

### Step 2: Learn the User Registration Example
- Read: [`URL_GENERATION_GUIDE.md`](URL_GENERATION_GUIDE.md)
- Code: `Controller/UserRegistrationController.php`
- Run: Visit `/user/register` and follow the flow

### Step 3: Understand Database Integration
- Code: `Entity/User.php`
- Run: `php bin/console doctrine:migrations:migrate`
- Learn: Doctrine ORM mappings, entity relationships

### Step 4: Study the Service Layer
- Code: `Service/EmailNotificationService.php`
- Learn: Dependency injection, service layer patterns

### Step 5: Explore Advanced Patterns
- Read: [`VISUAL_URL_GENERATION_GUIDE.md`](VISUAL_URL_GENERATION_GUIDE.md)
- Topics: Signed URLs, locale-specific URLs, query parameters

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

## 🧪 Testing

### Run Migrations
```bash
php bin/console doctrine:migrations:migrate
```

### Run Tests
```bash
php bin/phpunit
```

### Use the Testing Scripts
```bash
# Windows PowerShell
.\test_crud.ps1

# Linux/Mac Bash
./test_crud.sh
```

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| [`URL_GENERATION_GUIDE.md`](URL_GENERATION_GUIDE.md) | Complete guide with step-by-step tutorials |
| [`URL_GENERATION_QUICK_REFERENCE.md`](URL_GENERATION_QUICK_REFERENCE.md) | Quick reference for common patterns |
| [`VISUAL_URL_GENERATION_GUIDE.md`](VISUAL_URL_GENERATION_GUIDE.md) | Visual diagrams and examples |
| [`URL_GENERATION_LEARNING_PATH.md`](URL_GENERATION_LEARNING_PATH.md) | Structured learning roadmap |
| [`README_URL_GENERATION.md`](README_URL_GENERATION.md) | Detailed URL generation reference |
| [`CRUD_TESTING_GUIDE.md`](CRUD_TESTING_GUIDE.md) | Guide to testing CRUD operations |
| [`ENUM_GUIDE.md`](ENUM_GUIDE.md) | PHP 8.1 Enums tutorial |

## 🛠️ Configuration

### Database
Located in: `config/packages/doctrine.yaml`

Default uses SQLite. To use MySQL/PostgreSQL:
1. Update `DATABASE_URL` in `.env.local`
2. Run migrations

### Routing
Located in: `config/routes.yaml`

All user-related routes are in: `config/routes/controllers.yaml`

### Services
Located in: `config/services.yaml`

Symfony auto-wires services by type-hinting in constructors.

## 🔒 Security Considerations

This project demonstrates security best practices:

- ✅ **Password Hashing** - Uses `PASSWORD_BCRYPT`
- ✅ **Token Generation** - Random 32-byte tokens
- ✅ **URL Signing** - Symmetric signing with expiration
- ✅ **Email Verification** - Requires token validation
- ✅ **CSRF Protection** - Via Symfony security
- ✅ **Input Validation** - Request validation examples

**⚠️ Note:** This is an educational project. In production:
- Add comprehensive input validation
- Implement proper email delivery
- Use environment-specific configurations
- Add rate limiting
- Implement proper token storage in database
- Add HTTPS enforcement

## 💡 Common Use Cases

### Generate Form Action URL
```php
$submitUrl = $this->generateUrl('user_register_submit');
```

### Generate Email Verification Link
```php
$verificationLink = $this->generateUrl(
    'user_verify_email',
    ['userId' => $userId, 'token' => $token],
    UrlGeneratorInterface::ABSOLUTE_URL
);
```

### Generate Redirect URL
```php
$dashboardUrl = $this->generateUrl('user_dashboard');
return new RedirectResponse($dashboardUrl);
```

### Generate URL in Service
```php
class EmailService {
    public function __construct(private UrlGeneratorInterface $urlGenerator) {}
    
    public function sendEmail() {
        $link = $this->urlGenerator->generate('verify_email', [], ABSOLUTE_URL);
    }
}
```

### Generate API Hypermedia Links
```php
$data = [
    '_links' => [
        'self' => $this->generateUrl('api_user', [], ABSOLUTE_URL),
        'all' => $this->generateUrl('api_users', [], ABSOLUTE_URL),
    ]
];
```

## 📞 Getting Help

1. **Stuck on URL generation?** → Read [`URL_GENERATION_QUICK_REFERENCE.md`](URL_GENERATION_QUICK_REFERENCE.md)
2. **Want to understand the full flow?** → Follow [`URL_GENERATION_LEARNING_PATH.md`](URL_GENERATION_LEARNING_PATH.md)
3. **Need visual explanations?** → Check [`VISUAL_URL_GENERATION_GUIDE.md`](VISUAL_URL_GENERATION_GUIDE.md)
4. **Want to test CRUD?** → See [`CRUD_TESTING_GUIDE.md`](CRUD_TESTING_GUIDE.md)

## 🎓 Learning Resources

- [Symfony Official Documentation](https://symfony.com/doc/current/routing.html)
- [Symfony Best Practices](https://symfony.com/doc/current/best_practices.html)
- [Doctrine ORM Documentation](https://www.doctrine-project.org/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)

## 📝 Project Features

- ✅ Symfony 7.4 with latest features
- ✅ Attribute-based routing (#[Route])
- ✅ Doctrine ORM with migrations
- ✅ Service dependency injection
- ✅ Email notification service
- ✅ User authentication flow
- ✅ Password reset workflow
- ✅ API HATEOAS links
- ✅ Docker support
- ✅ Comprehensive documentation

## 🚀 Next Steps

1. **Explore the code:**
   - Start with `Controller/UserRegistrationController.php`
   - Then check `Service/EmailNotificationService.php`

2. **Run the application:**
   - `php bin/console server:run`
   - Visit `http://localhost:8000/user/register`

3. **Follow the learning path:**
   - Read the guides in recommended order
   - Understand each URL generation pattern

4. **Modify and experiment:**
   - Add new routes
   - Create new email templates
   - Extend the user registration flow

## 📄 License

This project is provided for educational purposes.

## 🤝 Contributing

This is an educational project. Feel free to fork, modify, and use it for learning!

---

**Happy Learning! 🎉**

For detailed information, start with [`00_START_HERE.md`](00_START_HERE.md) or [`URL_GENERATION_QUICK_REFERENCE.md`](URL_GENERATION_QUICK_REFERENCE.md).
