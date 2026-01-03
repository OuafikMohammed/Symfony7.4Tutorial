<?php
// src/Controller/PostController.php
namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'post_index', methods: ['GET'])]
    // Old code (without param converter):
    // public function index(EntityManagerInterface $em): Response
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $em->getRepository(Post::class)->findAll(),
        ]);
    }

    #[Route('/posts/create', name: 'post_create', methods: ['GET', 'POST'])]
    // Old code (without param converter):
    // public function create(Request $request, EntityManagerInterface $em): Response
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $post = new Post();
            $post->setTitle($request->request->get('title'));
            $post->setContent($request->request->get('content'));

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/create.html.twig');
    }

    #[Route('/posts/{id:post}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    // Old code (without param converter):
    // #[Route('/posts/{id}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    // public function edit(Post $post, Request $request, EntityManagerInterface $em): Response
    public function edit(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $post->setTitle($request->request->get('title'));
            $post->setContent($request->request->get('content'));

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/posts/{id:post}/delete', name: 'post_delete', methods: ['POST'])]
    // Old code (without param converter):
    // #[Route('/posts/{id}/delete', name: 'post_delete', methods: ['POST'])]
    // public function delete(Post $post, EntityManagerInterface $em): Response
    public function delete(Post $post, EntityManagerInterface $em): Response
    {
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('post_index');
    }

    #[Route('/posts/{id:post}', name: 'post_show', methods: ['GET'])]
    // Old code (without param converter):
    // #[Route('/posts/{id}', name: 'post_show', methods: ['GET'])]
    // public function show(Post $post): Response
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
