<?php declare(strict_types=1);

namespace App\Controller;

use App\GraphQL\TypeConfigDecorator;
use App\Middleware\JsonBodyDecoder;
use GraphQL\Error\Debug;
use Overblog\DataLoader\Promise\Adapter\Webonyx\GraphQL\SyncPromiseAdapter;
use GraphQL\Language\Parser;
use GraphQL\Type\Schema;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use GraphQL\Validator\Rules\QueryDepth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GraphQL\Server\StandardServer;
use Symfony\Component\HttpKernel\KernelInterface;
use Zend\Diactoros\Response;

class GraphQLController extends Controller
{
    /** @var KernelInterface */
    private $kernel;

    /** @var TypeConfigDecorator */
    private $typeConfigDecorator;

    public function __construct(KernelInterface $kernel, TypeConfigDecorator $typeConfigDecorator)
    {
        $this->kernel = $kernel;
        $this->typeConfigDecorator = $typeConfigDecorator;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = new JsonBodyDecoder();
        $request = $middleware($request);

        $config = $this->getConfig();
        $context = $this->getContext();

        $server = new StandardServer([
            'schema' => $this->getSchema($config['schemaFile']),
            'debug'  => $config['debug'],
            'context' => $context,
            'validationRules' => [
                new QueryDepth($config['maxQueryDepth'])
            ],
            'queryBatching' => $config['queryBatching'],
            'promiseAdapter' => new SyncPromiseAdapter(),
        ]);

        $response = new Response();

        return $server->processPsrRequest($request, $response, $response->getBody());
    }

    private function getConfig(): array
    {
        return [
            'debug' => $this->kernel->isDebug() ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE  : 0,
            'schemaFile' => __DIR__ . '/../../config/schema.graphqls',
            'maxQueryDepth' => 11,
            'queryBatching' => true,
        ];
    }

    private function getContext(): array
    {
        return [
            'user' => null,
        ];
    }

    private function getSchema(string $schemaFile): Schema
    {
        $schemaCache = $this->kernel->getCacheDir(). '/schema.cache';

        if (file_exists($schemaCache) && !$this->kernel->isDebug()) {
            $document = AST::fromArray(unserialize(file_get_contents($schemaCache), []));
        } else {
            $document = Parser::parse(file_get_contents($schemaFile));
            file_put_contents($schemaCache, serialize(AST::toArray($document)) );
        }

        return BuildSchema::build($document, $this->typeConfigDecorator);
    }
}