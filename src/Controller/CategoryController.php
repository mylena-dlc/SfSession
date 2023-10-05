<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

       
    public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepository)
    {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
    }


    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findBy([], ['name' => 'ASC']);
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
    * Fonction pour ajouter ou éditer une catégorie
    */
 
    #[Route('/category/new', name: 'new_category')]
    #[Route('/category/{id}/edit', name: 'edit_category')]

    public function new_edit(Category $category = null, Request $request): Response {
    

        if(!$category) {
            $category = new Category();
        }
    
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form->getData(); 
            // prepare en PDO
            $this->em->persist($category);
            // execute PDO
            $this->em->flush();

            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new.html.twig', [
            'formAddCategory' => $form,
            'edit' => $category->getId()
        ]);

    }   


    /**
    * Fonction pour supprimer une catégorie
    */
    
    #[Route('/category/{id}/delete', name: 'delete_category')]
    public function delete(Category $category) {

        // pour préparé l'objet $category à supprimer (enlever cet objet de la collection)
        $this->em->remove($category);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $this->em->flush();

        return $this->redirectToRoute('app_category');
    }

    
    /**
    * Fonction pour afficher les détails d'une catégorie
    */
  
    #[Route('/category/{id}', name: 'show_category')]
    public function show(Category $category): Response {

        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);

    }



  
}
