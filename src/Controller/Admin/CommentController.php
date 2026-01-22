<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\Admin\CommentAdminType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/comments')]
final class CommentController extends AbstractController
{
    #[Route('/', name: 'admin_comment_index', methods: ['GET'])]
    public function index(Request $request, CommentRepository $commentRepository): Response
    {
        $pending = $request->query->getBoolean('pending', false);

        $criteria = [];
        if ($pending) {
            $criteria['isApproved'] = false;
        }

        return $this->render('admin/comment/index.html.twig', [
            'comments' => $commentRepository->findBy($criteria, ['createdAt' => 'DESC']),
            'pending' => $pending,
        ]);
    }


    #[Route('/{id}', name: 'admin_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/approve', name: 'admin_comment_approve', methods: ['POST'])]
    public function approve(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('approve'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $comment->setIsApproved(true);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire approuvé.');
        return $this->redirectToRoute('admin_comment_index', ['pending' => true], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}/edit', name: 'admin_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Comment1Type::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
