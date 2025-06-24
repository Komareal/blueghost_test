<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 */
class ContactRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * Generate a unique slug for the given name and surname.
     *
     * @param string $name
     * @param string $surname
     * @param int|null $excludeId Optionally exclude a contact by id (for updates)
     * @return string
     */
    public function generateUniqueSlug(string $name, string $surname, ?int $excludeId = null): string
    {
        $baseSlug = Contact::slugify($name, $surname);
        $slugCandidate = $baseSlug;
        $i = 1;

        do {
            $qb = $this->createQueryBuilder('c')
                ->select('c.id')
                ->where('c.slug = :slug')
                ->setParameter('slug', $slugCandidate);

            if ($excludeId !== null) {
                $qb->andWhere('c.id != :excludeId')
                    ->setParameter('excludeId', $excludeId);
            }

            $exists = (bool) $qb->getQuery()->getOneOrNullResult();

            if (!$exists) {
                break;
            }
            $slugCandidate = $baseSlug . '-' . $i++;
        } while (true);

        return $slugCandidate;
    }

}
