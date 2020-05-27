<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository implements PostRepositoryInterface
{
    private $em;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
        $this->em = $this->getEntityManager();
    }

    public function getPostByTelegramId(int $id)
    {
        return $this->findOneBy(['telegram_id' => $id]);
    }

    public function save(Post $post): void
    {
        try {
            $this->em->persist($post);
            $this->em->flush();
        } catch (ORMException $e) {
        }
    }
}
