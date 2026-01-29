<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_site')]
    public function index(): Response
    {


        return $this->render('admin/admin.html.twig');
    }

    #[Route('/admin/movie/load', name: 'data_load')]
    public function data_load(HttpClientInterface $httpClient, EntityManagerInterface $entityManager): Response
    {
        #PETICION API
        $response = $httpClient->request(
            'GET',
            'https://api.themoviedb.org/3/movie/popular?api_key=5964a5054bab9801d48f28b0251960ce&language=es-ES&page=1'
        );

        $content = $response->toArray();

        foreach ($content['results'] as $element) {
            $movie = new Movie();
            $movie->setTmdbId($element['id']);
            $movie->setTitle($element['title']);
            $movie->setOriginalTitle($element['original_title']);
            $movie->setOverview($element['overview']);
            if (!empty($element['release_date'])) {
                $movie->setReleaseDate(new \DateTime($element['release_date']));
            } else {
                $movie->setReleaseDate(null);
            }
            $movie->setPosterPath($element['poster_path']);
            $movie->setBackdropPath($element['backdrop_path']);
            $movie->setPopularity($element['popularity']);
            $movie->setVoteAverage($element['vote_average']);
            $movie->setVoteCount($element['vote_count']);
            $movie->setAdult($element['adult']);
            $movie->setVideo($element['video']);
            $movie->setOriginalLanguage($element['original_language']);
            if (!empty($element['create_at'])) {
                $movie->setCreatedAt(new \DateTime($element['create_at']));
            } else {
                $movie->setCreatedAt(null);
            }
            if (!empty($element['updated_at'])) {
                $movie->setUpdatedAt(new \DateTime($element['updated_at']));
            } else {
                $movie->setUpdatedAt(null);
            }

            $entityManager->persist($movie);
        }

        $entityManager->flush();



        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
            'content' => $content,
        ]);
    }
}
