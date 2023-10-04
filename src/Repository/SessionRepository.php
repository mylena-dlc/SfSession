<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Student;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }


//     public function findNoRegistered($id) {

//         return $this->createQueryBuilder('s')
//             ->leftJoin('s.students', 'students')  // Jointure entre la session et les étudiants inscrits
//             ->andWhere(':id NOT MEMBER OF s.students')  // Filtre pour sélectionner les étudiants non inscrits
//             ->setParameter('id', $id)  // Définition du paramètre :sessionId avec la session donnée
//             ->getQuery()  // Création de la requête
//             ->getResult();  // Exécution de la requête et récupération des résultats
// }

// public function findStudentsNotRegistered($sessionId) {
//     $qb = $this->createQueryBuilder('s');

//     // Requête pour récupérer les étudiants inscrits à la session
//     $registeredStudents = $qb
//         ->select('s.id')
//         ->leftJoin('s.students', 'registeredStudents')
//         ->where('s.id = :sessionId')
//         ->setParameter('sessionId', $sessionId)
//         ->getQuery()
//         ->getResult();

//     // Requête pour récupérer les étudiants qui ne sont pas inscrits à la session
//     $qb2 = $this->createQueryBuilder('s2');
//     $notRegisteredStudents = $qb2
//         ->where($qb2->expr()->notIn('s2.id', $registeredStudents))
//         ->getQuery()
//         ->getResult();

//     return $notRegisteredStudents;
// }

// public function findStudentsNotRegistered($sessionId) {
//     $qb = $this->createQueryBuilder('s');

//     // Requête pour récupérer les étudiants inscrits à la session
//     $registeredStudents = $qb
//         ->select('student.id')
//         ->from(Student::class, 'student')
//         ->leftJoin('s.students', 'registeredStudents')
//         ->where('s.id = :sessionId')
//         ->setParameter('sessionId', $sessionId)
//         ->getQuery()
//         ->getScalarResult();

//     // Extraire les identifiants des étudiants inscrits
//     $registeredStudentIds = array_column($registeredStudents, 'id');

//     // Requête pour récupérer les étudiants qui ne sont pas inscrits à la session
//     $qb2 = $this->getEntityManager()->createQueryBuilder();
//     $notRegisteredStudents = $qb2
//         ->select('student2')
//         ->from(Student::class, 'student2')
//         ->where($qb2->expr()->notIn('student2.id', $registeredStudentIds))
//         ->getQuery()
//         ->getResult();

//     return $notRegisteredStudents;
// }


    // Fonction pour chercher les stagiaires inscrit et non inscrit à une session

    public function findStudentsNotRegistered($id)
    {
        // on fait appel à l'EntityManager pour intéragir avec la BDD
        $entityManager = $this->getEntityManager();

        // Sous-requête pour récupérer les étudiants inscrits

        $sub = $entityManager->createQueryBuilder(); // on crée une instance de l'obet 'QueryBuilder'
        $sub->select('i.id') // on selectionne uniquement les id des stagiaires inscrit
            ->from('App\Entity\Student', 'i') // table Student
            ->leftJoin('i.sessions', 's') // rejoind la relation Session pour trouver les inscriptions
            ->where('s.id = :id'); // où id.session = session.id


        // Requête principale pour récupérer les étudiants non inscrits

        $qb = $entityManager->createQueryBuilder();
        $qb->select('e') // on selectionne l'objet Student complet
            ->from('App\Entity\Student', 'e') // de la table Student
            ->where($qb->expr()->notIn('e.id', $sub->getDQL())) // on utilise la sous requête pour exclure les étudiants inscrits
            ->setParameter('id', $id) // on définit le paramètre :id avec la valeur fournie
            ->orderBy('e.lastname'); // on trie les résultats par nom de famille

        // on execute la requête principale
        $query = $qb->getQuery();
        return $query->getResult(); // on retourne la liste des étudiants non inscrit à la session
    }







//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
