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
    public function index(
        MovieRepository $repository,
        Request $request
    ): Response {

        $search = $request->query->get('search');
        $language = $request->query->get('language');

        $movie_list = $repository->searchAndFilter($search, $language);

        $languages = $repository->findAvailableLanguages();

        return $this->render('movie/movie.html.twig', [
            'movie_list' => $movie_list,
            'search' => $search,
            'language' => $language,
            'languages' => $languages
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

                return $this->redirectToRoute('app_home', [
                    'id' => $movie->getId()
                ]);
            }
        }

        $avgRating = $reviewRepository->getAverageRatingForMovie($movie->getId());
        $reviewCount = $reviewRepository->getReviewCountForMovie($movie->getId());

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'review_form' => isset($form) ? $form->createView() : null,
            'avgRating' => $avgRating,
            'reviewCount' => $reviewCount,
        ]);

    }

    #[Route('/mis-reviews', name: 'app_user_reviews')]
    public function myReviews(\App\Repository\ReviewRepository $reviewRepository): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $reviews = $reviewRepository->findBy(
            [
                'user' => $this->getUser(),
                'isActive' => true
            ],
            [
                'createdAt' => 'DESC'
            ]
        );

        return $this->render('movie/my_reviews.html.twig', [
            'reviews' => $reviews
        ]);

    }

}
