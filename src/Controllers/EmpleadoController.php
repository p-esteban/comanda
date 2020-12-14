<?php

namespace App\Controllers;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Usuario;
use stdClass;
use App\Components\auth;
use App\Models\Empleado;
use App\Models\EstadoEmpleado;
use App\Models\Puesto;
use App\Models\Sector;

class EmpleadoController
{



    public function addOne(Request $request, Response $response, $args)
    {

        $newEmployee = new Empleado;
        $rta = new stdClass();


        $body  = $request->getParsedBody() ?? [];

        // var_dump($datosARegistrar);
        if (!empty($body)) {
            $nombre = $body['nombre'];
            $apellido = $body['apellido'];

            $clave = convert_uuencode($body['clave']);
            $employee = Empleado::whereRaw(
                'nombre = ? AND apellido = ? AND clave = ?',
                array($nombre, $apellido, $clave)
            )->first();
            if (!isset($employee)) {
                //no existe
                $newEmployee->nombre = $nombre;
                $newEmployee->apellido = $apellido;
                $newEmployee->clave = $clave;

                $puesto =  Puesto::where('nombre', $body["puesto"])->first();

                if (isset($puesto)) {
                    $newEmployee->id_puesto = $puesto->id;

                    if ($puesto->puesto != 'mozo') {
                        $sector =  Sector::where('sector', $body["sector"])->first();
                        if (isset($sector)) {
                            $newEmployee->id_sector = $sector->id;
                        } else {
                            $rta->message = "error: sector invalido ";
                        }
                    }
                    $estado = EstadoEmpleado::where('estado', 'activo')->first();
                    $newEmployee->id_estado = $estado->id;
                    $rta->message = $newEmployee->save();
                } else {
                    $rta->message = "error: puesto invalido ";
                }
            } else {
                $rta->message = "error: usuario registrado ";
            }
        } else {
            $rta->message = "no se recibe datos ";
        }

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function login(Request $request, Response $response, $args)
    {
        $rta = new stdClass();
        try {
            $body = $request->getParsedBody() ?? [];

            $apellido = $body['apellido'];
            $clave = convert_uuencode($body['clave']);
            // echo $nombre;


            if (isset($apellido)) {
                $userRegitred = Empleado::whereRaw('apellido = ? AND clave = ? AND id_estado = ?', array($apellido, $clave, 1))->firstOrFail();
                $rta->message = "todo bien";




                $rta->token = auth::signIn($apellido, $userRegitred->puesto->nombre);
            }
        } catch (\Throwable $th) {
            // echo "entre";
            $rta->message = "usuario no registrado";
        } finally {
            $response->getBody()->write(json_encode($rta));
            return $response;
        }
    }
}
