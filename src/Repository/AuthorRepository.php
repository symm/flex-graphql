<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class AuthorRepository extends ServiceEntityRepository
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Author::class);
        $this->logger = $logger;
    }

    public function findAuthorById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findAuthorsById($ids)
    {
        $authors = $this->findById($ids);

        return $this->mapAuthors($authors);
    }

    public function allAuthors(): array
    {
        $authors = $this->findAll();

        return $this->mapAuthors($authors);
    }

    private function mapAuthors(array $authors)
    {
        return array_map(function (Author $author) {
            return [
                'id' => $author->getId()->toString(),
                'name' => $author->getName(),
            ];
        }, $authors);
    }
}
