<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * CONFIGURATION REFERENCE for URL Generation
 * 
 * This file contains configuration examples for different scenarios
 */

// ============================================================================
// CONFIGURATION 1: Set default URI for Console Commands
// ============================================================================
// File: config/packages/routing.php

/*
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    // This domain is used when generating URLs in console commands
    // where there's no HTTP request to determine the domain
    $framework->router()->defaultUri('https://example.com/');
};
*/

// ============================================================================
// CONFIGURATION 2: Force HTTPS on Generated URLs
// ============================================================================

/*
// File: config/services.php

$container->parameters()
    ->set('router.request_context.scheme', 'https')
    ->set('asset.request_context.secure', true)
;

// Or in YAML (config/services.yaml):
parameters:
    router.request_context.scheme: 'https'
    asset.request_context.secure: true
*/

// ============================================================================
// CONFIGURATION 3: Define Routes with Required Schemes
// ============================================================================

/*
// File: config/routes.php

use App\Controller\SecurityController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    // Force HTTPS on the login route
    $routes->add('login', '/login')
        ->controller([SecurityController::class, 'login'])
        ->schemes(['https'])
    ;

    // Import all attribute routes and force HTTPS on them
    $routes->import('../../src/Controller/', 'attribute')
        ->schemes(['https'])
    ;
};
*/

// ============================================================================
// USAGE PATTERN 1: Controller with URL Generation
// ============================================================================

/*
class MyController extends AbstractController
{
    #[Route('/page', name: 'my_page')]
    public function page(): Response
    {
        // Generate simple URL
        $url = $this->generateUrl('my_route');

        // Generate with parameters
        $url = $this->generateUrl('my_route', ['id' => 5]);

        // Generate absolute URL
        $url = $this->generateUrl('my_route', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->render('template.html.twig', ['url' => $url]);
    }
}
*/

// ============================================================================
// USAGE PATTERN 2: Service with URL Generation
// ============================================================================

/*
class MyService
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function doSomething(): void
    {
        // Use the injected service
        $url = $this->urlGenerator->generate('my_route');
    }
}
*/

// ============================================================================
// USAGE PATTERN 3: Command with URL Generation
// ============================================================================

/*
#[AsCommand(name: 'app:my-command')]
class MyCommand extends Command
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
    }

    protected function execute(SymfonyStyle $io): int
    {
        // Use the injected service
        $url = $this->urlGenerator->generate('my_route', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $io->writeln('URL: ' . $url);
        return Command::SUCCESS;
    }
}
*/

// ============================================================================
// COMMON PATTERNS
// ============================================================================

class UrlGenerationPatterns
{
    // Pattern 1: Multiple URLs in one place
    public function getNavigation(UrlGeneratorInterface $urlGenerator): array
    {
        return [
            'home' => $urlGenerator->generate('home'),
            'about' => $urlGenerator->generate('about'),
            'contact' => $urlGenerator->generate('contact'),
            'login' => $urlGenerator->generate('login'),
        ];
    }

    // Pattern 2: Generate URLs with fallback
    public function getSafeUrl(UrlGeneratorInterface $urlGenerator, string $routeName, array $params = []): string
    {
        try {
            return $urlGenerator->generate($routeName, $params);
        } catch (\Exception $e) {
            // Route doesn't exist - return home page
            return $urlGenerator->generate('home');
        }
    }

    // Pattern 3: Generate paginated URLs
    public function getPaginationUrls(UrlGeneratorInterface $urlGenerator, int $currentPage, int $totalPages): array
    {
        $urls = [];

        if ($currentPage > 1) {
            $urls['previous'] = $urlGenerator->generate('blog', [
                'page' => $currentPage - 1
            ]);
        }

        for ($i = 1; $i <= $totalPages; $i++) {
            $urls['page_' . $i] = $urlGenerator->generate('blog', [
                'page' => $i
            ]);
        }

        if ($currentPage < $totalPages) {
            $urls['next'] = $urlGenerator->generate('blog', [
                'page' => $currentPage + 1
            ]);
        }

        return $urls;
    }

    // Pattern 4: Generate filter URLs
    public function getFilterUrl(UrlGeneratorInterface $urlGenerator, string $filterType, string $filterValue): string
    {
        return $urlGenerator->generate('products', [
            'filter' => $filterType,
            'value' => $filterValue,
        ]);
    }

    // Pattern 5: Generate social share URLs
    public function getSocialShareUrls(UrlGeneratorInterface $urlGenerator, int $articleId, string $title): array
    {
        $articleUrl = $urlGenerator->generate('article_show', [
            'id' => $articleId,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return [
            'twitter' => 'https://twitter.com/intent/tweet?url=' . urlencode($articleUrl) . '&text=' . urlencode($title),
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($articleUrl),
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($articleUrl),
        ];
    }

    // Pattern 6: Generate API endpoint URLs
    public function getApiUrls(UrlGeneratorInterface $urlGenerator): array
    {
        return [
            'users_list' => $urlGenerator->generate('api_users_list'),
            'user_show' => $urlGenerator->generate('api_user_show', ['userId' => '{userId}']),
            'user_update' => $urlGenerator->generate('api_user_update', ['userId' => '{userId}']),
            'user_delete' => $urlGenerator->generate('api_user_delete', ['userId' => '{userId}']),
        ];
    }
}

// ============================================================================
// DEBUGGING AND TESTING
// ============================================================================

class UrlGenerationDebug
{
    // Method to debug URL generation issues
    public static function debugRoute(UrlGeneratorInterface $urlGenerator, string $routeName): void
    {
        echo "=== Debugging Route: $routeName ===\n";

        try {
            // Try different URL types
            $absolutePath = $urlGenerator->generate($routeName, [], UrlGeneratorInterface::ABSOLUTE_PATH);
            echo "✓ Absolute Path: " . $absolutePath . "\n";

            $absoluteUrl = $urlGenerator->generate($routeName, [], UrlGeneratorInterface::ABSOLUTE_URL);
            echo "✓ Absolute URL: " . $absoluteUrl . "\n";

            $networkPath = $urlGenerator->generate($routeName, [], UrlGeneratorInterface::NETWORK_PATH);
            echo "✓ Network Path: " . $networkPath . "\n";

        } catch (\Exception $e) {
            echo "✗ Error generating URL: " . $e->getMessage() . "\n";
        }
    }

    // Method to debug URL generation with parameters
    public static function debugRouteWithParams(UrlGeneratorInterface $urlGenerator, string $routeName, array $params): void
    {
        echo "=== Debugging Route: $routeName ===\n";
        echo "Parameters: " . json_encode($params) . "\n";

        try {
            $url = $urlGenerator->generate($routeName, $params);
            echo "✓ Generated URL: " . $url . "\n";

        } catch (\Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
}

// ============================================================================
// SECURITY CONSIDERATIONS
// ============================================================================

/*

SECURITY CHECKLIST FOR URL GENERATION:

1. ✓ Always use route names, not hardcoded URLs
   - Hardcoded URLs can be missed during refactoring
   - Route names are centralized and easier to update

2. ✓ Use signed URLs for sensitive operations
   - Password resets
   - Email verification
   - Account recovery
   - Sensitive downloads

3. ✓ Set expiration on signed URLs
   - Password reset: 1 hour
   - Email verification: 24 hours
   - Download link: 2 hours

4. ✓ Use HTTPS for sensitive URLs
   - Set schemes(['https']) in route definition
   - Configure router.request_context.scheme: 'https'

5. ✓ Validate URL parameters before using
   - Check that IDs exist in database
   - Verify user permissions
   - Validate parameter types

6. ✓ Use absolute URLs for external communications
   - Emails
   - APIs
   - Webhooks
   - External redirects

7. ✓ Sanitize URLs in templates
   - Use {{ url|e }} in Twig to escape HTML
   - Use escape('js') for JavaScript: {{ url|escape('js') }}

8. ✓ Don't expose sensitive data in URLs
   - Use POST for sensitive operations
   - Use encrypted tokens instead of IDs
   - Mask personal information

*/
