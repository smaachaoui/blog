<?php

namespace App\Controller;

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
    public function home(Request $request, PostRepository $postRepository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $query = $request->query->get('q');

        $limit = 6;
        $offset = ($page - 1) * $limit;

        $posts = $postRepository->search($query, $limit, $offset);
        $total = $postRepository->countSearch($query);

        $totalPages = (int) ceil($total / $limit);

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'query' => $query,
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

        return $this->render('blog/show.html.twig', [
            'post' => $post,
            'comments' => $commentRepository->findApprovedForPost($post->getId()),
            'commentForm' => $form,
        ]);
    }


}

