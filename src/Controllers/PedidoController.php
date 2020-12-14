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



class PedidoController
{

    //id
    //codigo 5char
    //estado

    // CREATE TABLE `pedidos` (
    //     `id` int(11) NOT NULL,
    //     `estado_id` int(11) NOT NULL,
    //     `mesa_id` int(11) NOT NULL,
    //     `cliente_id` int(11) NOT NULL,
    //     `codigo` varchar(5) NOT NULL,
    //     `delivery_time` datetime DEFAULT NULL,
    //     `photo` varchar(100) DEFAULT NULL,
    //     `created_at` datetime NOT NULL,
    //     `updated_at` datetime DEFAULT NULL,
    //     `deleted_at` datetime DEFAULT NULL
    //   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




    // CREATE TABLE `items` (
    //     `id` int(11) NOT NULL,
    //     `pedido_id` int(11) NOT NULL,
    //     `producto_id` int(11) NOT NULL,
    //     `sector_id` int(11) NOT NULL,
    //     `empleado_id` int(11) DEFAULT NULL,
    //     `estado_id` int(11) NOT NULL,
    //     `cantidad` int(11) NOT NULL,
    //     `monto` decimal(10,0) NOT NULL,
    //     `delivery_time` datetime DEFAULT NULL,
    //     `created_at` datetime NOT NULL,
    //     `updated_at` datetime DEFAULT NULL,
    //     `deleted_at` datetime DEFAULT NULL
    //   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



    public function add(Request $request, Response $response, array $args)
    {
        $token = $request->getHeader('token')[0];
        $rta = new stdClass();
        $body = json_decode(file_get_contents("php://input"));

        $pedido = new Pedido;
        $photo = null;

        $mesa = $body->id_mesa ?? '';



        $uploadedFiles = $request->getUploadedFiles();
        if (isset($uploadedFiles['foto'])) {
            $uploadedFile = $uploadedFiles['foto'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $photo = serialize($uploadedFile);
            }
        }

        //obtengo id de empleado a aprtir de token

        $token = auth::check($token);


        $empleado = Empleado::whereRaw('nombre = ? OR apellido = ? AND id_sector = ? AND id_estado = ?', array($token->name, $token->name, 1, 1))->firstOrFail();



        //mesa
        $mesa = Mesa::find($mesa);
        if (isset($mesa)) {


            $pedido->id_mesa = $mesa->id;
            $pedido->id_empleado = $empleado->id;

            if (isset($photo)) $pedido->photo = $photo;
            //codigo
            $codigo = substr(md5(time()), 0, 5);
            $pedido->codigo = $codigo;
            //Estado
            $estado = EstadoPedido::where('nombre', 'PENDIENTE')->first();
            $pedido->id_estado = $estado->id;

            $pedido->save();

            //Mesa Estado
            //le cambio es estado a la mesa
            $estadoMesa = EstadoMesa::where('nombre', 'con cliente esperando pedido')->first();
            $estadoMesa = $estadoMesa->id;
            $mesa->id_estado = $estadoMesa;
            $mesa->save();


            //obtengo id de pedido




            ////agrego los articulos

            // $rta->art= $body->articulos[0]->items[1];



            //articulo




            $pedidos = [];
            $pedidos =  $body->pedidos;
            //  var_dump($pedidos);
            foreach ($pedidos as  $orden) {

                foreach ($orden as $key => $value) {


                    if ($key == 'comensal') {
                        $idComensal = $value;
                    }
                    if ($key == 'items') {
                        //registro por cada producto
                        foreach ($value as $art) {
                            $articulo = new Articulo;
                            // echo $idComensal;
                            echo $art;

                            $articulo->id_comensal = $idComensal;
                            ///traer todo de tabla producto
                            $producto =  Producto::where('nombre', $art)->first(); //tresolver si el producto no existe
                            echo $producto->id;
                            $articulo->id_producto = $producto->id;
                            $articulo->id_sector = $producto->id_sector;
                            $articulo->monto = $producto->precio;
                            $pedido = Pedido::orderBy('id', 'desc')->first(); //aca validar que sea el mismo el¿mpleado
                            //  var_dump($pedido);
                            $articulo->id_pedido = $pedido->id;



                            $articulo->id_estado = $estado->id;
                            $articulo->save();
                            clearstatcache();
                            // echo  $articulo->id_estado;


                        }
                    }
                }
            }



            // $pedido = Pedido::orderBy('id', 'desc')->first();
            $rta->message = $codigo;
            // $response->getBody()->write(json_encode($rta));

        } else {
            $rta->message = 'Mesa does not exist';
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }


    public function getBySector(Request $request, Response $response, array $args)
    {
        $rta = [];
        $sector = Sector::where('sector', $args['sector'])->first();


        $articulos = Articulo::where('id_sector', $sector->id)->where('id_estado', 1)->get();


        //hacer un for para que por cada art busque el nombre
        foreach ($articulos as  $art) {
            $item = new stdClass;
            $item->producto = $art->producto->nombre;
            $item->mesa = $art->pedido->mesa->nombre;
            $item->estado = $art->estado->nombre;
            array_push($rta, $item);
        }


        // var_dump($articulos);
        $response->getBody()->write(json_encode($rta));



        return $response;
    }

    public function setArticulo(Request $request, Response $response, array $args)
    {
        //cambiar estado y setear tiempo


        $articulo = Articulo::find($args['id']);
        //traer articulo por id
        $rta = new stdClass;
        //  $body = $request->getParsedBody() ?? [];
        if (!is_null($articulo)) {

            // $body['estado']
            // $body['tiempo']



            $body = json_decode(file_get_contents("php://input"));

            /////////////////////////////


            if ($body->estado == 'EN PREPARACION') {


                $preparacion = EstadoPedido::where('nombre', 'EN PREPARACION')->first();

                $tiempo = $body->temp;
                //  echo $tiempo;
                $articulo->id_estado = $preparacion->id;

                $time = new DateTime();
                $time->add(new DateInterval('PT' . $tiempo . 'M'));

                // var_dump($time);

                $articulo->id_estado = $preparacion->id;
                $articulo->tiempo_preparacion = $time;

                $rta->message = $articulo->save();
                ////// VER DE QUE MANERA CAMBIAR ESTADO MESA


                $pendiente =  EstadoPedido::where('nombre', 'PENDIENTE')->first();
                $count = Articulo::where('id_pedido', $articulo->id_pedido)->where('id_estado', $pendiente->id)->count();

                echo $count;
                if ($count == 0) {
                    //cierro el pedido, le cambio el estado
                    $pedido = Pedido::find($articulo->id_pedido);
                    $pedido->id_estado = $preparacion->id;
                    $max = Articulo::where('id_pedido', $articulo->id_pedido)->max('tiempo_preparacion');
                    $pedido->tiempo = $max;
                    $pedido->save();
                } else {
                    $rta->message = 'Item does not exist';
                }
            } else if ($body->estado == 'LISTO') {


                $listo = EstadoPedido::where('nombre', 'LISTO PARA SERVIR')->first();

                //  $tiempo = $body->temp;
                //  echo $tiempo;
                $articulo->id_estado = $listo->id;

                // $time = new DateTime();
                // $time->add(new DateInterval('PT' . $tiempo . 'M'));

                // var_dump($time);

                $articulo->id_estado = $listo->id;
                // $articulo->tiempo_preparacion = $time;

                $rta->message = $articulo->save();
                ////// VER DE QUE MANERA CAMBIAR ESTADO MESA


                $pendiente =  EstadoPedido::where('nombre', 'EN PREPARACION')->first();
                $count = Articulo::where('id_pedido', $articulo->id_pedido)->where('id_estado', $pendiente->id)->count();

                echo $count;
                if ($count == 0) {
                    //cierro el pedido, le cambio el estado
                    $pedido = Pedido::find($articulo->id_pedido);
                    $pedido->id_estado = $listo->id;
                    //   $max = Articulo::where('id_pedido', $articulo->id_pedido)->max('tiempo_preparacion');
                    // $pedido->tiempo = $max;

                    $mesa = Mesa::find($pedido->id_mesa);
                    $eMesa = EstadoMesa::where('nombre', 'con clientes comiendo')->first();

                    $mesa->id_estado = $eMesa->id;
                    $mesa->save();


                    $pedido->save();




                } else {
                    $rta->message = 'Item does not exist';
                }
            }












            ////////////////////












            $response->getBody()->write(json_encode($rta));
            return $response;
        }
    }



    public function cobrar(Request $request, Response $response, array $args)
    {

        /// traer pedido con codigo de mesa
        // con id pedido traer todos o art
     
        $rta = new stdClass;
        $codigo = $args['codigo'];
        $pedido = Pedido::where('codigo', $codigo)->first();
        $mesa = Mesa::find($pedido->id_mesa);

        $eMesa = EstadoMesa::where('nombre', 'con clientes pagando')->first();

        $mesa->id_estado = $eMesa->id;
        $mesa->save();
        
        $articulos = Articulo::where('id_pedido',$pedido->id)->get();

        $total = 0;
        foreach ($articulos as  $value) {
           // echo $value->monto;
           $total +=  $value->monto;

        }


        $rta->total = $total;

        $response->getBody()->write(json_encode($rta));
        return $response;



    }
}
