# 🎓 Quick Visual Guide - URL Generation Real-World Example

## The Complete User Registration & Password Reset Flow

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    USER REGISTRATION FLOW                                   │
└─────────────────────────────────────────────────────────────────────────────┘

STEP 1: User Visits Registration Page
┌─────────────────────────┐
│ GET /user/register      │
│ (showRegisterForm)      │
└──────────────┬──────────┘
               │ Generate URL for form submission
               ▼
         $submitUrl = $this->generateUrl('user_register_submit')
         Result: /user/register
               │
               ▼ Pass to template
         {{ submitUrl }}


STEP 2: User Submits Registration Form
┌─────────────────────────┐
│ POST /user/register     │  ◄─── Form action="{{ submitUrl }}"
│ (handleRegistration)    │
└──────────────┬──────────┘
               │
        ┌──────┴──────┐
        │             │
     Create User   Generate Token
        │             │
        ▼             ▼
   User object  $token = bin2hex(random_bytes(32))
        │             │
        └──────┬──────┘
               │
               ▼ Generate Verification Link
        $verificationLink = $this->generateUrl(
            'user_verify_email',
            ['userId' => $user->getId(), 'token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL  ◄─── IMPORTANT: ABSOLUTE!
        )
        
        Result: https://example.com/user/verify/123/abc123def456...
               │
               ▼ Send Email with Link
        $this->emailService->sendWelcomeEmail(
            $email,
            $username,
            $verificationLink
        )
               │
               ▼ Redirect to Success
        return new RedirectResponse(
            $this->generateUrl('user_register_success')
        )


STEP 3: User Clicks Verification Link in Email
┌──────────────────────────────────────┐
│ GET /user/verify/123/abc123def456... │
│ (verifyEmail)                        │
└──────────────┬───────────────────────┘
               │
        ┌──────┴──────┐
        │             │
    Get User   Validate Token
        │             │
        ▼             ▼
  Found? ✓       Valid? ✓
        │             │
        └──────┬──────┘
               │
               ▼ Mark as Verified
        $user->setVerified(true)
        // Save to database
               │
               ▼ Redirect to Dashboard
        return new RedirectResponse(
            $this->generateUrl('user_dashboard')
        )


┌─────────────────────────────────────────────────────────────────────────────┐
│                  PASSWORD RESET FLOW                                        │
└─────────────────────────────────────────────────────────────────────────────┘

STEP 4: User Visits Password Reset Page
┌─────────────────────────────────┐
│ GET /user/password-reset        │
│ (showPasswordResetRequestForm)   │
└──────────────┬──────────────────┘
               │ Generate URL for form
               ▼
         $submitUrl = $this->generateUrl('password_reset_submit')
         Result: /user/password-reset
               │
               ▼ Pass to template
         {{ submitUrl }}


STEP 5: User Submits Password Reset Request
┌────────────────────────────────┐
│ POST /user/password-reset      │  ◄─── Form action="{{ submitUrl }}"
│ (handlePasswordResetRequest)    │
└──────────────┬─────────────────┘
               │
        Find User by Email
               │
               ▼ Generate Reset Link
        $resetToken = bin2hex(random_bytes(32))
        $resetLink = $this->generateUrl(
            'password_reset_form',
            ['userId' => $user->getId(), 'token' => $resetToken],
            UrlGeneratorInterface::ABSOLUTE_URL  ◄─── ABSOLUTE URL for email
        )
        
        Result: https://example.com/user/password-reset/123/xyz789...
               │
               ▼ Send Email with Link
        $this->emailService->sendPasswordResetEmail($email, $resetLink)
               │
               ▼ Redirect to Confirmation
        return new RedirectResponse(
            $this->generateUrl('password_reset_confirmation')
        )


STEP 6: User Clicks Reset Link in Email
┌──────────────────────────────────────┐
│ GET /user/password-reset/123/xyz789  │
│ (showPasswordResetForm)               │
└──────────────┬───────────────────────┘
               │
        Validate Token
               │
               ▼ Generate Form Action URL
        $submitUrl = $this->generateUrl('password_reset_update', [
            'userId' => $userId,
            'token' => $token,
        ])
        
        Result: /user/password-reset/123/xyz789
               │
               ▼ Display Password Form
        return $this->render('user/password_reset_form.html.twig', [
            'submitUrl' => $submitUrl,  ◄─── Form action="{{ submitUrl }}"
        ])


STEP 7: User Submits New Password
┌──────────────────────────────────────┐
│ POST /user/password-reset/123/xyz789 │
│ (updatePassword)                     │
└──────────────┬───────────────────────┘
               │
        ┌──────┴──────────────┐
        │                     │
    Validate Token      Get User
        │                     │
        ▼                     ▼
    Valid? ✓         Found? ✓
        │                     │
        └──────┬──────────────┘
               │
               ▼ Update Password
        $user->setPassword(
            password_hash($newPassword, PASSWORD_BCRYPT)
        )
        // Save to database
               │
               ▼ Redirect to Success
        return new RedirectResponse(
            $this->generateUrl('password_reset_success')
        )
```

---

## Key Patterns Used

### Pattern 1: Generate URL in Controller
```php
$url = $this->generateUrl('route_name', ['param' => 'value']);
```

### Pattern 2: Use ABSOLUTE_URL for Emails
```php
$url = $this->generateUrl(
    'route_name',
    ['param' => 'value'],
    UrlGeneratorInterface::ABSOLUTE_URL  // ← Add this!
);
```

### Pattern 3: Pass URL to Service
```php
$url = $this->generateUrl('route_name', $params, ABSOLUTE_URL);
$this->emailService->sendEmail($email, $url);  // ← Service receives URL
```

### Pattern 4: Service Uses Received URL
```php
public function sendEmail(string $email, string $verificationLink): void {
    $body = sprintf("Click here: %s", htmlspecialchars($verificationLink));
}
```

### Pattern 5: Generate Token for Security
```php
$token = bin2hex(random_bytes(32));  // Secure random 64-char hex string
$url = $this->generateUrl('route', ['token' => $token], ABSOLUTE_URL);
```

### Pattern 6: Validate Token on Retrieval
```php
if (!$this->validateToken($userId, $token)) {
    // Invalid token - show error
    return new RedirectResponse($this->generateUrl('error_page'));
}
// Token valid - proceed
```

### Pattern 7: Redirect After Action
```php
return new RedirectResponse(
    $this->generateUrl('success_page')
);
```

---

## The Three Types of URLs

### Type 1: ABSOLUTE_PATH (Default)
```php
$url = $this->generateUrl('blog_list', []);
// Result: /blog

// Use for: Internal website links, form actions
```

### Type 2: ABSOLUTE_URL
```php
$url = $this->generateUrl('blog_list', [], UrlGeneratorInterface::ABSOLUTE_URL);
// Result: https://example.com/blog

// Use for: Emails, APIs, external services
```

### Type 3: NETWORK_PATH
```php
$url = $this->generateUrl('blog_list', [], UrlGeneratorInterface::NETWORK_PATH);
// Result: //example.com/blog

// Use for: Mixed http/https environments
```

---

## Common Mistakes ❌ vs. Correct Patterns ✅

### Mistake 1: Relative URLs in Emails
```
❌ Email text: "Click here: /verify?token=abc"
   → User doesn't know the domain!

✅ Email text: "Click here: https://example.com/verify?token=abc"
   → User can click it!
```

### Mistake 2: No Token for Security
```
❌ Reset link: /password-reset/123
   → Anyone can guess and reset any user!

✅ Reset link: /password-reset/123/abc123def456...
   → Only person with token can reset
```

### Mistake 3: No Token Validation
```
❌ public function reset(string $token): Response {
        // No validation - security hole!
        $user = $this->getUserByToken($token);
   }

✅ public function reset(string $token): Response {
        if (!$this->validateToken($token)) {
            throw new AccessDeniedException();
        }
        $user = $this->getUserByToken($token);
   }
```

### Mistake 4: Forgetting to Validate Tokens Again
```
❌ GET /reset?token=abc  ✓ Validated
   POST /reset (same token)  ✗ No validation!

✅ GET /reset?token=abc  ✓ Validated
   POST /reset (same token)  ✓ Validate again!
```

### Mistake 5: Hardcoded URLs
```
❌ $url = '/user/profile/123';
   // Breaks if route changes!

✅ $url = $this->generateUrl('user_profile', ['id' => 123]);
   // Automatically updates when route changes
```

---

## Security Checklist ✅

- ✅ Use `ABSOLUTE_URL` for emails
- ✅ Generate tokens: `bin2hex(random_bytes(32))`
- ✅ Validate tokens on retrieval
- ✅ Validate tokens again on update
- ✅ Use HTTPS in production
- ✅ Include CSRF tokens in forms
- ✅ Check user exists before updating
- ✅ Sign sensitive URLs
- ✅ Set expiration on signed URLs
- ✅ Never hardcode URLs

---

## File References 📚

| Task | File | Lines |
|------|------|-------|
| See complete controller | `src/Controller/UserRegistrationController.php` | All |
| See email service | `src/Service/EmailNotificationService.php` | All |
| See user entity | `src/Entity/User.php` | All |
| Read full tutorial | `URL_GENERATION_GUIDE.md` | "Complete Real-World Example" |
| Quick reference | `URL_GENERATION_QUICK_REFERENCE.md` | Any section |

---

## How to Implement This

### 1. Create Entity
```
✅ src/Entity/User.php  [Already created]
```

### 2. Create Routes
```
Add to config/routes.yaml:
- user_register
- user_register_submit
- user_verify_email
- user_verify_error
- user_register_success
- password_reset_request
- password_reset_submit
- password_reset_form
- password_reset_update
- password_reset_success
- password_reset_error
```

### 3. Create Controller
```
✅ src/Controller/UserRegistrationController.php  [Already created]
```

### 4. Create Service
```
✅ src/Service/EmailNotificationService.php  [Already updated]
```

### 5. Create Templates
```
Create these Twig templates:
- templates/user/register.html.twig
- templates/user/register_success.html.twig
- templates/user/password_reset_request.html.twig
- templates/user/password_reset_form.html.twig
- templates/user/password_reset_success.html.twig
- templates/user/password_reset_error.html.twig
- templates/user/verify_error.html.twig
- templates/user/dashboard.html.twig
```

### 6. Uncomment Database Code
```
In UserRegistrationController.php, uncomment all TODOs:
- persist() and flush() in handleRegistration()
- flush() in verifyEmail()
- flush() in updatePassword()
```

### 7. Implement Helper Methods
```
In UserRegistrationController.php, implement:
- getUserById($id)
- findUserByEmail($email)
- validateToken($user, $token)
- validateResetToken($userId, $token)
```

---

## Testing the Flow

### Test Scenario (Manual)

1. Go to `/user/register` → See registration form
2. Fill form and submit → Get success message
3. Check email (or logs) → See verification link
4. Click verification link → User marked as verified
5. Go to `/user/password-reset` → See password reset form
6. Submit email → Get confirmation page
7. Check email (or logs) → See reset link
8. Click reset link → See password form
9. Submit new password → Get success message
10. Login with new password → Works! ✅

---

## Summary

This complete flow demonstrates:
- ✅ URL generation in controllers
- ✅ ABSOLUTE_URL for emails
- ✅ Token generation and validation
- ✅ Passing URLs to services
- ✅ Redirecting after actions
- ✅ Error handling
- ✅ Security best practices
- ✅ Real-world patterns

Everything is step-by-step documented in the code comments! 🎉
