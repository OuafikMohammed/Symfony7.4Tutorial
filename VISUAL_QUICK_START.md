# URL Generation - Visual Quick Start Guide

## 📍 Where Are The Files?

```
my_project_directory/
├── 📄 README_URL_GENERATION.md                    ← START HERE (Overview)
├── 📄 URL_GENERATION_GUIDE.md                    ← Read this first (Full Tutorial)
├── 📄 URL_GENERATION_QUICK_REFERENCE.md          ← Quick lookup (Cheat Sheet)
├── 📄 URL_GENERATION_LEARNING_PATH.md            ← Learning Order (Roadmap)
└── src/
    ├── Controller/
    │   ├── UrlGenerationBlogController.php       ← Simple Examples
    │   └── UserRegistrationController.php        ← Complete Real-World App
    └── Service/
        ├── EmailNotificationService.php          ← Service Pattern
        ├── SignedUrlService.php                  ← Security Pattern
        └── UrlGenerationConfiguration.php        ← Configuration Reference
```

## 🔄 The 3-Step URL Generation Pattern

### Step 1: Define a Route
```php
#[Route('/blog/{id}', name: 'blog_show')]
public function show(int $id): Response { }
```

### Step 2: Generate the URL
```php
$url = $this->generateUrl('blog_show', ['id' => 5]);
```

### Step 3: Use the URL
```php
// In a redirect
return new RedirectResponse($url);

// In a template
return $this->render('template.html', ['url' => $url]);

// In an email
$email->setBody("Click here: " . $url);
```

## 🎯 Which Method to Use?

```
┌─────────────────────────────────────────────────────┐
│         Where Am I Writing Code?                    │
└──────────────┬──────────────────────────────────────┘
               │
        ┌──────┴──────────┬──────────────┐
        │                 │              │
    Controller          Service       Command
        │                 │              │
        ▼                 ▼              ▼
   $this->                $urlGenerator-> $urlGenerator->
   generateUrl()          generate()      generate()
   
   (Must extend          (Inject via    (Inject via
   AbstractController)   constructor)   constructor)
```

## 📊 URL Types at a Glance

```
generateUrl(name, params, TYPE)
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
    ABSOLUTE_PATH      ABSOLUTE_URL      NETWORK_PATH
        │                  │                  │
    /blog              https://             //
                      example.com/blog    example.com/blog
        │                  │                  │
    For internal       For emails,         For mixed
    links              APIs, webhooks      http/https
```

## 🚀 Common Scenarios Quick Map

```
┌─ Scenario ─────────────────┬─ What to Do ────────────────┬─ Type ─────────────┐
├─ Show article              │ generateUrl('article_show') │ ABSOLUTE_PATH      │
├─ Send email link           │ generateUrl(..., ABSOLUTE_URL) │ ABSOLUTE_URL   │
├─ API response              │ generateUrl(..., ABSOLUTE_URL) │ ABSOLUTE_URL   │
├─ Password reset            │ sign() the URL              │ ABSOLUTE_URL       │
├─ Form submission           │ generateUrl('form_submit')  │ ABSOLUTE_PATH      │
├─ Redirect after action     │ new RedirectResponse(url)   │ ABSOLUTE_PATH      │
├─ Pass to template          │ ['url' => generateUrl(...)] │ ABSOLUTE_PATH      │
└─ Command batch operation   │ generateUrl(..., ABSOLUTE_URL) │ ABSOLUTE_URL   │
```

## 🔐 Security Decision Tree

```
                    Is this a sensitive URL?
                            │
                    ┌───────┴────────┐
                   NO               YES
                    │                │
            Don't sign           ┌───▼───────┐
            it                   │           │
                           Set Expiration?
                                 │
                         ┌───────┴────────┐
                        NO              YES
                         │               │
                    Sign only      Sign + Expiration
                    (permanent)    (time-limited)
                         │               │
            Password Reset/Email  Password Reset (1h)
            Verification (very    Email Verify (24h)
            sensitive)            Download Link (2h)
```

## 💾 Code Template You'll Use

### Template 1: Simple URL in Controller
```php
// In controller extending AbstractController
$url = $this->generateUrl('route_name', ['id' => 5]);
```

### Template 2: URL in Service
```php
// In service
public function __construct(private UrlGeneratorInterface $urlGenerator) {}

public function someMethod(): void {
    $url = $this->urlGenerator->generate('route_name', ['id' => 5]);
}
```

### Template 3: Signed URL
```php
// For security-sensitive operations
$url = 'https://example.com/reset?id=5';
$signed = $this->uriSigner->sign($url, new \DateInterval('PT1H'));
```

### Template 4: Email Link
```php
// ALWAYS use ABSOLUTE_URL for emails
$link = $this->generateUrl(
    'verify_email',
    ['token' => $token],
    UrlGeneratorInterface::ABSOLUTE_URL
);
```

## 🧭 Learning Roadmap

```
START HERE
    │
    ▼
Read README_URL_GENERATION.md (5 min)
    │
    ▼
Read URL_GENERATION_GUIDE.md Introduction (10 min)
    │
    ▼
Study UrlGenerationBlogController.php (10 min)
    ├─ Understand simple URLs
    ├─ Understand parameters
    └─ Understand URL types
    │
    ▼
Study EmailNotificationService.php (10 min)
    ├─ Understand service injection
    ├─ Understand dependency injection
    └─ Understand when to use services
    │
    ▼
Study UserRegistrationController.php (15 min)
    ├─ See complete flow
    ├─ See form handling
    ├─ See redirects
    └─ See email generation
    │
    ▼
Study SignedUrlService.php (10 min)
    ├─ Understand signing
    ├─ Understand expiration
    └─ Understand verification
    │
    ▼
Study UrlGenerationCommand.php (5 min)
    ├─ Understand command context
    └─ Understand ABSOLUTE_URL in commands
    │
    ▼
Use URL_GENERATION_QUICK_REFERENCE.md (while coding)
    │
    ▼
✅ YOU'RE READY TO IMPLEMENT!
```

## 🎯 10-Minute Implementation Guide

### Minute 1-2: Read Overview
```
File: README_URL_GENERATION.md
Focus: Quick start section
```

### Minute 3-4: Understand Pattern
```
File: UrlGenerationBlogController.php
Focus: list() method
Pattern: $this->generateUrl('route_name', ['key' => value])
```

### Minute 5-6: Understand Services
```
File: EmailNotificationService.php
Focus: Constructor injection
Pattern: public function __construct(private UrlGeneratorInterface $gen)
```

### Minute 7-8: See Real Example
```
File: UserRegistrationController.php
Focus: Any method
Pattern: Complete workflow with redirects and emails
```

### Minute 9-10: Reference as Needed
```
File: URL_GENERATION_QUICK_REFERENCE.md
Use: While coding your own URLs
Pattern: Copy-paste and adapt
```

## 🛠️ Common Code Snippets

### Generate Simple URL
```php
$url = $this->generateUrl('home');
```

### Generate URL with One Parameter
```php
$url = $this->generateUrl('user_profile', ['username' => 'john']);
```

### Generate URL with Multiple Parameters
```php
$url = $this->generateUrl('product_filter', [
    'category' => 'electronics',
    'brand' => 'sony',
    'price_max' => 500
]);
```

### Generate Absolute URL
```php
$url = $this->generateUrl(
    'verify',
    ['token' => $token],
    UrlGeneratorInterface::ABSOLUTE_URL
);
```

### Sign a URL
```php
$signed = $this->uriSigner->sign(
    'https://example.com/reset?id=5',
    new \DateInterval('PT1H')
);
```

### Verify Signed URL
```php
if ($this->uriSigner->check($signedUrl)) {
    // URL is valid and not expired
}
```

### Redirect with Generated URL
```php
return new RedirectResponse($this->generateUrl('success_page'));
```

### Pass to Template
```php
return $this->render('template.html', [
    'loginUrl' => $this->generateUrl('login'),
    'registerUrl' => $this->generateUrl('register'),
]);
```

## ❌ Common Mistakes Cheat Sheet

```
❌ WRONG                              ✅ CORRECT
─────────────────────────────────────────────────────────────
$url = '/blog/5'                      $this->generateUrl('blog_list', ['id' => 5])

Email: generateUrl('verify')          Email: generateUrl('verify', [], ABSOLUTE_URL)

$url = '/email/send'                  Use: EmailNotificationService with injection

['id' => $uuid]                       ['id' => (string)$uuid]

Sign without expiration               Sign with: new \DateInterval('PT1H')

generateUrl() in service              Inject UrlGeneratorInterface in service

Command without config                Configure: default_uri in routing

Hardcoded URL everywhere              Use route names everywhere

Not handling signed URL errors        Use try-catch for security exceptions

ABSOLUTE_PATH for emails              ABSOLUTE_URL for emails, APIs, webhooks
```

## 📋 Your Checklist

Before you start coding:

- [ ] I understand route names vs route paths
- [ ] I understand route parameters vs extra parameters
- [ ] I know when to use ABSOLUTE_URL vs ABSOLUTE_PATH
- [ ] I know how to inject services in constructors
- [ ] I understand why signed URLs are important
- [ ] I know common expiration times
- [ ] I know the 3 places to generate URLs
- [ ] I've read at least one example file

## 🎓 Success Criteria

After completing this, you should be able to:

- ✅ Generate URLs in controllers
- ✅ Generate URLs in services  
- ✅ Generate URLs in commands
- ✅ Choose the right URL type
- ✅ Sign sensitive URLs
- ✅ Set expiration on signed URLs
- ✅ Handle verification errors
- ✅ Implement complete user flows

---

## 📁 File Size & Reading Time

| File | Type | Size | Reading Time |
|------|------|------|--------------|
| README_URL_GENERATION.md | Guide | ~3kb | 5-10 min |
| URL_GENERATION_GUIDE.md | Tutorial | ~30kb | 30-45 min |
| URL_GENERATION_QUICK_REFERENCE.md | Reference | ~8kb | 10 min |
| URL_GENERATION_LEARNING_PATH.md | Roadmap | ~8kb | 10 min |
| UrlGenerationBlogController.php | Code | ~8kb | 15 min |
| EmailNotificationService.php | Code | ~7kb | 15 min |
| SignedUrlService.php | Code | ~10kb | 15 min |
| UserRegistrationController.php | Code | ~15kb | 20 min |
| UrlGenerationConfiguration.php | Code | ~12kb | 15 min |

**Total Reading/Study Time: ~3-4 hours for complete understanding**

---

## 🚀 Ready to Start?

1. **First Time?** → Start with `README_URL_GENERATION.md`
2. **Need Quick Lookup?** → Use `URL_GENERATION_QUICK_REFERENCE.md`
3. **Want to Learn?** → Read `URL_GENERATION_GUIDE.md`
4. **Need Examples?** → Study controller/service files
5. **Implementing Now?** → Use the code templates above

**Good luck! You've got everything you need!** 🎉
