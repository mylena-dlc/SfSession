<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\ProgramRepository;
use App\Repository\SessionRepository;
use App\Repository\StudentRepository;
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
        // on utilise le service SessionRepository pour récupérer toutes les sessions
        // findBy([], ['startDate' => 'ASC']) signifie : trouver toutes les sessions, triées par date de début (startDate) par ordre croissant (ASC)
        $sessions = $sessionRepository->findBy([], ['startDate' => 'ASC']);
        
        // on renvoie une réponse HTML en utilisant un template Twig
        // on passe les données de session (les sessions récupérées) au template pour les afficher.
        // 'sessions' est la variable utilisée dans le template Twig pour accéder aux sessions
        return $this->render('session/index.html.twig', [
            'sessions' => $sessions
        ]);
    }

    // Fonction pour ajouter ou editer une session
    #[Route('/session/new', name: 'new_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]

    public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response {
       
        // on vérifie si une session existe déjà, sinon on crée une nouvelle instance
        if(!$session) {
            $session = new Session();
        }

        // on crée un formulaire en utilisant la classe SessionType et associe la session à ce formulaire
        $form = $this->createForm(SessionType::class, $session);

        // la méthode handleRequest traite les données du formulaire
        $form->handleRequest($request);

        // on vérifie si le formulaire a été soumis et s'il est valide
        if($form->isSubmitted() && $form->isValid()) {

            $session = $form->getData(); // récupère les données du formulaire

            // persiste la session en BDD (enregistrer les données de l'objet Session en BDD, ajouter ou mettre à jour):
            $entityManager->persist($session); // indique à Doctrine de suivre cette instance de Session pour une eventuelle opération de persistance
            $entityManager->flush(); // envoie réellement les opérations en BDD
            return $this->redirectToRoute('app_session'); // redirection vers la page
        }

        // affiche la vue pour le formulaire d'ajout ou d'édition
        return $this->render('session/new.html.twig', [ // fonction render() génère une page HTML à partir du modèle template. l'argument est un tableau associatif qui permet de passer des données au template pour les afficher
            'form' => $form, // transmet le formulaire
            'edit' => $session->getId(), // transmet l'ID de la session actuelle (ajout ou edit)
            'sessionId' => $session->getId() // transmet aussi l'ID de la session mais sous un autre nom
        ]);

    }   

    // Fonction pour supprimer une session
    #[Route('/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, EntityManagerInterface $entityManager) {

        // pour préparé l'objet $session à supprimer (enlever cet objet de la collection)
        $entityManager->remove($session);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
    }


    // Fonction pour afficher les détails d'une session (+son programme)
    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, ProgramRepository $programRepository, SessionRepository $sessionRepository, $id): Response {

    // $session est un paramètre de la méthode qui est automatiquement injecté par Symfony
    // en fonction de la valeur de {id} dans l'URL. Cela signifie que Symfony va essayer
    // de trouver une session avec l'ID correspondant dans la base de données et l'injecter
    // en tant qu'objet Session dans cette méthode.

    // $programRepository est également injecté automatiquement par Symfony
    // et représente le repository (ou gestionnaire) des programmes (ou cours) dans votre application.

        // on utilise $programRepository pour rechercher les programmes associés à cette session
        $programs = $programRepository->findBy(['session' => $id ]);
        
        $notRegisteredStudents = $sessionRepository->findStudentsNotRegistered($id);
        // dd($notRegisteredStudents);

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'programs' => $programs,
            'notRegisteredStudents' => $notRegisteredStudents
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
