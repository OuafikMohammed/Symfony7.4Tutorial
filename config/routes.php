<?php
use App\Controller\Api\PostController as ApiPostController;
use App\Controller\PostController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    // Pour les routes API des posts
    // Valeur de retour de chaque méthode dans Api/PostController.php sera JsonResponse
    $routes->add('api_posts_index', '/api/posts')
        ->controller([ApiPostController::class, 'index'])
        ->methods(['GET']);

    $routes->add('api_posts_show', '/api/posts/{id}')
        ->controller([ApiPostController::class, 'show'])
        ->methods(['GET']);

    $routes->add('api_posts_create', '/api/posts')
        ->controller([ApiPostController::class, 'create'])
        ->methods(['POST']);

    $routes->add('api_posts_update', '/api/posts/{id}')
        ->controller([ApiPostController::class, 'update'])
        ->methods(['PUT']);

    $routes->add('api_posts_delete', '/api/posts/{id}')
        ->controller([ApiPostController::class, 'delete'])
        ->methods(['DELETE']);
    
    //Pour Landing page des posts (non-API)
    // Valeur de retour de chaque méthode dans PostController.php sera Response
    // Explication de différence entre API et non-API dans le cours
    // Les routes ci-dessous pointent vers src/Controller/PostController.php
    // IMPORTANT: Routes with specific paths must come BEFORE routes with parameters
    
    $routes->add('post_index', '/posts')
        ->controller([PostController::class, 'index'])
        ->methods(['GET']);

    $routes->add('post_create', '/posts/create')
        ->controller([PostController::class, 'create'])
        ->methods(['GET', 'POST']);

    $routes->add('post_edit', '/posts/{id}/edit')
        ->controller([PostController::class, 'edit'])
        ->methods(['GET', 'POST']);

    $routes->add('post_delete', '/posts/{id}/delete')
        ->controller([PostController::class, 'delete'])
        ->methods(['POST']);

    $routes->add('post_show', '/posts/{id}')
        ->controller([PostController::class, 'show'])
        ->methods(['GET']);
};
