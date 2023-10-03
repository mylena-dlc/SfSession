<?php

namespace App\Controller;

use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProgramController extends AbstractController
{
    #[Route('/program', name: 'app_program')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findBy([]);
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }


    // Fonction pour ajouter ou éditer un programme
    #[Route('/program/new', name: 'new_program')]
    #[Route('/program/{id}/edit', name: 'edit_program')]

    public function new_edit(Program $program = null, Request $request, EntityManagerInterface $entityManager): Response {
        if(!$program) {
            $program = new Program();
        }
    
        $form = $this->createForm(ProgramType::class, $program);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $program = $form->getData();
            $entityManager->persist($program);
            $entityManager->flush();
            return $this->redirectToRoute('app_program');
        }

        return $this->render('program/new.html.twig', [
            'formAddProgram' => $form,
            'edit' => $program->getId()
        ]);

    }   


    // Fonction pour supprimer un programme
    #[Route('/program/{id}/delete', name: 'delete_program')]
    public function delete(Program $program, EntityManagerInterface $entityManager) {

        // pour préparé l'objet $program à supprimer (enlever cet objet de la collection)
        $entityManager->remove($program);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $entityManager->flush();

        return $this->redirectToRoute('app_program');
    }


    // Fonction pour afficher les détails d'un programme
    #[Route('/program/{id}', name: 'show_program')]
    public function show(Program $program): Response {

        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);

    }
}
