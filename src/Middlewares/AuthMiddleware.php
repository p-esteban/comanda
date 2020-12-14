<?php

namespace App\Middlewares;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Components\auth;




class AuthMiddleware
{
    private $_tipo;
    public function __construct($tipo)
    {
        $this->_tipo = $tipo;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {

        $token = auth::check($request->getHeader('token')[0]);
        //var_dump($tokenAuth->type);
        // var_dump($tokenAuth);
        // var_dump($this->_tipo);
        $response = new Response();
        if (empty($token) || !in_array($token->type, $this->_tipo)) {

            $response->getBody()->write(json_encode('salio mal'));
            return $response->withStatus(403);
        }

        $rta = $handler->handle($request);
        $existingContent = (string) $rta->getBody();

        $response->getBody()->write($existingContent);

        return $response;
    }
}
