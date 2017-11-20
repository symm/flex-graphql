<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\GraphQL\DataProvider;
use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\DataLoader\DataLoader;
use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;

class ArticleResolver implements Resolver
{
    private $authorLoader;
    private $dataProvider;

    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->authorLoader = $this->getAuthorDataLoader();
    }

    public function __invoke($article, $args, $context, ResolveInfo $info)
    {
        if ('author' === $info->fieldName) {
            return $this->authorLoader->load($article['authorId']);
        }

        return Executor::defaultFieldResolver($article, $args, $context, $info);
    }

    private function getAuthorDataLoader()
    {
        $dataProvider = $this->dataProvider;

        $graphQLPromiseAdapter = Executor::getPromiseAdapter();
        $dataLoaderPromiseAdapter = new WebonyxGraphQLSyncPromiseAdapter($graphQLPromiseAdapter);

        return new DataLoader(function($authorIds) use ($dataLoaderPromiseAdapter, $dataProvider) {
            $authors = $dataProvider->findAuthorsById($authorIds);

            return $dataLoaderPromiseAdapter->createAll($authors);

        }, $dataLoaderPromiseAdapter);
    }
}