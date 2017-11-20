<?php declare(strict_types=1);

namespace App\GraphQL\DataLoader;

use App\Repository\AuthorRepository;
use GraphQL\Executor\Promise\PromiseAdapter;
use Overblog\DataLoader\DataLoader;
use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;
use Overblog\PromiseAdapter\PromiseAdapterInterface;

class BatchAuthorLoader
{
    private $repo;
    private $promiseAdapter;

    private function __construct(AuthorRepository $repo, PromiseAdapterInterface $promiseAdapter)
    {
        $this->repo = $repo;
        $this->promiseAdapter = $promiseAdapter;
    }

    public function __invoke(array $authorIds)
    {
        $authors = $this->repo->findAuthorsById($authorIds);

        return $this->promiseAdapter->createAll($authors);
    }

    public static function factory(PromiseAdapter $promiseAdapter, AuthorRepository $repo): DataLoader
    {
        $dataLoaderPromiseAdapter = new WebonyxGraphQLSyncPromiseAdapter($promiseAdapter);

        return new DataLoader(
            new self($repo, $dataLoaderPromiseAdapter),
            $dataLoaderPromiseAdapter
        );
    }
}