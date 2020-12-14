<?php

namespace App\Components;


use \Firebase\JWT\JWT;

use function PHPSTORM_META\type;
use stdClass;
use FFI\Exception;

class Auth
{
    private static $secret_key = 'comanda';


    public static function signIn($name, $type)
    {

        $payload = array(
            'name'=>$name,
            'type' => $type
            
        );
        

        return JWT::encode($payload, self::$secret_key);
    }
    public static function check($token)
    {
       
        
           // $response = new stdClass();
            // $response->algo = "asd";
            // var_dump($response);
            // //$response = (array)$response;
            // if (count((array)$response) == 0 ) {
            //    echo "llegue";
            // }
            try {
               
                return empty($token) ? [] : JWT::decode($token, self::$secret_key,array('HS256'));
              //return [];
                
            } catch (\Throwable $th) {
                
                return  [];

            }
            // if (!empty($token)) {

            //    $response->data = JWT::decode($token, self::$secret_key,array('HS256'));
              
            // $response->status=false;
                
                
            // }else{
            //     $response->status = false;
            // }
            
            // return $response;
            
        
    }

    // public static function GetData($token)
    // {
    //     return JWT::decode($token, self::$secret_key)->data;
    // }
}
