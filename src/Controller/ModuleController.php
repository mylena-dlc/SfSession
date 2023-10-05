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

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

   
    public function __construct(ModuleRepository $moduleRepository, EntityManagerInterface $em)
    {
        $this->moduleRepository = $moduleRepository;
        $this->em = $em;

    }
    #[Route('/module', name: 'app_module')]
    public function index(): Response
    {
        $modules = $this->moduleRepository->findBy([], ['name' => 'ASC']);
        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }


    /**
    * Fonction pour ajouter ou éditer un module
    */
    
    #[Route('/module/new', name: 'new_module')]
    #[Route('/module/{id}/edit', name: 'edit_module')]

    public function new_edit(Module $module = null, Request $request): Response {
        if(!$module) {
            $module = new Module();
        }
    
        $form = $this->createForm(ModuleType::class, $module);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $module = $form->getData();
            $this->em->persist($module);
            $this->em->flush();
            return $this->redirectToRoute('app_module');
        }

        return $this->render('module/new.html.twig', [
            'formAddModule' => $form,
            'edit' => $module->getId()
        ]);

    }   

    /**
    * Fonction pour supprimer un module
    */
    
    #[Route('/module/{id}/delete', name: 'delete_module')]
    public function delete(Module $module) {

        // pour préparé l'objet $module à supprimer (enlever cet objet de la collection)
        $this->em->remove($module);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $this->em->flush();

        return $this->redirectToRoute('app_module');
    }

}
