<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\EmpleadoController;
use App\Controllers\PedidoController;

use App\Controllers\MesaController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Exception\NotFoundException;
use Config\Database;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;







$conn = new Database;
$app = AppFactory::create();
$app->setBasePath('/Programacion3/comanda/public'); /// aca hay que corregir


//$app->post('/singIn',)


$app->group('/users', function (RouteCollectorProxy $group) {

    $group->post('[/]', EmpleadoController::class . ":addOne");

    //     $group->get('[/]', UsuarioController::class . ":getAll")
    //     ->add(new AuthMiddleware(['admin']));
    //     $group->get('/{id}', UsuarioController::class . ":getOne")
    //     ->add(new AuthMiddleware(['user','admin']));

    $group->post('/login', EmpleadoController::class . ":login");

    //     $group->post('[/]', UsuarioController::class . ":addOne");

    //     $group->post('/login', UsuarioController::class . ":login");

    //    // $group->put('/{id}', UsuarioController::class . ":update");

    //     $group->delete('/{id}', UsuarioController::class . ":deleteOne");

})->add(new JsonMiddleware());

$app->group('/pedido', function (RouteCollectorProxy $group) {

    $group->post('[/]', PedidoController::class . ":add")->add(new AuthMiddleware(['mozo']));

    $group->get('/{sector}', PedidoController::class . ":getBySector"); //ver como hacer para validar por sector

    $group->post('/{id}', PedidoController::class . ":setArticulo");

    $group->get('/cobrar/{codigo}', PedidoController::class . ":cobrar");



})->add(new JsonMiddleware());

$app->group('/mesa', function (RouteCollectorProxy $group) {

    $group->post('/{id}', MesaController::class . ":setState");//->add(new AuthMiddleware(['mozo']));

   



})->add(new JsonMiddleware());


//$app->addBodyParsingMiddleware();
$app->run();
