<?php
namespace vsm\bl\Vultr;

use mysql_xdevapi\Exception;
use vsm\api\lib\Helpers\VsmAssistance;

abstract class Vultr {

    protected $lastError = null;
    protected $ApiLink = "https://api.vultr.com/v1/";

    /**
     * @param $link
     * @return mixed|null
     */
    protected function GetResult($link,$data=[],$key=null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL,$link);
        if($key!=null)
            curl_setopt($ch, CURLOPT_HTTPHEADER,[
                'API-Key: '.$key
            ]);
        if(!empty($data)){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

        }
        $result=curl_exec($ch);

        $this->lastError = $result;


        curl_close($ch);
        if($result !== NULL && is_string($result)){
            return json_decode($result);
        }



        return NULL;
    }

    protected function GetLink($Entity,$Action){
            return $this->ApiLink.$Entity.DS.$Action;
    }
}