<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpFoundation\Exception\ExpiredSignedUriException;
use Symfony\Component\HttpFoundation\Exception\UnsignedUriException;
use Symfony\Component\HttpFoundation\Exception\UnverifiedSignedUriException;

/**
 * SignedUrlService demonstrates creating and verifying signed URLs
 * 
 * A signed URL includes a cryptographic hash that ensures:
 * 1. The URL hasn't been tampered with
 * 2. The URL hasn't expired (if expiration is set)
 * 3. The URL is authentic and came from your server
 * 
 * Common use cases:
 * - Password reset links
 * - Email verification links
 * - Temporary download links
 * - Sensitive actions that need verification
 */
class SignedUrlService
{
    // STEP 1: Inject the UriSigner service
    // The UriSigner handles all cryptographic signing operations
    public function __construct(
        private UriSigner $uriSigner,
    ) {
    }

    /**
     * Generate a signed password reset link
     * The signature ensures this link really came from your server
     * and hasn't been modified by the user
     */
    public function generatePasswordResetLink(int $userId): string
    {
        // STEP 2: Create the base URL
        // In a real app, this would be generated using UrlGeneratorInterface
        $url = 'https://example.com/password-reset?userId=' . $userId;

        // STEP 3: Sign the URL
        // The sign() method adds a '_hash' parameter to the URL
        // Example result: https://example.com/password-reset?userId=1&_hash=abc123def456
        $signedUrl = $this->uriSigner->sign($url);

        return $signedUrl;
    }

    /**
     * Generate a signed download link that expires after 1 hour
     * After 1 hour, the link will be invalid even if the signature is correct
     */
    public function generateTemporaryDownloadLink(string $filePath): string
    {
        $url = 'https://example.com/download?file=' . urlencode($filePath);

        // STEP 4: Sign URL with expiration using DateInterval
        // DateInterval('PT1H') means "Period Time of 1 Hour"
        // The expiration time is added to the current time
        // The sign() method automatically calculates the expiration timestamp
        // Result adds: &_expiration=1234567890&_hash=...
        $expiringUrl = $this->uriSigner->sign(
            $url,
            new \DateInterval('PT1H')  // Valid for 1 hour from now
        );

        return $expiringUrl;
    }

    /**
     * Generate a signed link that expires at a specific date
     * Useful when you know exactly when the link should stop working
     */
    public function generateLimitedTimeOfferLink(string $offerId, \DateTimeImmutable $expiryDate): string
    {
        $url = 'https://example.com/offer?offerId=' . $offerId;

        // STEP 5: Sign URL with absolute expiration date
        // This link will be valid until the specified date
        // Result: https://example.com/offer?offerId=123&_expiration=1704067200&_hash=...
        $signedUrl = $this->uriSigner->sign($url, $expiryDate);

        return $signedUrl;
    }

    /**
     * Verify that a signed URL is valid and hasn't been tampered with
     */
    public function verifySignedUrl(string $signedUrl): bool
    {
        // STEP 6: Simple check for signature validity
        // Returns true if the signature is valid and not expired
        // Returns false if the signature is invalid or expired
        return $this->uriSigner->check($signedUrl);
    }

    /**
     * Verify with detailed error reporting
     * Tells you exactly why a URL is invalid
     */
    public function getSignatureValidationDetails(string $signedUrl): array
    {
        $result = [
            'isValid' => false,
            'error' => null,
            'reason' => null,
        ];

        try {
            // STEP 7: Use verify() method for detailed error information
            // This throws exceptions if there's any problem with the signature
            $this->uriSigner->verify($signedUrl);

            // If we get here, the URL is valid
            $result['isValid'] = true;

        } catch (UnsignedUriException $e) {
            // The URL doesn't have a signature at all
            $result['error'] = 'unsigned_uri';
            $result['reason'] = 'The URL is missing the required _hash parameter';

        } catch (UnverifiedSignedUriException $e) {
            // The URL has a signature, but it's wrong
            // This could mean tampering or the URL was signed with a different key
            $result['error'] = 'invalid_signature';
            $result['reason'] = 'The signature does not match the URL content';

        } catch (ExpiredSignedUriException $e) {
            // The URL's expiration timestamp has passed
            $result['error'] = 'expired';
            $result['reason'] = 'The URL has expired and is no longer valid';
        }

        return $result;
    }

    /**
     * Process a password reset request with signature verification
     * This is a complete example of the workflow
     */
    public function processPasswordReset(string $signedUrl, string $newPassword): bool
    {
        // STEP 8: Verify the URL before processing the reset
        $validationResult = $this->getSignatureValidationDetails($signedUrl);

        if (!$validationResult['isValid']) {
            // Log the security issue
            // Log the reason in $validationResult['reason']
            return false;
        }

        // If we get here, the URL is valid and not expired
        // It's safe to process the password reset
        // Extract the userId from the URL and update the password
        // In a real app: parse userId from URL and update database

        return true;
    }

    /**
     * Generate a signed email verification link
     * Complete example with expiration
     */
    public function generateEmailVerificationLink(int $userId, string $email): string
    {
        $url = 'https://example.com/verify-email?userId=' . $userId . '&email=' . urlencode($email);

        // STEP 9: Sign with 24-hour expiration
        // PT24H = Period Time of 24 Hours
        // User must verify their email within 24 hours
        $signedUrl = $this->uriSigner->sign(
            $url,
            new \DateInterval('PT24H')
        );

        return $signedUrl;
    }

    /**
     * Generate multiple types of signed URLs for different purposes
     */
    public function generateSecurityLinks(int $userId): array
    {
        $baseUrl = 'https://example.com';

        return [
            // Password reset: expires in 1 hour
            'passwordReset' => $this->uriSigner->sign(
                $baseUrl . '/password-reset?userId=' . $userId,
                new \DateInterval('PT1H')
            ),

            // Email verification: expires in 24 hours
            'emailVerification' => $this->uriSigner->sign(
                $baseUrl . '/verify-email?userId=' . $userId,
                new \DateInterval('PT24H')
            ),

            // API access token: expires in 30 days
            'apiToken' => $this->uriSigner->sign(
                $baseUrl . '/api/token?userId=' . $userId,
                new \DateInterval('P30D')  // P = Period, 30D = 30 Days
            ),

            // Temporary download: expires in 2 hours
            'tempDownload' => $this->uriSigner->sign(
                $baseUrl . '/download?userId=' . $userId,
                new \DateInterval('PT2H')
            ),
        ];
    }
}
