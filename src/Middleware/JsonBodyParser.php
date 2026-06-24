<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonBodyParser implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');
        
        if (stripos($contentType, 'application/json') === 0) {
            $rawBody = (string)$request->getBody();
            $parsedBody = $rawBody === '' ? [] : json_decode($rawBody, true);
            
            if (!is_array($parsedBody)) {
                $parsedBody = [];
            }
            
            $request = $request->withParsedBody($parsedBody);
        }
        
        return $handler->handle($request);
    }
}