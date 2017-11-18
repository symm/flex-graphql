<?php declare(strict_types=1);

namespace App\Controller;

use App\GraphQL\TypeConfigDecorator;
use GraphQL\Error\Debug;
use GraphQL\Language\Parser;
use GraphQL\Type\Schema;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryDepth;
use Psr\Http\Message\RequestInterface;
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
        $request = $this->jsonBodyMiddleware($request);

        $config = [
            'debug' => $this->kernel->isDebug() ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE  : 0,
            'schemaFile' => __DIR__ . '/../../config/schema.graphqls',
            'maxQueryDepth' => 11,
        ];

        // http://webonyx.github.io/graphql-php/executing-queries/#server-configuration-options
        $server = new StandardServer([
            'schema' => $this->getSchema($config['schemaFile']),
            'debug'  => $config['debug'],
        ]);

        DocumentValidator::addRule(new QueryDepth($config['maxQueryDepth']));

        $response = new Response();

        return $server->processPsrRequest($request, $response, $response->getBody());
    }

    // TODO: Implement as proper middleware ala
    // https://github.com/zendframework/zend-expressive-helpers/blob/35126f5c7b71d56d5f1c18316d0bb67eef07aad9/src/BodyParams/BodyParamsMiddleware.php
    private function jsonBodyMiddleware(ServerRequestInterface $request): ServerRequestInterface
    {
        $nonBodyRequests = [
            'GET',
            'HEAD',
            'OPTIONS',
        ];

        if (in_array($request->getMethod(), $nonBodyRequests, false)) {
            return $request;
        }
        $contentType = $request->getHeaderLine('Content-Type');
        $parts = explode(';', $contentType);
        $mime = array_shift($parts);
        $isJson = (bool) preg_match('#[/+]json$#', trim($mime));

        if ($isJson) {
            $rawBody = (string) $request->getBody();
            $parsedBody = json_decode($rawBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Error when parsing JSON request body: ' . json_last_error_msg());
            }
            $request = $request
                ->withAttribute('rawBody', $rawBody)
                ->withParsedBody($parsedBody);
        }

        return $request;
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