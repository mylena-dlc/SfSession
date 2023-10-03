<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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


    // Fonction pour chercher les stagiaires non inscrits à une session
//     public function findNoRegistered($id) {

//         return $this->createQueryBuilder('s')
//             ->leftJoin('s.students', 'students')  // Jointure entre la session et les étudiants inscrits
//             ->andWhere(':id NOT MEMBER OF s.students')  // Filtre pour sélectionner les étudiants non inscrits
//             ->setParameter('id', $id)  // Définition du paramètre :sessionId avec la session donnée
//             ->getQuery()  // Création de la requête
//             ->getResult();  // Exécution de la requête et récupération des résultats
// }
public function findStudentsNotRegistered($sessionId) {
    $qb = $this->createQueryBuilder('s');

    // Requête pour récupérer les étudiants inscrits à la session
    $registeredStudents = $qb
        ->select('s.id')
        ->leftJoin('s.students', 'registeredStudents')
        ->where('s.id = :sessionId')
        ->setParameter('sessionId', $sessionId)
        ->getQuery()
        ->getResult();

    // Requête pour récupérer les étudiants qui ne sont pas inscrits à la session
    $qb2 = $this->createQueryBuilder('s2');
    $notRegisteredStudents = $qb2
        ->where($qb2->expr()->notIn('s2.id', $registeredStudents))
        ->getQuery()
        ->getResult();

    return $notRegisteredStudents;
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
