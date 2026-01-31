<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(MovieRepository $movieRepository): Response
    {
        // Solo obtenemos las películas, sin estadísticas complejas
        $movies = $movieRepository->findAllOrderedByVoteCount();

        return $this->render('home/home.html.twig', [
            'movies' => $movies,
        ]);
    }
}
