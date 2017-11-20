<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Repository\ArticleRepository;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ResolveInfo;

class AuthorResolver implements Resolver
{
    /** @var ArticleRepository */
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function __invoke($author, $args, $context, ResolveInfo $info)
    {
        if ('articles' === $info->fieldName) {
            return $this->articleRepository->findArticlesByAuthorId($author['id']);
        }

        return Executor::defaultFieldResolver($author, $args, $context, $info);
    }
}