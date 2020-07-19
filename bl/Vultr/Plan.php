<?php


namespace vsm\bl\Vultr;


class Plan extends Vultr implements IVultr
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
        $result = $this->GetResult($this->GetLink("plans","list"));
        return $result;
    }
}