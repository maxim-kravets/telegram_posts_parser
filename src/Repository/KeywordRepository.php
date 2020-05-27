<?php

namespace App\Repository;

use App\Entity\Keyword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Keyword|null find($id, $lockMode = null, $lockVersion = null)
 * @method Keyword|null findOneBy(array $criteria, array $orderBy = null)
 * @method Keyword[]    findAll()
 * @method Keyword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeywordRepository extends ServiceEntityRepository implements KeywordRepositoryInterface
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Keyword::class);
        $this->em = $this->getEntityManager();
    }

    public function getByName(string $name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function save(Keyword $keyword): void
    {
        try {
            $this->em->persist($keyword);
            $this->em->flush();
        } catch (ORMException $e) {
        }
    }
}
