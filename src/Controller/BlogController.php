<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog/list', name: 'blog_list')]
    public function list(): Response
    {
        // Normally, you would fetch blog posts from a database
        $posts = [
            ['title' => 'First Post', 'content' => 'This is the first blog post.'],
            ['title' => 'Second Post', 'content' => 'This is the second blog post.'],
        ];

        return $this->render('blog/list.html.twig', [
            'posts' => $posts,
        ]);
    }
}