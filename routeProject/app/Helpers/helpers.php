<?php

if(!function_exists("dd")){
    function dd(mixed $data):void{
        print_r($data);
        die();
    }
}