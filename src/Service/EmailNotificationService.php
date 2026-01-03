<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * EmailNotificationService demonstrates URL generation in Services
 * 
 * When you're NOT extending AbstractController, you need to inject the
 * UrlGeneratorInterface service to generate URLs
 * 
 * This is common when sending emails, generating API responses, etc.
 */
class EmailNotificationService
{
    // STEP 1: Constructor Injection of UrlGeneratorInterface
    // By type-hinting with UrlGeneratorInterface, Symfony's autowiring
    // automatically injects the router service
    // This is called "Dependency Injection" - we ask for what we need
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * Generate a confirmation email with URLs
     * 
     * This method shows how to use the injected service to generate URLs
     * that can be sent in emails to users
     */
    public function sendWelcomeEmail(string $email, string $username, string $verificationLink): void
    {
        // STEP 2: Build the email content with generated URLs
        $emailBody = sprintf(
            "Welcome %s!\n\n" .
            "Please confirm your email by clicking this link:\n" .
            "%s\n\n" .
            "Best regards,\n" .
            "The Team",
            htmlspecialchars($username),
            htmlspecialchars($verificationLink)
        );

        // In a real app, you'd send this email here
        // $mailer->send($email, 'Welcome!', $emailBody);
    }

    /**
     * Send password reset email with reset link
     */
    public function sendPasswordResetEmail(string $email, string $resetLink): void
    {
        // Build the email content with generated URL
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

        // In a real app, you'd send this email here
        // $mailer->send($email, 'Password Reset', $emailBody);
    }

    /**
     * Generate a password reset link
     * Shows how to generate URLs with sensitive data
     */
    public function generatePasswordResetLink(string $userId, string $resetToken): string
    {
        // Generate the password reset URL with both user ID and reset token
        // The URL might be: /password-reset/123?token=secure_token_xyz
        $resetLink = $this->urlGenerator->generate(
            'password_reset',
            [
                'userId' => $userId,
                'token' => $resetToken,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $resetLink;
    }

    /**
     * Generate API documentation links
     * Shows how to generate URLs for different purposes
     */
    public function getApiDocumentationLink(string $section = 'overview'): string
    {
        // Generate a URL to the API documentation
        // Can include optional section parameters
        return $this->urlGenerator->generate('api_docs', [
            'section' => $section,
        ]);
    }

    /**
     * Generate a social share link
     * Shows extra parameters being added as query strings
     */
    public function generateShareLink(int $articleId, string $platform = 'twitter'): string
    {
        // Generate article URL with extra tracking parameters
        // Result: /article/42?utm_source=twitter&utm_campaign=social_share
        return $this->urlGenerator->generate('article_show', [
            'id' => $articleId,
            'utm_source' => $platform,         // Tracking parameter
            'utm_campaign' => 'social_share',  // Tracking parameter
        ]);
    }

    /**
     * Generate URLs with different path types
     */
    public function demonstrateUrlTypes(): void
    {
        // STEP 7: Use different URL generation types

        // Type 1: ABSOLUTE_PATH (default)
        // Best for: Frontend redirects, internal links
        // Result: /blog
        $relativePath = $this->urlGenerator->generate('blog', [], UrlGeneratorInterface::ABSOLUTE_PATH);

        // Type 2: ABSOLUTE_URL
        // Best for: Emails, external APIs, webhooks
        // Result: https://example.com/blog
        $fullUrl = $this->urlGenerator->generate('blog', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // Type 3: NETWORK_PATH
        // Best for: Mixed http/https environments
        // Result: //example.com/blog
        // The browser will use the same protocol as the current page (http or https)
        $networkPathUrl = $this->urlGenerator->generate('blog', [], UrlGeneratorInterface::NETWORK_PATH);
    }
}
