<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\ProgramRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findBy([], ['startDate' => 'ASC']);
        
    // Créez un tableau pour stocker les programmes associés à chaque session
    // $programs = [];

    // Bouclez sur les sessions pour obtenir les programmes associés
    // foreach ($sessions as $session) {
    //     $programs[$session->getId()] = $session->getPrograms();
    // }

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
            // 'programs' => $programs,
    
        ]);
    }

    #[Route('/session/new', name: 'new_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]

    public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response {
        if(!$session) {
            $session = new Session();
        }
    
        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $session = $form->getData();
            $entityManager->persist($session);
            $entityManager->flush();
            return $this->redirectToRoute('app_session');
        }

        // vue pour afficher le formulaire d'ajout
        return $this->render('session/new.html.twig', [
            'form' => $form,
            'edit' => $session->getId(),
            'sessionId' => $session->getId()
        ]);

    }   

    #[Route('/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, EntityManagerInterface $entityManager) {

        // pour préparé l'objet $session à supprimer (enlever cet objet de la collection)
        $entityManager->remove($session);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
    }


    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, ProgramRepository $programRepository, $id): Response {

        $programs = $programRepository->findBy(['session' => $id ]);

// dd($programs);
        return $this->render('session/show.html.twig', [
            'session' => $session,
            'programs' => $programs
        ]);

    }

    
    // #[Route('/session/{id}/', name: 'add_student')]

    // public function addStudent(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response {
        


    //     $session->addStudent($student);
      
    //     $form = $this->createForm(SessionType::class, $session);

    //     $form->handleRequest($request);


    //     if($form->isSubmitted() && $form->isValid()) {

    //         $session = $form->getData();
    //         $entityManager->persist($session);
    //         $entityManager->flush();
    //         return $this->redirectToRoute('app_session');
    //     }

    //     return $this->render('session/new.html.twig', [
    //         'formAddSession' => $form,
    //         'edit' => $session->getId()
    //     ]);

    // }   


}
