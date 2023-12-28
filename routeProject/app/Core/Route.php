<?php
namespace app\Core;
class Route
{
    private static array $routes = [];

    public static function getUri():string{
        $uri =  $_SERVER["REQUEST_URI"];
        $uriSegments = explode('/', $uri);
        $uri = "/". end($uriSegments);
        return  $uri;
    }
    public static function getMethod():string{
        return strtolower($_SERVER["REQUEST_METHOD"]);
    }
    public static function checkMethod():void{
            $uri = self::getUri();

            $method = self::getMethod();
            $routes = self::$routes;


            $check = array_key_exists($method,$routes);

            $supportedMethods = array_filter($routes,function($item) use ($uri){


                return array_key_exists($uri,$item);
            });
            if(!$check || !in_array($method,array_keys($supportedMethods))){

            $supportedMethods = strtoupper(implode(" | ",array_keys($supportedMethods)));



            echo json_encode([
                "message"=> "Method desteklenmiyor. Desteklenen methodlar : $supportedMethods"
            ]);
            exit();
        }

    }



    public static function get($url,\Closure|string $action): Route{

        self::$routes["get"][$url] = ["action" => $action];

        return new self();
    }
    public static function put($url,\Closure|string $action): Route{

        self::$routes["put"][$url] = ["action" => $action];

        return new self();
    }
    public static function post($url,\Closure|string $action): Route{
        self::$routes["post"][$url] = ["action" => $action];
        return new self();

    }
    public static function dispatch(): void{
        $uri = self::getUri();
        $method = self::getMethod();
        $routes = self::$routes;
//        dd($routes[$method]);
        self::checkMethod();
        foreach ($routes[$method] as $url => $item){

            $pattern = "@^".$url."$@";
//            dd($pattern);
            if (preg_match($pattern,$uri,$parameters)){
                dd($parameters);

            }else{
                echo "uyuşmuyor";
            }
        }






    }

}