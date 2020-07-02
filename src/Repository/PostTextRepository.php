<?php

namespace App\Repository;

use App\Entity\PostText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostText|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostText|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostText[]    findAll()
 * @method PostText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostTextRepository extends ServiceEntityRepository implements PostTextRepositoryInterface
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostText::class);
        $this->em = $this->getEntityManager();
    }

    public function getPostText(string $text)
    {
        return $this->findOneBy(['text' => $text]);
    }

    public function save(PostText $postText): void
    {
        try {
            $this->em->persist($postText);
            $this->em->flush();
        } catch (ORMException $e) {
        }
    }
}
