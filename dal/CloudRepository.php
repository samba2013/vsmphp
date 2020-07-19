<?php


namespace vsm\dal;


use vsm\bl\Cloud;
use vsm\bl\Vultr\OS;
use vsm\bl\Vultr\Plan;
use vsm\bl\Vultr\Region;
use vsm\bl\Vultr\Script;
use vsm\bl\Vultr\Server;

class CloudRepository extends VsmModel
{


    public static $table = "admin.cloud";
    public static $model = Cloud::class;
    private static $Instance;

    public static function getInstance(){
        if(self::$Instance == null){
            self::$Instance = new self();

        }
        return self::$Instance;
    }

    public function __construct()
    {
        parent::__construct();
    }


    public function getRegion(){
        return Region::getInstance();
    }

    public function getServer($apikey,$regionId){
        return Server::getInstance($apikey,$regionId);
    }

    public function getPlan(){
        return Plan::getInstance();
    }

    public function getOS(){
        return OS::getInstance();
    }

    public function getScript($apikey){
        return Script::getInstance($apikey);
    }


}