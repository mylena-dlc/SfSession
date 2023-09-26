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
    
    #[Route('/formation', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findBy([], ['name' => 'ASC']);
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }
    
    #[Route('/formation/new', name: 'new_formation')]
    #[Route('/formation/{id}/edit', name: 'edit_formation')]

    public function new_edit(Formation $formation = null, Request $request, EntityManagerInterface $entityManager): Response {
    
        if(!$formation) {
            $formation = new formation();
        }
    
        $form = $this->createForm(FormationType::class, $formation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formation = $form->getData(); 
            // prepare en PDO
            $entityManager->persist($formation);
            // execute PDO
            $entityManager->flush();

            return $this->redirectToRoute('app_formation');
        }

        return $this->render('formation/new.html.twig', [
            'formAddFormation' => $form,
            'edit' => $formation->getId()
        ]);

    }   



    #[Route('/formation/{id}/delete', name: 'delete_formation')]
    public function delete(Formation $formation, EntityManagerInterface $entityManager) {

        // pour préparé l'objet $formation à supprimer (enlever cet objet de la collection)
        $entityManager->remove($formation);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $entityManager->flush();

        return $this->redirectToRoute('app_formation');
    }


    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(Formation $formation): Response {

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);

    }

}
