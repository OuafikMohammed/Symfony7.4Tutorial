# URL Generation Quick Reference Card

## 🚀 Quick Start - 3 Scenarios

### Scenario 1: In a Controller (Extending AbstractController)
```php
$url = $this->generateUrl('route_name', ['param' => 'value']);
```

### Scenario 2: In a Service
```php
public function __construct(private UrlGeneratorInterface $urlGenerator) {}

$url = $this->urlGenerator->generate('route_name', ['param' => 'value']);
```

### Scenario 3: In a Command
```php
public function __construct(private UrlGeneratorInterface $urlGenerator) {
    parent::__construct();
}

$url = $this->urlGenerator->generate('route_name', [], UrlGeneratorInterface::ABSOLUTE_URL);
// First configure: config/services.yaml -> framework.router.default_uri
```

---

## 📋 Method Signatures

| Location | Signature | Returns |
|----------|-----------|---------|
| Controller | `generateUrl(name, params=[], type=ABSOLUTE_PATH)` | string |
| Service | `$urlGenerator->generate(name, params=[], type=ABSOLUTE_PATH)` | string |
| Command | `$urlGenerator->generate(name, params=[], type=ABSOLUTE_PATH)` | string |

---

## 🎯 URL Types

| Type | Code | Example |
|------|------|---------|
| **Relative** (default) | `ABSOLUTE_PATH` or omit | `/blog` |
| **Full URL** | `ABSOLUTE_URL` | `https://example.com/blog` |
| **Protocol-relative** | `NETWORK_PATH` | `//example.com/blog` |

**When to use:**
- **ABSOLUTE_PATH**: Internal links, internal redirects
- **ABSOLUTE_URL**: Emails, external APIs, webhooks, exports
- **NETWORK_PATH**: When you need to switch between http/https

---

## 🔑 Parameter Types

### Route Parameters (Replace {placeholders})
```php
// Route: /blog/{slug}
$url = $this->generateUrl('blog_show', ['slug' => 'my-post']);
// Result: /blog/my-post
```

### Extra Parameters (Become Query String)
```php
// Route: /blog
$url = $this->generateUrl('blog_list', ['page' => 2, 'sort' => 'date']);
// Result: /blog?page=2&sort=date
```

### Locale Parameter (Special - for multi-language)
```php
$url = $this->generateUrl('user_register', ['_locale' => 'nl']);
// Result: /nl/sign-up (or similar depending on your routing)
```

---

## ✍️ Signing URLs (Security)

### Basic Signing
```php
use Symfony\Component\HttpFoundation\UriSigner;

public function __construct(private UriSigner $uriSigner) {}

$url = 'https://example.com/reset?id=5';
$signed = $this->uriSigner->sign($url);
// Result: https://example.com/reset?id=5&_hash=abc123...
```

### Signing with Expiration
```php
// Expires in 1 hour
$signed = $this->uriSigner->sign($url, new \DateInterval('PT1H'));

// Expires at specific date
$signed = $this->uriSigner->sign($url, new \DateTimeImmutable('2025-12-31'));

// Common durations:
// PT30M = 30 minutes
// PT1H = 1 hour
// PT24H = 24 hours
// P7D = 7 days
// P30D = 30 days
```

### Verifying Signed URLs
```php
// Simple check
if ($this->uriSigner->check($signedUrl)) {
    // URL is valid and not expired
}

// Detailed errors
try {
    $this->uriSigner->verify($signedUrl);
} catch (ExpiredSignedUriException) {
    // URL expired
} catch (UnverifiedSignedUriException) {
    // Signature is invalid
} catch (UnsignedUriException) {
    // URL is not signed
}
```

---

## 🛠️ Configuration

### Set Domain for Console Commands
**File:** `config/packages/routing.php`
```php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->router()->defaultUri('https://example.com/');
};
```

### Force HTTPS
**File:** `config/services.yaml`
```yaml
parameters:
    router.request_context.scheme: 'https'
    asset.request_context.secure: true
```

### Per-Route HTTPS
**File:** `config/routes.php`
```php
$routes->add('login', '/login')
    ->controller([SecurityController::class, 'login'])
    ->schemes(['https'])
;
```

---

## 📝 Common Patterns

### Email Verification
```php
$verificationLink = $this->urlGenerator->generate(
    'email_verify',
    ['token' => $token],
    UrlGeneratorInterface::ABSOLUTE_URL  // Always ABSOLUTE_URL for emails!
);

// Send in email: $verificationLink
```

### Password Reset
```php
$resetLink = $this->urlGenerator->generate(
    'password_reset',
    ['token' => $resetToken],
    UrlGeneratorInterface::ABSOLUTE_URL
);

// Sign and set expiration
$signedResetLink = $this->uriSigner->sign(
    $resetLink,
    new \DateInterval('PT1H')  // Valid 1 hour
);
```

### Pagination
```php
$prevPage = $this->generateUrl('blog_list', ['page' => $page - 1]);
$nextPage = $this->generateUrl('blog_list', ['page' => $page + 1]);
```

### Filters/Search
```php
$filterUrl = $this->generateUrl('products', [
    'category' => 'electronics',
    'brand' => 'sony',
    'price_min' => 100,
]);
// Result: /products?category=electronics&brand=sony&price_min=100
```

---

## ❌ Common Mistakes

| ❌ Wrong | ✅ Correct |
|---------|-----------|
| `$url = '/blog/5'` | `$url = $this->generateUrl('blog_show', ['id' => 5])` |
| Email: `generateUrl('verify')` | Email: `generateUrl('verify', [], ABSOLUTE_URL)` |
| Service without injection | Service: `__construct(UrlGeneratorInterface $gen)` |
| `['uuid' => $obj]` in extra params | `['uuid' => (string)$obj]` |
| Command without `default_uri` config | Command: Configure `routing.default_uri` first |

---

## 🧪 Testing

```php
public function testUrlGeneration(): void
{
    $url = $this->urlGenerator->generate('blog_show', ['id' => 1]);
    $this->assertEquals('/blog/1', $url);
}

public function testAbsoluteUrl(): void
{
    $url = $this->urlGenerator->generate(
        'blog_show',
        ['id' => 1],
        UrlGeneratorInterface::ABSOLUTE_URL
    );
    $this->assertStringContainsString('https://example.com', $url);
}

public function testSignedUrl(): void
{
    $url = 'https://example.com/reset?id=1';
    $signed = $this->uriSigner->sign($url);
    $this->assertTrue($this->uriSigner->check($signed));
}
```

---

## 🔐 Security Checklist

- ✅ Use route names, never hardcode URLs
- ✅ Use ABSOLUTE_URL for emails/APIs, not just paths
- ✅ Sign sensitive URLs (password reset, verification)
- ✅ Set expiration on signed URLs
- ✅ Validate parameters before using
- ✅ Use HTTPS for sensitive operations
- ✅ Don't expose sensitive data in URLs
- ✅ Use POST for sensitive operations

---

## 📚 When to Use What

| Need | Use | Location |
|------|-----|----------|
| Redirect user | `generateUrl()` | Controller |
| Send email link | `generate() + ABSOLUTE_URL` | Service |
| API response | `generate() + ABSOLUTE_URL` | Service |
| Password reset | `generate() + sign()` | Service |
| Batch processing | `generate() + ABSOLUTE_URL` | Command |
| Template variable | `generateUrl()` then pass to Twig | Controller |

---

## 🎓 Order to Learn

1. Start with simple `generateUrl()` in controllers
2. Learn to inject in services
3. Add parameters to routes
4. Switch to ABSOLUTE_URL for emails
5. Sign URLs for security
6. Use commands for batch operations
7. Add expiration to signed URLs

---

## 🆘 Troubleshooting

| Problem | Solution |
|---------|----------|
| Route not found | Check route name matches exactly (case-sensitive) |
| localhost in command | Configure `default_uri` in routing config |
| Objects not working in params | Cast to string: `(string)$object` |
| Email link shows /path only | Use `ABSOLUTE_URL` not `ABSOLUTE_PATH` |
| Signature invalid | Check URL hasn't been modified, check expiration |
| Service injection fails | Add type hint: `UrlGeneratorInterface $urlGenerator` |

