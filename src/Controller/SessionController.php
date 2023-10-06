<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Student;
use App\Form\SessionType;
use App\Repository\FormationRepository;
use App\Repository\ProgramRepository;
use App\Repository\SessionRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ProgramRepository
     */
    private $programRepository;

    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * @var FormationRepository
     */
    private $formationRepository;

   
    public function __construct(SessionRepository $sessionRepository, EntityManagerInterface $em, ProgramRepository $programRepository,FormationRepository $formationRepository, StudentRepository $studentRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->em = $em;
        $this->programRepository = $programRepository;
        $this->studentRepository = $studentRepository;
        $this->formationRepository = $formationRepository;
    }

    #[Route('/session', name: 'app_session')]
    public function index(): Response
    {
        // on utilise le service SessionRepository pour récupérer toutes les sessions
        // findBy([], ['startDate' => 'ASC']) signifie : trouver toutes les sessions, triées par date de début (startDate) par ordre croissant (ASC)
        $sessions = $this->sessionRepository->findBy([], ['startDate' => 'DESC']);

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

        // on renvoie une réponse HTML en utilisant un template Twig
        // on passe les données de session (les sessions récupérées) au template pour les afficher.
        // 'sessions' est la variable utilisée dans le template Twig pour accéder aux sessions
        return $this->render('session/index.html.twig', [
            'currentSessions' => $currentSessions,
            'pastSessions' => $pastSessions
        ]);
    }



    /**
    * Fonction pour ajouter une session
    */

    #[Route('/{formation_id}/session/new', name: 'new_session')]

    public function new( Request $request, $formation_id): Response {
       
        // on crée une nouvelle instance
        $session = new Session();
    
        // on cherche l'id de la formation
        $formation = $this->formationRepository->find($formation_id);

        // on crée un formulaire en utilisant la classe SessionType et associe la session à ce formulaire
        $form = $this->createForm(SessionType::class, $session);

        // la méthode handleRequest traite les données du formulaire
        $form->handleRequest($request);

        // on vérifie si le formulaire a été soumis et s'il est valide
        if($form->isSubmitted() && $form->isValid()) {

            $session = $form->getData(); // récupère les données du formulaire

            $session->setFormation($formation); // on ajoute l'id de la formation à session
            
            // persiste la session en BDD (enregistrer les données de l'objet Session en BDD, ajouter ou mettre à jour):
            $this->em->persist($session); // indique à Doctrine de suivre cette instance de Session pour une eventuelle opération de persistance
            $this->em->flush(); // envoie réellement les opérations en BDD
            return $this->redirectToRoute('show_formation' , ['id' => $formation_id]); // redirection vers la page
        }

        // affiche la vue pour le formulaire d'ajout ou d'édition
        return $this->render('session/new.html.twig', [ // fonction render() génère une page HTML à partir du modèle template. l'argument est un tableau associatif qui permet de passer des données au template pour les afficher
            'form' => $form, // transmet le formulaire
            'edit' => $session->getId(), // transmet l'ID de la session actuelle (ajout ou edit)
            'sessionId' => $session->getId() // transmet aussi l'ID de la session mais sous un autre nom
        ]);

    }   


    /**
    * Fonction pour editer une session
    */

  
    #[Route('formaton/{formation_id}/session/{id}/edit', name: 'edit_session')]

    public function edit(Session $session = null, Request $request, $formation_id): Response {
       

        // on crée un formulaire en utilisant la classe SessionType et associe la session à ce formulaire
        $form = $this->createForm(SessionType::class, $session);

        // la méthode handleRequest traite les données du formulaire
        $form->handleRequest($request);

        // on vérifie si le formulaire a été soumis et s'il est valide
        if($form->isSubmitted() && $form->isValid()) {

            $session = $form->getData(); // récupère les données du formulaire


            // persiste la session en BDD (enregistrer les données de l'objet Session en BDD, ajouter ou mettre à jour):
            $this->em->persist($session); // indique à Doctrine de suivre cette instance de Session pour une eventuelle opération de persistance
            $this->em->flush(); // envoie réellement les opérations en BDD
            return $this->redirectToRoute('show_formation' , ['id' => $formation_id]); // redirection vers la page
        }

        // affiche la vue pour le formulaire d'ajout ou d'édition
        return $this->render('session/new.html.twig', [ // fonction render() génère une page HTML à partir du modèle template. l'argument est un tableau associatif qui permet de passer des données au template pour les afficher
            'form' => $form, // transmet le formulaire
            'edit' => $session->getId(), // transmet l'ID de la session actuelle (ajout ou edit)
            'sessionId' => $session->getId() // transmet aussi l'ID de la session mais sous un autre nom
        ]);

    }   

    

    /**
    * Fonction pour supprimer une session
    */

    #[Route('formation/{formation_id}/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, $formation_id) {

        

        // pour préparé l'objet $session à supprimer (enlever cet objet de la collection)
        $this->em->remove($session);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $this->em->flush();

        // on redirige vers le détail de la formation en récupérant l'id de la formation dans la route (et le bouton de suppression)
        return $this->redirectToRoute('show_formation', ['id' => $formation_id ]);
    }



    /**
    * Fonction pour afficher les détails d'une session (+son programme)
    */

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session, $id): Response {

    // $session est un paramètre de la méthode qui est automatiquement injecté par Symfony
    // en fonction de la valeur de {id} dans l'URL. Cela signifie que Symfony va essayer
    // de trouver une session avec l'ID correspondant dans la base de données et l'injecter
    // en tant qu'objet Session dans cette méthode.

    // $programRepository est également injecté automatiquement par Symfony
    // et représente le repository (ou gestionnaire) des programmes (ou cours) dans votre application.

        // on utilise $programRepository pour rechercher les programmes associés à cette session
        $programs = $this->programRepository ->findBy(['session' => $id ]);
        
        $notRegisteredStudents = $this->sessionRepository->findStudentsNotRegistered($session->getId());

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'programs' => $programs,
            'notRegisteredStudents' => $notRegisteredStudents
        ]);

    }

    
   
    /**
    * Fonction pour inscrire un stagiaire à une session
    */
   
    #[Route('/session/{id}/show/add/{student_id}', name: 'add_student_session')]
    public function addStudentsToSession(Session $session, $student_id): Response
    {
        // si les places restantes sont supérieur à 0, alors je peux inscrire un stagiaire
        if($session->getNbPlacesRemaining() > 0) {

            $student = $this->studentRepository->find($student_id);
            
            // on ajoute le stagiaire à la session grâce à addStudent présente dans l'entité Session
            $session->addStudent($student);
            // on persiste les modifications en BDD
            $this->em->persist($session);
            $this->em->flush();
            $this->addFlash('success', 'Le stagiaire a bien été inscrit !');

        } else {
            $this->addFlash('error', 'La formation est complète vous ne pouvez plus inscrire de stagiaire !');
        }           
        
        // redirection vers la page de détail de la session
            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }



    /**
    * Fonction pour désinscrire un stagiaire à une session
    */
   
    #[Route('/session/{id}/show/delete/{student_id}', name: 'delete_student_session')]
    public function deleteStudentsToSession(Session $session, $student_id): Response
    {

        $student = $this->studentRepository->find($student_id);
        
        try {

            // on supprime le stagiaire à la session grâce à removeStudent présente dans l'entité
            $session->removeStudent($student);
            // on persiste les modifications en BDD
            $this->em->persist($session);
            $this->em->flush();

            $this->addFlash('success', 'Le stagiaire a bien été désinscrit !');


        } catch (\Exception $e) {

            $this->addFlash('error', "Le stagiaire n/'a pas été supprimé !");
        }

        // redirection vers la page de détail de la session
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);

    }
}



