<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FormationRepository
     */
    private $formationRepository;
    

   
    public function __construct(FormationRepository $formationRepository, EntityManagerInterface $em)
    {
        $this->formationRepository = $formationRepository;
        $this->em = $em;

    }
    
    #[Route('/formation', name: 'app_formation')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findBy([], ['name' => 'ASC']);
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }
    

    /**
    * Fonction pour ajouter ou éditer une formation
    */

    #[Route('/formation/new', name: 'new_formation')]
    #[Route('/formation/{id}/edit', name: 'edit_formation')]

    public function new_edit(Formation $formation = null, Request $request): Response {
    
        if(!$formation) {
            $formation = new formation();
        }
    
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formation = $form->getData(); 
            // prepare en PDO
            $this->em->persist($formation);
            // execute PDO
            $this->em->flush();

            return $this->redirectToRoute('app_formation');
        }

        return $this->render('formation/new.html.twig', [
            'formAddFormation' => $form,
            'edit' => $formation->getId(),
        ]);

    }   

    /**
    * Fonction pour supprimer une formation
    */
   
    #[Route('/formation/{id}/delete', name: 'delete_formation')]
    public function delete(Formation $formation) {

        // pour préparé l'objet $formation à supprimer (enlever cet objet de la collection)
        $this->em->remove($formation);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $this->em->flush();

        return $this->redirectToRoute('app_formation');
    }



    /**
    * Fonction pour afficher les détails d'une formation
    */

    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(Formation $formation): Response {

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);

    }

}
