<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CategoryRanking;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\CategoryRankingRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/category', name: 'app_category_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(
            ['isActive' => true],
            ['createdAt' => 'DESC']
        );

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/category/new', name: 'app_category_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        MovieRepository $movieRepository
    ): Response {

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        $movies = $movieRepository->findBy(
            ['isActive' => true],
            ['title' => 'ASC']
        );

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setUser($this->getUser());

            $selectedMovies = $request->request->all('selected_movies');

            if ($selectedMovies) {

                foreach ($selectedMovies as $movieId) {

                    $movie = $movieRepository->find($movieId);

                    if ($movie) {
                        $category->addMovie($movie);
                    }

                }

            }

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('app_category_index');

        }

        return $this->render('category/categoryForm.html.twig', [
            'form' => $form->createView(),
            'movies' => $movies,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/category/{id}', name: 'app_category_show')]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/categories', name: 'public_category_index')]
    public function publicIndex(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy(
            ['isActive' => true],
            ['name' => 'ASC']
        );

        return $this->render('category/public_index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/categories/{id}', name: 'public_category_show')]
    public function publicShow(
        Category $category,
        Request $request,
        EntityManagerInterface $em,
        MovieRepository $movieRepository,
        CategoryRankingRepository $rankingRepository
    ): Response {

        $user = $this->getUser();

        if ($request->isMethod('POST')) {

            $existingRankings = $rankingRepository->findBy([
                'user' => $user,
                'category' => $category
            ]);

            foreach ($existingRankings as $ranking) {
                $em->remove($ranking);
            }

            $em->flush();

            $rankingData = json_decode($request->request->get('rankingData'), true);

            foreach ($rankingData as $movieId => $position) {

                $movie = $movieRepository->find($movieId);

                if (!$movie) continue;

                $ranking = new CategoryRanking();

                $ranking->setUser($user);
                $ranking->setCategory($category);
                $ranking->setMovie($movie);
                $ranking->setPosition((int)$position);

                $em->persist($ranking);

            }

            $em->flush();

            return $this->redirectToRoute('public_category_index', [
                'id' => $category->getId()
            ]);

        }

        $existingRankings = $rankingRepository->findBy([
            'user' => $user,
            'category' => $category
        ]);

        $rankingMap = [];

        foreach ($existingRankings as $ranking) {
            $rankingMap[$ranking->getMovie()->getId()] = $ranking->getPosition();
        }

        return $this->render('category/public_show.html.twig', [
            'category' => $category,
            'movies' => $category->getMovies(),
            'rankingMap' => $rankingMap
        ]);
    }

    #[Route('/categories/{id}/leaderboard', name: 'category_leaderboard')]
    public function leaderboard(
        Category $category,
        CategoryRankingRepository $rankingRepository
    ): Response {

        $leaderboard = $rankingRepository->getLeaderboard($category->getId());

        $totalParticipants = $rankingRepository->getTotalParticipants($category->getId());

        return $this->render('category/leaderboard.html.twig', [
            'category' => $category,
            'leaderboard' => $leaderboard,
            'totalParticipants' => $totalParticipants
        ]);
    }



}
