<?php declare(strict_types=1);


namespace App\GraphQL\Resolver;

use App\Repository\ArticleRepository;
use App\Repository\AuthorRepository;
use GraphQL\Type\Definition\ResolveInfo;

class QueryResolver implements Resolver
{
    /** @var ArticleRepository */
    private $articleRepository;

    /** @var AuthorRepository */
    private $authorRepository;

    public function __construct(ArticleRepository $articleRepository, AuthorRepository $authorRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->authorRepository = $authorRepository;
    }

    public function __invoke($query, $args, $context, ResolveInfo $info)
    {
        if ('authors' === $info->fieldName) {
            return $this->authorRepository->allAuthors();
        }

        if ('articles' === $info->fieldName) {
            return $this->articleRepository->allArticles();
        }

        return null;
    }
}