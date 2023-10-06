<?php

namespace App\Controller;

use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(SessionRepository $sessionRepository): Response
    {
        // on recupère toutes les sessions
        $sessions = $sessionRepository->findBy([], ['startDate' => 'ASC']);

        // on récupère la date actuelle
        $currentDate = new \DateTime();

        // on sépare les sessions en cours et les sessions passées
        $currentSessions = [];
        $pastSessions = [];

        // on parcourt le tableau des sessions
        foreach ($sessions as $session) {
            if($session->getEndDate() >= $currentDate) { // si la date de fin est supérieur à la date du jour
                $currentSessions[] = $session; // alors cette sessions ira dans le tableau des sessions en cours
            } else {
                $pastSessions[] = $session; // sinon la session ira dans le tableau des sessions passées
            }
        }
        
        return $this->render('home/index.html.twig', [
            'currentSessions' => $currentSessions,
            'pastSessions' => $pastSessions
        ]);
    }
}

