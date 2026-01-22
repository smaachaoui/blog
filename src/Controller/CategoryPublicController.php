<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryPublicController extends AbstractController
{
    #[Route('/categories', name: 'category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category_public/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/category/{slug}', name: 'category_show', methods: ['GET'])]
    public function show(
        string $slug,
        Request $request,
        CategoryRepository $categoryRepository,
        PostRepository $postRepository
    ): Response {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        if ($category === null) {
            throw $this->createNotFoundException();
        }

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 6;
        $offset = ($page - 1) * $limit;

        $posts = $postRepository->findBy(
            ['category' => $category],
            ['createdAt' => 'DESC'],
            $limit,
            $offset
        );

        $total = $postRepository->count(['category' => $category]);
        $totalPages = (int) ceil($total / $limit);

        return $this->render('category_public/show.html.twig', [
            'category' => $category,
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}
