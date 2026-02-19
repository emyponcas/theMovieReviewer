<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire; #para obtener la apikey
use Symfony\Component\HttpFoundation\Request;
use App\Form\MovieType;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_site')]
    public function index(): Response
    {


        return $this->render('admin/admin.html.twig');
    }

    #[Route('/admin/movie/load', name: 'data_load')]
    public function data_load(HttpClientInterface $httpClient
        , EntityManagerInterface $entityManager
        , #[Autowire('%tmdb_api_key%')] string $apiKey
        , #[Autowire('%tmdb_api_url%')] string $apiUrl
        , MovieRepository $movieRepository): Response
    {
        $randomPage = random_int(1, 500);

        #PETICION API
        $response = $httpClient->request(
            'GET',
            $apiUrl . '/movie/top_rated?api_key=' . $apiKey . '&language=es-ES&page=' . $randomPage
        );

        $content = $response->toArray();

        $peliculasInsertadas = 0;
        $peliculasDuplicadas = 0;

        foreach ($content['results'] as $element) {

            $existingMovie = $movieRepository->findOneBy(['tmdb_id' => $element['id']]);

            if ($existingMovie) {
                $peliculasDuplicadas++;

                continue; // salto a la siguiente iteración
            }

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

            $peliculasInsertadas++;

            $entityManager->persist($movie);
        }

        $entityManager->flush();

        $this->addFlash(
            'success',
            sprintf('Carga completada: %d nuevas películas insertadas, %d duplicadas omitidas.',
                $peliculasInsertadas,
                $peliculasDuplicadas
            )
        );



        return $this->render('admin/admin.html.twig', [
            'controller_name' => 'AdminController',
            'insertadas' => $peliculasInsertadas,
            'duplicadas' => $peliculasDuplicadas
        ]);
    }

    #[Route('/admin/movie/new', name: 'admin_movie_new')]
    public function newMovie(Request $request, EntityManagerInterface $em): Response
    {
        $movie = new Movie();

        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $movie->setTmdbId(random_int(1000000, 9999999));

            $em->persist($movie);

            $em->flush();

            return $this->redirectToRoute('admin_site');
        }

        return $this->render('admin/movieForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
