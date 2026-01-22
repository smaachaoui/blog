<?php

namespace App\Controller\Dashboard;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Security\Voter\PostVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/dashboard/posts')]
class PostController extends AbstractController
{
    #[Route('/', name: 'dashboard_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $user = $this->getUser();

        return $this->render('dashboard/post/index.html.twig', [
            'posts' => $postRepository->findBy(['author' => $user], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'dashboard_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::CREATE);

        $post = new Post();
        $post->setAuthor($this->getUser());

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            /** @var UploadedFile|null $coverFile */
            $coverFile = $form->get('coverImageFile')->getData();

            if ($coverFile !== null) {
                $filename = bin2hex(random_bytes(16)).'.'.($coverFile->guessExtension() ?: 'bin');
                $coverFile->move($this->getParameter('covers_directory'), $filename);
                $post->setCoverImage($filename);
            }

            if (!$post->getSlug()) {
                $baseSlug = strtolower($slugger->slug($post->getTitle())->toString());
                $post->setSlug($this->makeUniquePostSlug($baseSlug, $entityManager));
            }

            $post->setUpdatedAt(new \DateTimeImmutable());



            $entityManager->flush();

            return $this->redirectToRoute('dashboard_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'dashboard_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUpdatedAt(new \DateTimeImmutable());
            /** @var UploadedFile|null $coverFile */
            $coverFile = $form->get('coverImageFile')->getData();

            if ($coverFile !== null) {
                $filesystem = new Filesystem();

                $oldFilename = $post->getCoverImage();
                if ($oldFilename) {
                    $oldPath = $this->getParameter('covers_directory').'/'.$oldFilename;
                    if ($filesystem->exists($oldPath)) {
                        $filesystem->remove($oldPath);
                    }
                }

                $filename = bin2hex(random_bytes(16)).'.'.($coverFile->guessExtension() ?: 'bin');
                $coverFile->move($this->getParameter('covers_directory'), $filename);
                $post->setCoverImage($filename);
            }

            $post->setUpdatedAt(new \DateTimeImmutable());

            if (!$post->getSlug()) {
                $baseSlug = strtolower($slugger->slug($post->getTitle())->toString());
                $post->setSlug($this->makeUniquePostSlug($baseSlug, $entityManager));
            }

            $entityManager->flush();

            return $this->redirectToRoute('dashboard_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'dashboard_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(PostVoter::DELETE, $post);

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $filesystem = new Filesystem();

            $filename = $post->getCoverImage();
            if ($filename) {
                $path = $this->getParameter('covers_directory').'/'.$filename;
                if ($filesystem->exists($path)) {
                    $filesystem->remove($path);
                }
            }

            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard_post_index', [], Response::HTTP_SEE_OTHER);
    }

    private function makeUniquePostSlug(string $baseSlug, EntityManagerInterface $entityManager): string
    {
        $slug = $baseSlug;
        $i = 2;

        while ($entityManager->getRepository(\App\Entity\Post::class)->findOneBy(['slug' => $slug]) !== null) {
            $slug = $baseSlug.'-'.$i;
            $i++;
        }

        return $slug;
    }


}
