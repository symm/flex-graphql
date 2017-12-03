<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findArticlesByAuthorId($authorId)
    {
        $articles = $this->findBy(['author' => $authorId]);

        return $this->mapArticles($articles);
    }

    public function allArticles(): array
    {
        $articles = $this->findAll();

        return $this->mapArticles($articles);
    }

    private function mapArticles(array $articles)
    {
        return array_map(function (Article $article) {
            return [
                'id' => $article->getId()->toString(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'authorId' => $article->getAuthor()->getId()->toString(),
            ];
        }, $articles);
    }
}
