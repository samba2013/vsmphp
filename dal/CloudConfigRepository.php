<?php


namespace vsm\dal;


use vsm\bl\CloudConfig;

class CloudConfigRepository extends VsmModel
{
    public static $table = "admin.cloud_config";
    public static $model = CloudConfig::class;
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
}