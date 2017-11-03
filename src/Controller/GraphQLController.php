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
use Symfony\Component\HttpKernel\KernelInterface;
use Zend\Diactoros\Response;

class GraphQLController extends Controller
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $debug = $this->kernel->isDebug() ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE  : 0;

        // http://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
        $server = new StandardServer([
            'schema' => $this->getSchema(),
            'debug'  => $debug,
        ]);


        $response = new Response();

        return $server->processPsrRequest($request, $response, $response->getBody());
    }

    private function getSchema(): Schema
    {
        $schemaCache = $this->kernel->getCacheDir(). '/schema.cache';
        $schemaFile = __DIR__ . '/../../config/schema.graphqls';

        if (file_exists($schemaCache) && !$this->kernel->isDebug()) {
            $document = AST::fromArray(unserialize(file_get_contents($schemaCache), []));
        } else {
            $document = Parser::parse(file_get_contents($schemaFile));
            file_put_contents($schemaCache, serialize(AST::toArray($document)) );
        }

        return BuildSchema::build($document, new TypeConfigDecorator());
    }
}