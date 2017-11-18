<?php declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class JsonBodyDecoder
{
    public function __invoke(ServerRequestInterface $request): ServerRequestInterface
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
}