<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StudentController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var StudentRepository
     */
    private $studentRepository;

   
    public function __construct(StudentRepository $studentRepository, EntityManagerInterface $em)
    {
        $this->studentRepository = $studentRepository;
        $this->em = $em;

    }



    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        $students = $this->studentRepository->findBy([], ['lastname' => 'ASC']);
        return $this->render('student/index.html.twig', [
            'students' => $students,
        ]);
    }


    /**
     * Fonction pour ajouter ou editer un stagiaire
    */

    #[Route('/student/new', name: 'new_student')]
    #[Route('/student/{id}/edit', name: 'edit_student')]

    public function new_edit(Student $student = null, Request $request): Response {
        if(!$student) {
            $student = new Student();
        }
    
        $form = $this->createForm(StudentType::class, $student);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $student = $form->getData();
            $this->em->persist($student);
            $this->em->flush();
            return $this->redirectToRoute('app_student');
        }

        return $this->render('student/new.html.twig', [
            'formAddStudent' => $form,
            'edit' => $student->getId()
        ]);

    }   


    /**
     * Fonction pour supprimer un stagiaire
    */

    #[Route('/student/{id}/delete', name: 'delete_student')]
    public function delete(Student $student) {

        // pour préparé l'objet $student à supprimer (enlever cet objet de la collection)
        $this->em->remove($student);
        // flush va faire la requête SQL et concretement supprimer l'objet de la BDD
        $this->em->flush();

        return $this->redirectToRoute('app_student');
    }

    

    /**
     * Fonction pour afficher les détails d'un stagiaire
    */

    #[Route('/student/{id}', name: 'show_student')]
    public function show(Student $student): Response {

        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);

    }

}
