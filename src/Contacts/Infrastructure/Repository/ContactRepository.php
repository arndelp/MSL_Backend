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
}