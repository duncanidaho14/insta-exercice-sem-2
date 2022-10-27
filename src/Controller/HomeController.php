<?php

namespace App\Controller;


use App\Repository\VideoGamesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name:'app_home')]
    public function index(VideoGamesRepository $videoGames): Response
    {
        return $this->render('home/index.html.twig', [
            'games' => $videoGames->findAll(),
        ]);
    }

    #[Route('/home/{id}', name:'app_show')]
    public function show(VideoGamesRepository $videoGames, string $id): Response
    {
        return $this->render('home/show.html.twig', [
            'game' => $videoGames->findOneById(['id' => $id]),
        ]);
    }

    #[Route('/blog', name:'app_blog')]
    public function aboutCVElhadi(): Response
    {
        return $this->file('./../public/CV_2022-10-26_Elhadi_Beddarem.pdf', null, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
