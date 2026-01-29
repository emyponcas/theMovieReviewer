<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}
