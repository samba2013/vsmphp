<?php


namespace vsm\bl\Vultr;


class OS extends Vultr implements IVultr
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
        $result = $this->GetResult($this->GetLink("os","list"));
        return $result;
    }
}