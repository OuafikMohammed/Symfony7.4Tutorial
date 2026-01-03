# Complete Guide: Generating URLs in Symfony 7.4

## Table of Contents
1. [Introduction](#introduction)
2. [URL Generation in Controllers](#url-generation-in-controllers)
3. [URL Generation in Services](#url-generation-in-services)
4. [URL Generation in Commands](#url-generation-in-commands)
5. [Signing URLs for Security](#signing-urls-for-security)
6. [Complete Working Examples](#complete-working-examples)
7. [Common Mistakes and Solutions](#common-mistakes-and-solutions)

---

## Introduction

### What is URL Generation?

URL generation is the process of creating links (URLs) dynamically in your code. Instead of hardcoding URLs like `/blog/123`, Symfony lets you reference routes by name and automatically generates the correct URL.

**Why is this important?**

- **Flexibility**: If you change a route path, all generated URLs automatically update
- **Type Safety**: Catch URL-related errors at development time, not in production
- **Maintainability**: Your code is easier to refactor
- **Security**: You can add security features like signing URLs to prevent tampering

### Key Concepts to Remember

| Concept | Meaning |
|---------|---------|
| **Route** | A path in your application (e.g., `/blog/{id}`) with a unique name (e.g., `blog_show`) |
| **Route Name** | The identifier for a route (used when generating URLs) |
| **Route Parameters** | Variables in the URL (e.g., `{id}` in `/blog/{id}`) |
| **Absolute Path** | URL without domain (e.g., `/blog` - default type) |
| **Absolute URL** | Full URL with domain (e.g., `https://example.com/blog`) |

---

## URL Generation in Controllers

### When to Use This

Use URL generation in controllers when:
- You're redirecting users to another page
- You're passing URLs to templates
- You need URLs for conditional logic

### Prerequisites

Your controller **must extend `AbstractController`** to use the `generateUrl()` helper.

### Step 1: Basic URL Generation (No Parameters)

**File:** `src/Controller/UrlGenerationBlogController.php`

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UrlGenerationBlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_list')]
    public function list(): Response
    {
        // Generate a simple URL
        // This creates a URL for the 'user_register' route
        // Result: /user/register
        $signUpPage = $this->generateUrl('user_register');

        // Use it in a response
        return new Response('Sign up here: ' . $signUpPage);
    }
}
```

**How It Works:**
1. You call `$this->generateUrl('user_register')` with the route name
2. Symfony looks up the route named `user_register`
3. It returns the URL path for that route

### Step 2: URL Generation with Parameters

**Parameters replace placeholders in your route definition.**

First, define a route with parameters:

```php
// In your controller
#[Route('/user/{username}', name: 'user_profile')]
public function userProfile(string $username): Response
{
    return new Response('User: ' . $username);
}
```

Now generate a URL for this route:

```php
// Generate the URL with parameters
$userUrl = $this->generateUrl('user_profile', [
    'username' => 'john-doe'
]);
// Result: /user/john-doe
```

**Key Points:**
- Parameters are passed as an associative array (key-value pairs)
- The array keys must match the route parameter names
- Multiple parameters are separated by commas

### Step 3: Generating Absolute URLs (with Domain)

**Default behavior:** URLs are "absolute paths" (relative)

```php
// Default - returns just the path
$relativePath = $this->generateUrl('user_register');
// Result: /sign-up

// To get full URL with domain, use the third parameter
$fullUrl = $this->generateUrl('user_register', [], UrlGeneratorInterface::ABSOLUTE_URL);
// Result: https://example.com/sign-up
```

**When to Use:**
- **Absolute paths**: For internal links on your website
- **Absolute URLs**: For emails, APIs, webhooks, or external redirects

**URL Types Available:**

```php
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// Type 1: ABSOLUTE_PATH (default) - just the path
$url = $this->generateUrl('blog', [], UrlGeneratorInterface::ABSOLUTE_PATH);
// Result: /blog

// Type 2: ABSOLUTE_URL - full URL with protocol and domain
$url = $this->generateUrl('blog', [], UrlGeneratorInterface::ABSOLUTE_URL);
// Result: https://example.com/blog

// Type 3: NETWORK_PATH - protocol-relative URL
$url = $this->generateUrl('blog', [], UrlGeneratorInterface::NETWORK_PATH);
// Result: //example.com/blog (uses the browser's current protocol)
```

### Step 4: Multi-Language URLs (Localization)

When your app supports multiple languages, you can generate URLs in different languages:

```php
// Default - uses the current request's language
$englishUrl = $this->generateUrl('user_register');
// Result: /en/user/register (if English is current language)

// Generate Dutch version
$dutchUrl = $this->generateUrl('user_register', [
    '_locale' => 'nl'
]);
// Result: /nl/user/register

// Generate French version
$frenchUrl = $this->generateUrl('user_register', [
    '_locale' => 'fr'
]);
// Result: /fr/user/register
```

**Important:** The `_locale` parameter is special - it's always handled as a locale, not as a route parameter.

### Step 5: Extra Parameters (Query Strings)

Sometimes you need to pass parameters that aren't part of the route definition. These become query string parameters:

```php
// Route: /blog/{page}
// Generated with extra parameters:
$url = $this->generateUrl('blog', [
    'page' => 2,              // This replaces {page} in the route
    'category' => 'Symfony',  // This is extra - becomes ?category=Symfony
    'sort' => 'date',         // Another extra parameter
]);
// Result: /blog/2?category=Symfony&sort=date
```

**How Query Strings Work:**
- Route parameters replace placeholders: `{page}` → `2`
- Extra parameters become query strings: `?key=value&key2=value2`

### Step 6: Handling Objects in Parameters

When you pass objects, Symfony needs special handling:

```php
// For ROUTE parameters - automatic conversion to string
$userUrl = $this->generateUrl('user_profile', [
    'username' => $user  // Object is automatically converted to string
]);

// For EXTRA parameters - you must convert to string manually
$uuid = '550e8400-e29b-41d4-a716-446655440000';
$url = $this->generateUrl('blog_list', [
    'uuid' => (string) $uuid  // Must explicitly cast to string!
]);
```

---

## URL Generation in Services

### When to Use This

Use URL generation in services when:
- Sending emails (need full URLs with domain)
- Generating API responses
- Creating database records with URLs
- Working outside of HTTP request context

### Prerequisites

Your service is **not** extending AbstractController, so you need to inject the `UrlGeneratorInterface` service.

### Step 1: Setting Up Your Service with Dependency Injection

**File:** `src/Service/EmailNotificationService.php`

```php
<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailNotificationService
{
    // STEP 1: Inject UrlGeneratorInterface in constructor
    // Type-hint with UrlGeneratorInterface tells Symfony to inject the router
    // This is called "Autowiring" - it happens automatically
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * Send welcome email with verification link
     */
    public function sendWelcomeEmail(string $email, string $username, string $verificationLink): void
    {
        // STEP 2: Build email content with generated URL
        // The $verificationLink is already generated (passed from controller)
        $emailBody = sprintf(
            "Welcome %s!\n\n" .
            "Please confirm your email by clicking this link:\n" .
            "%s\n\n" .
            "Best regards,\n" .
            "The Team",
            htmlspecialchars($username),
            htmlspecialchars($verificationLink)
        );

        // STEP 3: Send the email (in real app)
        // $mailer->send($email, 'Welcome!', $emailBody);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $email, string $resetLink): void
    {
        // STEP 1: Build email content with password reset link
        $emailBody = sprintf(
            "Hello,\n\n" .
            "We received a request to reset your password.\n" .
            "Click the link below to reset your password:\n" .
            "%s\n\n" .
            "This link expires in 1 hour.\n\n" .
            "If you didn't request this, you can ignore this email.\n\n" .
            "Best regards,\n" .
            "The Team",
            htmlspecialchars($resetLink)
        );

        // STEP 2: Send the email (in real app)
        // $mailer->send($email, 'Password Reset', $emailBody);
    }
}
```

### How Dependency Injection Works

```php
// Step 1: Define what your service needs
public function __construct(
    private UrlGeneratorInterface $urlGenerator,  // Type hint the interface
) {
}

// Step 2: Symfony sees this type hint
// It knows that UrlGeneratorInterface is the router service

// Step 3: When creating your service, Symfony automatically injects it
// This is called "Autowiring" - it happens automatically
```

### Using the Service in Your Controller

```php
// In your controller
public function register(EmailNotificationService $emailService): Response
{
    // Symfony automatically injects the service
    $emailService->sendWelcomeEmail('user@example.com', 'John');
    
    return new Response('Welcome email sent!');
}
```

### Key Differences from Controllers

| Aspect | Controller | Service |
|--------|-----------|---------|
| URL generation helper | `$this->generateUrl()` | `$this->urlGenerator->generate()` |
| Must extend | `AbstractController` | Can be any class |
| When to use | Inside controller actions | Business logic, emails, APIs |
| Injection | Inherited from parent | Injected in constructor |

---

## URL Generation in Commands

### When to Use This

Use URL generation in console commands when:
- Sending batch emails from a scheduled task
- Generating URLs for reports
- Processing webhooks
- Sending notifications via background jobs

### Prerequisites and Special Considerations

Commands run **outside the HTTP context** - there's no web request. This means:
- No current domain is available
- URLs use `http://localhost/` by default
- You need to configure the `default_uri` to use your real domain

### Configuration Step

**File:** `config/services.yaml`

Add this configuration to set your default domain for commands:

```yaml
# config/services.yaml
framework:
    router:
        # This domain is used when generating URLs in commands
        # (outside of HTTP request context)
        default_uri: 'https://example.com/'
```

Or using PHP configuration:

**File:** `config/packages/routing.php`

```php
<?php

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->router()
        ->defaultUri('https://example.com/');
};
```

### Command Example

**File:** `src/Command/UrlGenerationCommand.php`

```php
<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsCommand(
    name: 'app:send-batch-emails',
    description: 'Send batch verification emails to all users'
)]
class UrlGenerationCommand extends Command
{
    // Inject the router service (same as in services)
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
    }

    protected function execute(SymfonyStyle $io): int
    {
        $io->title('Sending Batch Emails');

        // Generate a verification link for a user
        $verificationLink = $this->urlGenerator->generate(
            'email_verify',
            ['userId' => 123],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // With default_uri configured, this becomes:
        // https://example.com/verify?userId=123

        $io->writeln('Generated link: ' . $verificationLink);

        return Command::SUCCESS;
    }
}
```

### Running Commands

```bash
# Run the command
php bin/console app:send-batch-emails

# Output will show:
# Generated link: https://example.com/verify?userId=123
```

### Practical Example: Bulk Email Generation

```php
protected function execute(SymfonyStyle $io): int
{
    // List of users to email
    $users = [
        ['id' => 1, 'username' => 'alice'],
        ['id' => 2, 'username' => 'bob'],
        ['id' => 3, 'username' => 'charlie'],
    ];

    $table = [];

    foreach ($users as $user) {
        // Generate a unique verification link for each user
        $verificationLink = $this->urlGenerator->generate(
            'email_verify',
            ['userId' => $user['id']],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $table[] = [
            $user['username'],
            $verificationLink,
        ];
    }

    // Display in a nice table
    $io->table(['Username', 'Verification Link'], $table);

    return Command::SUCCESS;
}
```

---

## Signing URLs for Security

### Why Sign URLs?

A signed URL includes a cryptographic hash that proves:
1. **Authenticity**: The URL really came from your server
2. **Integrity**: The URL hasn't been tampered with
3. **Expiration**: The URL hasn't expired (if you set an expiration)

**Use Cases:**
- Password reset links
- Email verification links
- Temporary download links
- Sensitive actions that need verification

### How Signed URLs Work

```
Normal URL:     https://example.com/password-reset?userId=5
Signed URL:     https://example.com/password-reset?userId=5&_hash=abc123def456&_expiration=1704067200
                                                     ^                      ^
                                                  signature              expiration timestamp
```

### Basic Signing Example

**File:** `src/Service/SignedUrlService.php`

```php
<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\UriSigner;

class SignedUrlService
{
    // Inject the UriSigner service
    public function __construct(
        private UriSigner $uriSigner,
    ) {
    }

    // Generate a simple signed URL
    public function generatePasswordResetLink(int $userId): string
    {
        $url = 'https://example.com/password-reset?userId=' . $userId;

        // Sign the URL - adds the _hash parameter
        $signedUrl = $this->uriSigner->sign($url);

        return $signedUrl;
        // Result: https://example.com/password-reset?userId=1&_hash=e4a21b9...
    }
}
```

### Signing with Expiration

**Expiration ensures the URL can't be used after a certain time.**

```php
// Sign with 1-hour expiration
$signedUrl = $this->uriSigner->sign(
    $url,
    new \DateInterval('PT1H')  // PT = Period Time, 1H = 1 Hour
);
// Result: ...&_expiration=1704067200&_hash=...

// Common expiration periods:
new \DateInterval('PT30M')   // 30 minutes
new \DateInterval('PT1H')    // 1 hour
new \DateInterval('PT24H')   // 24 hours
new \DateInterval('P7D')     // 7 days (P = Period, D = Days)
new \DateInterval('P30D')    // 30 days
```

### Verifying Signed URLs

```php
// Simple check - returns true/false
if ($this->uriSigner->check($signedUrl)) {
    // The URL is valid and not expired
    echo "URL is valid";
} else {
    // The URL is either invalid or expired
    echo "URL is invalid";
}
```

### Detailed Error Reporting

```php
use Symfony\Component\HttpFoundation\Exception\ExpiredSignedUriException;
use Symfony\Component\HttpFoundation\Exception\UnsignedUriException;
use Symfony\Component\HttpFoundation\Exception\UnverifiedSignedUriException;

try {
    // Try to verify the URL
    $this->uriSigner->verify($signedUrl);
    
    // If we get here, the URL is valid
    echo "URL is valid";

} catch (UnsignedUriException $e) {
    // The URL doesn't have a signature
    echo "URL is not signed";

} catch (UnverifiedSignedUriException $e) {
    // The signature is invalid (tampering detected)
    echo "URL signature is invalid";

} catch (ExpiredSignedUriException $e) {
    // The URL has expired
    echo "URL has expired";
}
```

### Complete Example: Password Reset Flow

```php
// Step 1: Generate the reset link (in a service)
$resetLink = $this->signedUrlService->generatePasswordResetLink(
    userId: 123,
    expiresIn: new \DateInterval('PT1H')  // Valid for 1 hour
);

// Step 2: Send the link in an email to the user

// Step 3: User clicks the link, request comes to this controller
#[Route('/password-reset', name: 'password_reset')]
public function resetPassword(Request $request): Response
{
    $signedUrl = $request->getUri();
    
    // Verify the URL before processing
    try {
        $this->uriSigner->verify($signedUrl);
        
        // URL is valid - safe to process password reset
        // Update the user's password
        
        return new Response('Password updated successfully');
        
    } catch (ExpiredSignedUriException) {
        return new Response('Reset link has expired', Response::HTTP_GONE);
    } catch (UnverifiedSignedUriException) {
        return new Response('Invalid reset link', Response::HTTP_BAD_REQUEST);
    }
}
```

---

## Complete Working Examples

### Example 1: Blog Application with URL Generation

**Scenario:** You're building a blog and need to generate URLs in various places.

**Step 1: Define Routes**

```php
// In your controller
#[Route('/blog', name: 'blog_list')]
public function list(): Response { /* ... */ }

#[Route('/blog/{slug}', name: 'blog_show')]
public function show(string $slug): Response { /* ... */ }

#[Route('/blog/{id}/edit', name: 'blog_edit')]
public function edit(int $id): Response { /* ... */ }
```

**Step 2: Generate URLs in Controller**

```php
public function list(): Response
{
    // Create URLs for the first blog post
    $post1Url = $this->generateUrl('blog_show', [
        'slug' => 'first-post'
    ]);
    // Result: /blog/first-post

    $editUrl = $this->generateUrl('blog_edit', [
        'id' => 1
    ]);
    // Result: /blog/1/edit

    // Pass to template
    return $this->render('blog/list.html.twig', [
        'post1Url' => $post1Url,
        'editUrl' => $editUrl,
    ]);
}
```

**Step 3: Use in Twig Template**

```twig
{# In blog/list.html.twig #}
<h1>Blog Posts</h1>

<a href="{{ post1Url }}">Read First Post</a>
<a href="{{ editUrl }}">Edit Post</a>
```

### Example 2: Email Service with Verification Links

**Scenario:** Send a welcome email with a unique verification link to each user.

```php
// src/Service/RegistrationService.php
class RegistrationService
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private Mailer $mailer,
    ) {
    }

    public function registerUser(string $email, string $username): void
    {
        // Save the user to database
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        // ... save to database

        // Generate verification link
        $verificationToken = bin2hex(random_bytes(32));  // Random token
        $verificationUrl = $this->urlGenerator->generate(
            'verify_email',
            ['token' => $verificationToken],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // Send email
        $email = (new Email())
            ->to($email)
            ->subject('Verify your email')
            ->html(sprintf(
                '<p>Click here to verify: <a href="%s">Verify Email</a></p>',
                $verificationUrl
            ));

        $this->mailer->send($email);
    }
}
```

### Example 3: Generating Multiple URLs

```php
// Generate a set of URLs for different purposes
public function generateSecurityLinks(User $user): array
{
    return [
        'profile' => $this->generateUrl('user_profile', [
            'username' => $user->getUsername()
        ]),
        'settings' => $this->generateUrl('user_settings', [
            'userId' => $user->getId()
        ]),
        'logout' => $this->generateUrl('logout'),
    ];
}
```

---

## Complete Real-World Example: User Registration Flow

This is a step-by-step walkthrough of how URL generation is used in a complete user registration and password reset flow.

### Overview of the Flow

1. User registers on the website
2. Verification email is sent with a signed link
3. User clicks link to verify their email
4. User can reset password using another signed link

### File Structure

- **Controller**: `src/Controller/UserRegistrationController.php` - Orchestrates the flow
- **Service**: `src/Service/EmailNotificationService.php` - Sends emails with generated URLs
- **Entity**: `src/Entity/User.php` - User data model

---

### Step 1: User Registration Form

**Goal:** Generate a URL for where the registration form should submit

```php
#[Route('/register', name: 'user_register', methods: ['GET'])]
public function showRegisterForm(): Response
{
    // STEP 1: Generate the URL where the form will POST to
    $submitUrl = $this->generateUrl('user_register_submit');
    // Result: /user/register

    // STEP 2: Pass the URL to the template
    return $this->render('user/register.html.twig', [
        'submitUrl' => $submitUrl,
    ]);
}
```

**Template Usage:**

```html
<form method="POST" action="{{ submitUrl }}">
    <input type="text" name="username" required>
    <input type="email" name="email" required>
    <input type="password" name="password" required>
    <button type="submit">Register</button>
</form>
```

---

### Step 2: Handle Registration & Generate Verification Link

**Goal:** Create a verification link and send it via email

```php
#[Route('/register', name: 'user_register_submit', methods: ['POST'])]
public function handleRegistration(Request $request): Response
{
    // Get form data
    $username = $request->request->get('username');
    $email = $request->request->get('email');
    $password = $request->request->get('password');

    // STEP 1: Create and save new user
    $user = new User();
    $user->setUsername($username);
    $user->setEmail($email);
    $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
    $user->setVerified(false);  // Not verified yet
    // TODO: Save to database: $this->getDoctrine()->getManager()->persist($user);

    // STEP 2: Generate verification link
    // This link will be sent in the email
    // When user clicks it, they'll hit the verifyEmail() action
    $token = bin2hex(random_bytes(32));  // Random token for security
    $verificationLink = $this->generateUrl(
        'user_verify_email',
        [
            'userId' => $user->getId() ?? 0,  // Use placeholder if not yet persisted
            'token' => $token,
        ],
        UrlGeneratorInterface::ABSOLUTE_URL  // IMPORTANT: Full URL for email
    );
    // Result: https://example.com/user/verify/123/abc123def456...

    // STEP 3: Send verification email
    $this->emailService->sendWelcomeEmail(
        email: $email,
        username: $username,
        verificationLink: $verificationLink  // Pass the generated URL to the service
    );

    // STEP 4: Redirect to success page
    $successUrl = $this->generateUrl('user_register_success');
    
    return new RedirectResponse($successUrl);
}
```

---

### Step 3: Verify Email (User Clicks Link)

**Goal:** Process the verification link user clicked in email

```php
#[Route('/verify/{userId}/{token}', name: 'user_verify_email')]
public function verifyEmail(int $userId, string $token): Response
{
    // STEP 1: Find user by ID from the URL parameter
    $user = $this->getUserById($userId);  // Fetch from database
    if (!$user) {
        // Generate error page URL if user not found
        $errorUrl = $this->generateUrl('user_verify_error');
        return new RedirectResponse($errorUrl);
    }

    // STEP 2: Verify the token
    // In real app: check token against database
    if (!$this->validateToken($user, $token)) {
        // Token is invalid - redirect to error
        $errorUrl = $this->generateUrl('user_verify_error');
        return new RedirectResponse($errorUrl);
    }

    // STEP 3: Mark user as verified
    $user->setVerified(true);
    // TODO: Save to database: $this->getDoctrine()->getManager()->flush();

    // STEP 4: Redirect to dashboard
    $dashboardUrl = $this->generateUrl('user_dashboard');

    return new RedirectResponse($dashboardUrl);
}
```

---

### Step 4: Password Reset Request Form

**Goal:** Generate form action URL for password reset request

```php
#[Route('/password-reset', name: 'password_reset_request', methods: ['GET'])]
public function showPasswordResetRequestForm(): Response
{
    // STEP 1: Generate form submission URL
    $submitUrl = $this->generateUrl('password_reset_submit');
    // Result: /user/password-reset

    // STEP 2: Pass to template
    return $this->render('user/password_reset_request.html.twig', [
        'submitUrl' => $submitUrl,
    ]);
}
```

---

### Step 5: Handle Password Reset Request & Generate Reset Link

**Goal:** Generate password reset link and send it via email

```php
#[Route('/password-reset', name: 'password_reset_submit', methods: ['POST'])]
public function handlePasswordResetRequest(Request $request): Response
{
    $email = $request->request->get('email');

    // STEP 1: Find user by email
    $user = $this->findUserByEmail($email);

    if ($user) {
        // STEP 2: Generate password reset link
        // This is a signed URL that expires in 1 hour
        $resetToken = bin2hex(random_bytes(32));
        $resetLink = $this->generateUrl(
            'password_reset_form',
            [
                'userId' => $user->getId(),
                'token' => $resetToken,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL  // Full URL for email
        );
        // Result: https://example.com/user/password-reset/123/xyz789...

        // STEP 3: Send password reset email
        $this->emailService->sendPasswordResetEmail(
            email: $email,
            resetLink: $resetLink  // Pass the generated URL to service
        );
    }

    // STEP 4: Redirect to confirmation page
    // Always show success, even if email doesn't exist (security best practice)
    $confirmationUrl = $this->generateUrl('password_reset_confirmation');

    return new RedirectResponse($confirmationUrl);
}
```

---

### Step 6: Process Password Reset (User Clicks Link)

**Goal:** Validate reset link and allow user to set new password

```php
#[Route('/password-reset/{userId}/{token}', name: 'password_reset_form')]
public function showPasswordResetForm(int $userId, string $token): Response
{
    // STEP 1: Verify the token is valid and not expired
    if (!$this->validateResetToken($userId, $token)) {
        // Token expired or invalid - redirect to new request
        $newRequestUrl = $this->generateUrl('password_reset_request');
        return new RedirectResponse($newRequestUrl);
    }

    // STEP 2: Generate form submission URL with the token
    $submitUrl = $this->generateUrl('password_reset_update', [
        'userId' => $userId,
        'token' => $token,
    ]);
    // Result: /user/password-reset/123/xyz789... (POST action)

    // STEP 3: Display password reset form
    return $this->render('user/password_reset_form.html.twig', [
        'submitUrl' => $submitUrl,
    ]);
}
```

---

### Step 7: Update Password

**Goal:** Validate token and update user password

```php
#[Route('/password-reset/{userId}/{token}', name: 'password_reset_update', methods: ['POST'])]
public function updatePassword(int $userId, string $token, Request $request): Response
{
    // STEP 1: Verify token again
    if (!$this->validateResetToken($userId, $token)) {
        $errorUrl = $this->generateUrl('password_reset_error');
        return new RedirectResponse($errorUrl);
    }

    // STEP 2: Get new password from form
    $newPassword = $request->request->get('password');

    // STEP 3: Update user password
    $user = $this->getUserById($userId);
    if (!$user) {
        $errorUrl = $this->generateUrl('password_reset_error');
        return new RedirectResponse($errorUrl);
    }
    $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
    // TODO: Save to database: $this->getDoctrine()->getManager()->flush();

    // STEP 4: Redirect to success page
    $successUrl = $this->generateUrl('password_reset_success');

    return new RedirectResponse($successUrl);
}
```

---

### Key Takeaways from This Example

1. **Use `ABSOLUTE_URL` for emails** - External services need the full domain
2. **Generate tokens for security** - Use `bin2hex(random_bytes(32))`
3. **Pass URLs to services** - Controllers generate, services use
4. **Validate on retrieval** - Always check tokens when URLs are used
5. **Redirect after actions** - Use generated URLs in `RedirectResponse`
6. **Pass URLs to templates** - Controllers generate, templates render
7. **Use named routes** - Reference by name, not hardcoded paths

---

## Common Mistakes and Solutions

### ❌ Mistake 1: Using Hardcoded URLs

```php
// DON'T DO THIS!
$url = '/blog/123';  // Hardcoded - breaks if you change the route

// DO THIS INSTEAD
$url = $this->generateUrl('blog_show', ['id' => 123]);
```

**Why?** If you change your route structure, all hardcoded URLs break.

---

### ❌ Mistake 2: Using Relative URLs in Emails

```php
// DON'T DO THIS!
$emailBody = "Click here: /verify?token=abc123";
// User won't know which domain this refers to

// DO THIS INSTEAD
$verificationUrl = $this->generateUrl('email_verify', [
    'token' => 'abc123'
], UrlGeneratorInterface::ABSOLUTE_URL);
// Result: https://example.com/verify?token=abc123
```

**Why?** Email clients don't know your domain. You need the full URL.

---

### ❌ Mistake 3: Forgetting the Type Hint in Services

```php
// DON'T DO THIS!
public function __construct($urlGenerator) {
    // Symfony won't know what service to inject
}

// DO THIS INSTEAD
public function __construct(UrlGeneratorInterface $urlGenerator) {
    // Symfony automatically provides the router service
}
```

**Why?** Symfony uses type hints to know which service to inject (autowiring).

---

### ❌ Mistake 4: Not Converting Objects to Strings

```php
// DON'T DO THIS!
$url = $this->generateUrl('show', [
    'uuid' => $entity->getUuid()  // Might be an object
]);

// DO THIS INSTEAD
$url = $this->generateUrl('show', [
    'uuid' => (string) $entity->getUuid()  // Explicitly convert
]);
```

**Why?** Route parameters must be strings. Objects need explicit conversion.

---

### ❌ Mistake 5: Using Relative URLs for External Services

```php
// DON'T DO THIS!
$webhookUrl = '/webhook/order-created';  // Relative - external service won't know the domain

// DO THIS INSTEAD
$webhookUrl = $this->generateUrl('webhook_order', [], UrlGeneratorInterface::ABSOLUTE_URL);
// Result: https://example.com/webhook/order-created
```

**Why?** External services need the full domain to reach your webhook endpoint.

---

### ❌ Mistake 6: Not Configuring default_uri for Commands

```bash
# DON'T RUN COMMANDS WITHOUT CONFIGURATION!
php bin/console app:send-emails
# Results in: http://localhost/verify  (WRONG!)

# DO THIS FIRST - Configure default_uri
# In config/services.yaml or config/packages/routing.php
```

**Why?** Commands generate localhost URLs by default. Configuration fixes this.

---

## Quick Reference Table

### Available URL Types

| Type | Example | Use Case |
|------|---------|----------|
| `ABSOLUTE_PATH` (default) | `/blog` | Internal website links |
| `ABSOLUTE_URL` | `https://example.com/blog` | Emails, APIs, external services |
| `NETWORK_PATH` | `//example.com/blog` | Mixed http/https environments |

### Common Parameters

| Parameter | Example | Purpose |
|-----------|---------|---------|
| Route parameters | `['id' => 5]` | Replace placeholders in route |
| Extra parameters | `['page' => 2]` | Added as query string `?page=2` |
| `_locale` | `['_locale' => 'nl']` | Generate URL for specific language |

### Expiration Intervals

| Interval | Meaning |
|----------|---------|
| `PT30M` | 30 minutes |
| `PT1H` | 1 hour |
| `PT24H` | 24 hours |
| `P7D` | 7 days |
| `P30D` | 30 days |

---

## Testing Your Implementation

### Test 1: Basic URL Generation

```php
// In a test
public function testBlogUrlGeneration(): void
{
    $url = $this->urlGenerator->generate('blog_show', ['id' => 1]);
    $this->assertEquals('/blog/1', $url);
}
```

### Test 2: Absolute URLs

```php
public function testAbsoluteUrl(): void
{
    $url = $this->urlGenerator->generate(
        'blog_show',
        ['id' => 1],
        UrlGeneratorInterface::ABSOLUTE_URL
    );
    $this->assertStringContainsString('https://example.com', $url);
}
```

### Test 3: Signed URLs

```php
public function testSignedUrl(): void
{
    $url = 'https://example.com/reset?id=1';
    $signed = $this->uriSigner->sign($url);
    
    // Verify it's valid
    $this->assertTrue($this->uriSigner->check($signed));
    
    // Tampering should make it invalid
    $tampered = str_replace('id=1', 'id=2', $signed);
    $this->assertFalse($this->uriSigner->check($tampered));
}
```

---

## Summary

✅ **Use `generateUrl()` in controllers** - when extending AbstractController  
✅ **Use `$urlGenerator->generate()` in services** - inject UrlGeneratorInterface  
✅ **Use `ABSOLUTE_URL` in emails and APIs** - not just relative paths  
✅ **Sign sensitive URLs** - for password resets, email verification, etc.  
✅ **Configure `default_uri`** - for console commands to work correctly  

By following these patterns, your URL generation will be:
- **Flexible**: Easy to refactor routes
- **Secure**: Signed URLs prevent tampering  
- **Maintainable**: Clear, testable code
- **Reliable**: No broken links
