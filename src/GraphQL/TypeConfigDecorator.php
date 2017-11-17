<?php declare(strict_types=1);

namespace App\GraphQL;


use GraphQL\Type\Definition\ResolveInfo;
use Psr\Log\LoggerInterface;

class TypeConfigDecorator
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke($typeConfig, $typeDefinitionNode)
    {
        if ($typeConfig['name'] === 'Query') {
            $typeConfig['resolveField'] = function($query, $args, $context, ResolveInfo $info) {
                switch($info->fieldName) {
                    case 'authors':
                        return DataProvider::allAuthors();
                    case 'articles':
                        return DataProvider::allArticles();
                    default:
                        return null;
                }
            };
        }

        if ($typeConfig['name'] === 'Article') {
            $typeConfig['resolveField'] = function($article, $args, $context, ResolveInfo $info) {
                switch ($info->fieldName) {
                    case 'author':
                        return DataProvider::findAuthorById($article['authorId']);
                    default:
                        return $article[$info->fieldName];
                }
            };
        }

        if ($typeConfig['name'] === 'Author') {
            $typeConfig['resolveField'] = function($author, $args, $context, ResolveInfo $info) {
                switch($info->fieldName) {
                    case 'articles':
                        return DataProvider::findArticlesByAuthorId($author['id']);
                    default:
                        return $author[$info->fieldName];
                }
            };
        }


        return $typeConfig;
    }
}