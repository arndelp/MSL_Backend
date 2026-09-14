<?php

namespace App\Contacts\Infrastructure\Repository;

use App\Contacts\Domain\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Contacts\Domain\Repository\ContactRepositoryInterface;
/** 
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository implements ContactRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }   

    public function save(Contact $contact): void
    {
        $em = $this->getEntityManager();
        $em->persist($contact);
        $em->flush();
        
    }

     public function deleteContact(Contact $contact): void
    {
        $em = $this->getEntityManager(); //hérité de ServiceEntiyRepository 
        $em->remove($contact);
        $em->flush();
    }

     public function findById(int $id): ?Contact
    {
        return $this->find($id); // find : méthode héritée du serviceEntityRepository
    }

    public function countAll(): int
    {
        return $this->count([]);
    }

     public function findPaginated(int $page,int $nbre): array
    {
        $query = $this -> createQueryBuilder('m');

        $countQb = clone $query;               // on clone $query pour séparer le comptage des marker et la récupération des résultats
        $total = (int) $countQb ->select('COUNT(m.id)')
                                ->getQuery()
                                ->getSingleScalarResult(); 
        
        // ----------- Pagination -----------
        $query  ->orderBy('m.id', 'DESC')
                ->setFirstResult(($page - 1) * $nbre)
                ->setMaxResults($nbre);

        $contacts = $query->getQuery()->getResult() ?? []; //si pas de résultat on retourne un tableau vide

        return [
            'contacts'   => $contacts,
            'total'     => $total,
            'nbrePage'  => (int) ceil($total / $nbre),
            'currentPage' => $page
        ];
    }




}