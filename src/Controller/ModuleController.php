<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(ModuleRepository $moduleRepository): Response
    {
        $modules = $moduleRepository->findBy([], ['name' => 'ASC']);
        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    // Fonction pour ajouter ou éditer un module
    #[Route('/module/new', name: 'new_module')]
    #[Route('/module/{id}/edit', name: 'edit_module')]

    public function new_edit(Module $module = null, Request $request, EntityManagerInterface $entityManager): Response {
        if(!$module) {
            $module = new Module();
        }
    
        $form = $this->createForm(ModuleType::class, $module);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $module = $form->getData();
            $entityManager->persist($module);
            $entityManager->flush();
            return $this->redirectToRoute('app_module');
        }

        return $this->render('module/new.html.twig', [
            'formAddModule' => $form,
            'edit' => $module->getId()
        ]);

    }   

    // Fonction pour supprimer un module
    #[Route('/module/{id}/delete', name: 'delete_module')]
    public function delete(Module $module, EntityManagerInterface $entityManager) {

        // pour préparé l'objet $module à supprimer (enlever cet objet de la collection)
        $entityManager->remove($module);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $entityManager->flush();

        return $this->redirectToRoute('app_module');
    }

}
