<?php

namespace vsm\bl\Vultr;

class Region extends Vultr implements IVultr
{


    private static $Instance;

    public static function getInstance(){
        if(self::$Instance == null){
            self::$Instance = new self();

        }
        return self::$Instance;
    }

    public function __construct()
    {

    }

    public function getList()
    {
        $result = $this->GetResult($this->GetLink("regions","list"));
        return $result;
    }

    public function getAvailable($dcid)
    {
        $result = $this->GetResult($this->GetLink("regions","availability?DCID=".intval($dcid)));
        return $result;
    }
}