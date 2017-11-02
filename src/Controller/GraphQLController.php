<?php declare(strict_types=1);

namespace App\Controller;

use App\GraphQL\TypeConfigDecorator;
use GraphQL\Error\Debug;
use GraphQL\Language\Parser;
use GraphQL\Type\Schema;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GraphQL\Server\StandardServer;
use Zend\Diactoros\Response;

class GraphQLController extends Controller
{
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // http://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
        $server = new StandardServer([
            'schema' => $this->getSchema(),
            'debug'  => Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE | Debug::RETHROW_INTERNAL_EXCEPTIONS,
        ]);


        $result = $server->executePsrRequest($request);

        $response = new Response();
        $response->getBody()->write(json_encode($result));

        return $response;
    }

    private function getSchema(): Schema
    {
        $cacheDir = $this->container->getParameter('kernel.cache_dir');
        $environment = $this->container->getParameter('kernel.environment');

        $cachedEnvironments = ['prod'];
        $schemaCache = $cacheDir . '/schema.cache';
        $schemaFile = __DIR__ . '/../../config/schema.graphqls';

        if (file_exists($schemaCache) && in_array($environment, $cachedEnvironments, false)) {
            $document = AST::fromArray(unserialize(file_get_contents($schemaCache), []));
        } else {
            $document = Parser::parse(file_get_contents($schemaFile));
            file_put_contents($schemaCache, serialize(AST::toArray($document)) );
        }

        return BuildSchema::build($document, new TypeConfigDecorator());
    }
}