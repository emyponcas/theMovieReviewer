<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Movie;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReviewRepository;
use App\Entity\Review;
use App\Form\ReviewType;


final class MovieController extends AbstractController
{
    #[Route('/movie', name: 'app_movie')]
    public function index(MovieRepository $repository): Response
    {

        $movie_list = $repository->findAll();



        return $this->render('movie/movie.html.twig', [
            'controller_name' => 'MovieController',
            'movie_list' => $movie_list,
        ]);
    }

    #[Route('/movie/{id}', name: 'app_movie_show')]
    public function show(
        Movie $movie,
        Request $request,
        EntityManagerInterface $em,
        ReviewRepository $reviewRepository
    ): Response {

        $user = $this->getUser();

        $review = null;

        if ($user) {
            $review = $reviewRepository->findOneBy([
                'user' => $user,
                'movie' => $movie
            ]);

            if (!$review) {
                $review = new Review();
                $review->setUser($user);
                $review->setMovie($movie);
            }

            $form = $this->createForm(ReviewType::class, $review);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($review);
                $em->flush();

                return $this->redirectToRoute('app_movie_show', [
                    'id' => $movie->getId()
                ]);
            }
        }

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'review_form' => isset($form) ? $form->createView() : null,
        ]);
    }
}
