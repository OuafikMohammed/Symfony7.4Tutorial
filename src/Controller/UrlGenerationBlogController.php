<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * UrlGenerationBlogController demonstrates URL generation in Controllers
 * 
 * When your controller extends AbstractController, you get access to the 
 * generateUrl() helper method which makes it easy to create URLs
 */
class UrlGenerationBlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_list')]
    public function list(): Response
    {
        // STEP 1: Generate a simple URL with NO route arguments
        // This creates a URL for the 'sign_up' route without any parameters
        // Result: /sign-up (or whatever your sign_up route path is)
        $signUpPage = $this->generateUrl('sign_up');

        // STEP 2: Generate a URL WITH route arguments
        // The second parameter is an array containing route parameters
        // These parameters are defined in the route definition like {username}
        // Result: /user/john-doe (or whatever username is provided)
        $userProfilePage = $this->generateUrl('user_profile', [
            'username' => 'john-doe', // This replaces {username} in the route
        ]);

        // STEP 3: Generate ABSOLUTE URL instead of relative path
        // By default, generateUrl() returns "absolute paths" (relative URLs like /blog)
        // To get a full URL with domain (http://example.com/blog), pass a third parameter
        // UrlGeneratorInterface::ABSOLUTE_URL tells Symfony to include the full domain
        // Result: https://example.com/sign-up
        $signUpPageAbsolute = $this->generateUrl(
            'sign_up', 
            [], 
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // STEP 4: Generate URL for a LOCALIZED route (multi-language support)
        // When your app supports multiple languages, you can specify which locale
        // The '_locale' parameter is special - it tells Symfony which language version to use
        // Result: /nl/sign-up (the 'nl' prefix might be added depending on your routing)
        $signUpPageInDutch = $this->generateUrl('sign_up', [
            '_locale' => 'nl', // 'nl' = Dutch language code
        ]);

        // STEP 5: Handle EXTRA parameters (not defined in route)
        // If you pass parameters that aren't part of the route definition,
        // they get added as query string parameters (the ?key=value part of the URL)
        // Result: /blog?page=2&category=Symfony
        $blogWithFilters = $this->generateUrl('blog', [
            'page' => 2,              // This becomes ?page=2
            'category' => 'Symfony',  // This becomes &category=Symfony
        ]);

        // STEP 6: Convert objects to string for extra parameters
        // Important: Objects (like UUID) in extra parameters need to be explicitly converted
        // This is because objects have special handling in route parameters
        $blogWithUuid = $this->generateUrl('blog', [
            'uuid' => (string) '550e8400-e29b-41d4-a716-446655440000', // Cast to string!
        ]);

        // Create a response showing all generated URLs
        $content = sprintf(
            '<h1>Generated URLs Examples</h1>
            <p>Simple URL: %s</p>
            <p>With Parameters: %s</p>
            <p>Absolute URL: %s</p>
            <p>Localized (Dutch): %s</p>
            <p>With Query String: %s</p>
            <p>With UUID: %s</p>',
            htmlspecialchars($signUpPage),
            htmlspecialchars($userProfilePage),
            htmlspecialchars($signUpPageAbsolute),
            htmlspecialchars($signUpPageInDutch),
            htmlspecialchars($blogWithFilters),
            htmlspecialchars($blogWithUuid)
        );

        return new Response($content);
    }

    // Other URL generation options

    #[Route('/url-types', name: 'url_types')]
    public function urlTypes(): Response
    {
        // Different ways to reference URL generation constants
        
        // Type 1: ABSOLUTE_PATH (default)
        // Returns: /blog (just the path without domain)
        $path = $this->generateUrl('blog', [], UrlGeneratorInterface::ABSOLUTE_PATH);

        // Type 2: ABSOLUTE_URL 
        // Returns: https://example.com/blog (full URL with protocol and domain)
        $absoluteUrl = $this->generateUrl('blog', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // Type 3: NETWORK_PATH
        // Returns: //example.com/blog (protocol-relative URL)
        // Useful when switching between http and https
        $networkPath = $this->generateUrl('blog', [], UrlGeneratorInterface::NETWORK_PATH);

        $content = sprintf(
            '<h1>Different URL Types</h1>
            <p>Absolute Path (default): %s</p>
            <p>Absolute URL: %s</p>
            <p>Network Path: %s</p>',
            htmlspecialchars($path),
            htmlspecialchars($absoluteUrl),
            htmlspecialchars($networkPath)
        );

        return new Response($content);
    }
}
