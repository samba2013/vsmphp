<?php
namespace vsm\api\lib\Helpers;

class VsmAssistance
{
    public static function WrapObject($obj,$printValue=false,$exit=false){
        echo "<pre>";
        if($printValue)
            print_r($obj);
        else
            var_dump($obj);
        echo "</pre>";
        if($exit)
            exit(0);
    }

}