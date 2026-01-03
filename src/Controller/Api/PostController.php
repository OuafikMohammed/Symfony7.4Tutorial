<?php
namespace App\Controller\Api;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    // GET /api/posts
    public function index(): JsonResponse
    {
        $posts = $this->em->getRepository(Post::class)->findAll();

        $data = array_map(fn(Post $post) => [
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
        ], $posts);

        return new JsonResponse($data);
    }

    // GET /api/posts/{id}
    public function show(Post $post): JsonResponse
    {
        return new JsonResponse([
            'id' => $post->getId(),
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
        ]);
    }

    // POST /api/posts
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $post = new Post();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);

        $this->em->persist($post);
        $this->em->flush();

        return new JsonResponse(['message' => 'Post created'], 201);
    }

    // PUT /api/posts/{id}
    public function update(Post $post, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $post->setTitle($data['title']);
        $post->setContent($data['content']);

        $this->em->flush();

        return new JsonResponse(['message' => 'Post updated']);
    }

    // DELETE /api/posts/{id}
    public function delete(Post $post): JsonResponse
    {
        $this->em->remove($post);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}
