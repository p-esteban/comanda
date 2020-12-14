<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Usuario;
use stdClass;
use App\Components\auth;
use App\Models\Articulo;
use App\Models\EstadoMesa;
use App\Models\EstadoPedido;
use App\Models\Pedido;
use App\Models\Empleado;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Sector;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use DateTime;
use DateInterval;



class MesaController
{

    public function setState(Request $request, Response $response, array $args)
    {

        $rta = new stdClass;
$mesa = Mesa::find($args['id']);

        if ($mesa->id_estado = 3) {


            

            $eMesa = EstadoMesa::where('nombre', 'cerrada')->first();

            $mesa->id_estado = $eMesa->id;
            $rta->msg = $mesa->save();

        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
}
