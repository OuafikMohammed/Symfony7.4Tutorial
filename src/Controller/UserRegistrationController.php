<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EmailNotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * UserRegistrationController - Complete Real-World Example
 * 
 * This controller demonstrates URL generation in a realistic user registration flow:
 * 1. User registers on the website
 * 2. Verification email is sent with a signed link
 * 3. User clicks link to verify their email
 * 4. User can reset password using another signed link
 */
#[Route('/user')]
class UserRegistrationController extends AbstractController
{
    public function __construct(
        private EmailNotificationService $emailService,
    ) {
    }

    /**
     * Display registration form
     * Generates URL for the form submission endpoint
     */
    #[Route('/register', name: 'user_register', methods: ['GET'])]
    public function showRegisterForm(): Response
    {
        // Generate URL where the form will be submitted
        $submitUrl = $this->generateUrl('user_register_submit');

        return $this->render('user/register.html.twig', [
            'submitUrl' => $submitUrl,
        ]);
    }

    /**
     * Handle form submission for user registration
     * Generates verification link and sends email
     */
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
        // TODO: Save user to database: $this->getDoctrine()->getManager()->persist($user); $this->getDoctrine()->getManager()->flush();

        // STEP 2: Generate verification link
        // This is a signed URL that expires in 24 hours
        $token = bin2hex(random_bytes(32));  // Random token
        $verificationLink = $this->generateUrl(
            'user_verify_email',
            [
                'userId' => $user->getId() ?? 0,  // Use placeholder until saved
                'token' => $token,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL  // Use full URL for email
        );

        // STEP 3: Send verification email
        $this->emailService->sendWelcomeEmail(
            email: $email,
            username: $username,
            verificationLink: $verificationLink
        );

        // STEP 4: Redirect to success page
        // Generate the URL for the success page
        $successUrl = $this->generateUrl('user_register_success');

        return new RedirectResponse($successUrl);
    }

    /**
     * Registration success page
     */
    #[Route('/register/success', name: 'user_register_success')]
    public function registrationSuccess(): Response
    {
        // Generate URL to login page (user should login after verification)
        $loginUrl = $this->generateUrl('login');

        return $this->render('user/register_success.html.twig', [
            'loginUrl' => $loginUrl,
        ]);
    }

    /**
     * Email verification endpoint
     * User clicks the link from their email
     */
    #[Route('/verify/{userId}/{token}', name: 'user_verify_email')]
    public function verifyEmail(int $userId, string $token): Response
    {
        // STEP 1: Find user by ID
        $user = $this->getUserById($userId);  // Fetch from database
        if (!$user) {
            // Generate error page URL
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

        // STEP 4: Redirect to success page
        // Generate URL for dashboard (user is now verified)
        $dashboardUrl = $this->generateUrl('user_dashboard');

        return new RedirectResponse($dashboardUrl);
    }

    /**
     * Verification error page
     */
    #[Route('/verify/error', name: 'user_verify_error')]
    public function verifyError(): Response
    {
        // Generate URLs to retry or contact support
        $resendEmailUrl = $this->generateUrl('user_resend_verification');
        $supportUrl = $this->generateUrl('contact_support');

        return $this->render('user/verify_error.html.twig', [
            'resendUrl' => $resendEmailUrl,
            'supportUrl' => $supportUrl,
        ]);
    }

    /**
     * User dashboard (only for verified users)
     */
    #[Route('/dashboard', name: 'user_dashboard')]
    public function dashboard(): Response
    {
        // Generate URLs for dashboard actions
        $profileUrl = $this->generateUrl('user_profile');
        $settingsUrl = $this->generateUrl('user_settings');
        $logoutUrl = $this->generateUrl('logout');

        return $this->render('user/dashboard.html.twig', [
            'profileUrl' => $profileUrl,
            'settingsUrl' => $settingsUrl,
            'logoutUrl' => $logoutUrl,
        ]);
    }

    /**
     * Password reset request form
     */
    #[Route('/password-reset', name: 'password_reset_request', methods: ['GET'])]
    public function showPasswordResetRequestForm(): Response
    {
        // Generate form submission URL
        $submitUrl = $this->generateUrl('password_reset_submit');

        return $this->render('user/password_reset_request.html.twig', [
            'submitUrl' => $submitUrl,
        ]);
    }

    /**
     * Handle password reset request
     * Generates and sends password reset email
     */
    #[Route('/password-reset', name: 'password_reset_submit', methods: ['POST'])]
    public function handlePasswordResetRequest(Request $request): Response
    {
        $email = $request->request->get('email');

        // Find user by email
        $user = $this->findUserByEmail($email);

        if ($user) {
            // STEP 1: Generate password reset link
            // This is a SIGNED URL that expires in 1 hour
            // Signing ensures only your server can create valid reset links
            $resetToken = bin2hex(random_bytes(32));
            $resetLink = $this->generateUrl(
                'password_reset_form',
                [
                    'userId' => $user->getId(),
                    'token' => $resetToken,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            // STEP 2: Sign the URL with 1-hour expiration
            // Users have 1 hour to reset their password
            // After that, they need to request a new link
            // ... sign the URL (in a real app with UriSigner)

            // STEP 3: Send password reset email
            $this->emailService->sendPasswordResetEmail(
                email: $email,
                resetLink: $resetLink
            );
        }

        // STEP 4: Redirect to confirmation page
        // Always show success, even if email doesn't exist (security best practice)
        $confirmationUrl = $this->generateUrl('password_reset_confirmation');

        return new RedirectResponse($confirmationUrl);
    }

    /**
     * Password reset form (user clicks link from email)
     */
    #[Route('/password-reset/{userId}/{token}', name: 'password_reset_form')]
    public function showPasswordResetForm(int $userId, string $token): Response
    {
        // Verify the token is valid and not expired
        if (!$this->validateResetToken($userId, $token)) {
            // Token expired or invalid - redirect to new request
            $newRequestUrl = $this->generateUrl('password_reset_request');
            return new RedirectResponse($newRequestUrl);
        }

        // Generate form submission URL with the token
        $submitUrl = $this->generateUrl('password_reset_update', [
            'userId' => $userId,
            'token' => $token,
        ]);

        return $this->render('user/password_reset_form.html.twig', [
            'submitUrl' => $submitUrl,
        ]);
    }

    /**
     * Handle password reset form submission
     */
    #[Route('/password-reset/{userId}/{token}', name: 'password_reset_update', methods: ['POST'])]
    public function updatePassword(int $userId, string $token, Request $request): Response
    {
        // Verify token again
        if (!$this->validateResetToken($userId, $token)) {
            $errorUrl = $this->generateUrl('password_reset_error');
            return new RedirectResponse($errorUrl);
        }

        // Get new password
        $newPassword = $request->request->get('password');

        // Update user password
        $user = $this->getUserById($userId);
        if (!$user) {
            $errorUrl = $this->generateUrl('password_reset_error');
            return new RedirectResponse($errorUrl);
        }
        $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
        // TODO: Save to database: $this->getDoctrine()->getManager()->flush();

        // Redirect to success page
        $successUrl = $this->generateUrl('password_reset_success');

        return new RedirectResponse($successUrl);
    }

    /**
     * Password reset error page
     */
    #[Route('/password-reset/error', name: 'password_reset_error')]
    public function passwordResetError(): Response
    {
        // Generate link to request new reset
        $newRequestUrl = $this->generateUrl('password_reset_request');

        return $this->render('user/password_reset_error.html.twig', [
            'newRequestUrl' => $newRequestUrl,
        ]);
    }

    /**
     * Password reset success page
     */
    #[Route('/password-reset/success', name: 'password_reset_success')]
    public function passwordResetSuccess(): Response
    {
        // Generate link to login
        $loginUrl = $this->generateUrl('login');

        return $this->render('user/password_reset_success.html.twig', [
            'loginUrl' => $loginUrl,
        ]);
    }

    /**
     * User profile page
     */
    #[Route('/profile', name: 'user_profile')]
    public function profile(): Response
    {
        // Generate various action URLs
        $editUrl = $this->generateUrl('user_profile_edit');
        $deleteUrl = $this->generateUrl('user_profile_delete');
        $dashboardUrl = $this->generateUrl('user_dashboard');

        return $this->render('user/profile.html.twig', [
            'editUrl' => $editUrl,
            'deleteUrl' => $deleteUrl,
            'dashboardUrl' => $dashboardUrl,
        ]);
    }

    /**
     * User settings page
     */
    #[Route('/settings', name: 'user_settings')]
    public function settings(): Response
    {
        // Generate URLs for different settings sections
        $accountSettings = $this->generateUrl('user_settings', ['section' => 'account']);
        $securitySettings = $this->generateUrl('user_settings', ['section' => 'security']);
        $privacySettings = $this->generateUrl('user_settings', ['section' => 'privacy']);
        $notificationSettings = $this->generateUrl('user_settings', ['section' => 'notifications']);

        return $this->render('user/settings.html.twig', [
            'accountSettings' => $accountSettings,
            'securitySettings' => $securitySettings,
            'privacySettings' => $privacySettings,
            'notificationSettings' => $notificationSettings,
        ]);
    }

    /**
     * API endpoint - return user data with links (HATEOAS)
     */
    #[Route('/api/profile', name: 'api_user_profile', methods: ['GET'])]
    public function apiProfile(): Response
    {
        // For API responses, generate absolute URLs so external clients can use them
        $data = [
            'id' => 1,
            'username' => 'john_doe',
            'email' => 'john@example.com',
            '_links' => [
                'self' => $this->generateUrl('api_user_profile', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'edit' => $this->generateUrl('api_user_update', ['id' => 1], UrlGeneratorInterface::ABSOLUTE_URL),
                'delete' => $this->generateUrl('api_user_delete', ['id' => 1], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];

        return $this->json($data);
    }

    // ========================================================================
    // HELPER METHODS (in real app, these would be in services)
    // ========================================================================

    private function validateToken(User $user, string $token): bool
    {
        // In real app: check token against database
        // For now, just return true
        return true;
    }

    private function validateResetToken(int $userId, string $token): bool
    {
        // In real app: verify token from database and check expiration
        // For now, just return true
        return true;
    }

    private function findUserByEmail(string $email): ?User
    {
        // TODO: Implement with repository
        // Example: return $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
        return null;
    }

    private function getUserById(int $id): ?User
    {
        // TODO: Implement with repository
        // Example: return $this->getDoctrine()->getRepository(User::class)->find($id);
        return null;
    }
}

/**
 * SUMMARY OF URL GENERATION PATTERNS USED
 * 
 * 1. FORM ACTIONS
 *    - Generate URL for where form should submit
 *    - Example: $submitUrl = $this->generateUrl('user_register_submit')
 *
 * 2. REDIRECTS
 *    - Generate URL to redirect user after action
 *    - Example: new RedirectResponse($this->generateUrl('user_dashboard'))
 *
 * 3. EMAIL LINKS
 *    - Use ABSOLUTE_URL (full domain)
 *    - Example: $this->generateUrl('user_verify_email', [], ABSOLUTE_URL)
 *
 * 4. SIGNED URLs (Security)
 *    - Sign URLs for sensitive operations
 *    - Example: $this->uriSigner->sign($resetLink)
 *
 * 5. TEMPLATE VARIABLES
 *    - Generate URLs in controller, pass to template
 *    - Example: ['dashboardUrl' => $this->generateUrl('dashboard')]
 *
 * 6. API RESPONSES
 *    - Use ABSOLUTE_URL for external API clients
 *    - Include HATEOAS links (_links section)
 *    - Example: '_links' => ['self' => $this->generateUrl(..., ABSOLUTE_URL)]
 *
 * 7. QUERY PARAMETERS
 *    - Extra parameters become query string
 *    - Example: $this->generateUrl('settings', ['section' => 'account'])
 *    - Result: /user/settings?section=account
 */
