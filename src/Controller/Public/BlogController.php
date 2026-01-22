<?php

namespace App\Controller\Public;

use App\Entity\Post;
use App\Repository\PostRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;





class BlogController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(PostRepository $postRepository): Response
    {
        $latestPosts = $postRepository->findBy([], ['createdAt' => 'DESC'], 6);

        return $this->render('public/home/index.html.twig', [
            'posts' => $latestPosts,
        ]);
    }


    #[Route('/articles', name: 'blog_index', methods: ['GET'])]
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $q = trim((string) $request->query->get('q', ''));
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 9;
        $offset = ($page - 1) * $limit;

        if ($q !== '') {
            $posts = $postRepository->search($q, $limit, $offset);
            $total = $postRepository->countSearch($q);
        } else {
            $posts = $postRepository->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
            $total = $postRepository->count([]);
        }

        $totalPages = (int) ceil($total / $limit);

        return $this->render('public/blog/index.html.twig', [
            'posts' => $posts,
            'query' => $q,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }



    #[Route('/blog/{slug}', name: 'blog_show', methods: ['GET', 'POST'])]
    public function show(
        PostRepository $postRepository,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        string $slug
    ): Response {
        $post = $postRepository->findOneBy(['slug' => $slug]);
        if ($post === null) {
            throw $this->createNotFoundException();
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire envoyé. Il sera visible après validation.');
            return $this->redirectToRoute('blog_show', ['slug' => $slug], Response::HTTP_SEE_OTHER);
        }

        return $this->render('public/blog/show.html.twig', [
            'post' => $post,
            'comments' => $commentRepository->findApprovedForPost($post->getId()),
            'commentForm' => $form,
        ]);
    }


}

