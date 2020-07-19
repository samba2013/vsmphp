<?php


namespace vsm\bl\Vultr;


use vsm\api\lib\Helpers\VsmAssistance;

class Script extends Vultr implements IVultr
{
    private $key;


    private static $Instance;

    public static function getInstance($apiKey){
        if(self::$Instance == null){
            self::$Instance = new self($apiKey);

        }
        return self::$Instance;
    }

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getList()
    {
        $result = $this->GetResult($this->GetLink("startupscript","list"),[],$this->key);
        return $result==null?[]:$result;
    }

    public function CreateScript($name,$scriptData){
        $result = $this->GetResult($this->GetLink("startupscript","create"),['script'=>$scriptData,'name'=>$name],$this->key);

        return $result==null?[]:$result->SCRIPTID;
    }

    public function Update($scriptId,$name,$script){
        $result = $this->GetResult($this->GetLink("startupscript","update"),["SCRIPTID"=>$scriptId,"name"=>$name,"script"=>$script],$this->key);

        return $result==null?[]:$result;
    }

    /**
     * @param $scriptname
     * @return mixed|null
     */
    public function GetScriptByName($scriptname){
        $result = $this->getList();
        if(count($result)){
            $convArray=(array)$result;
            foreach ($convArray as $scriptId=>$value){
                if(strtolower($value->name)==strtolower($scriptname)){
                    return  $value;
                }
            }
        }
        return null;
    }

    public function Destroy($scriptId){
        $result = $this->GetResult($this->GetLink("startupscript","destroy"),["SCRIPTID"=>$scriptId],$this->key);

        return $result==null?[]:$result;
    }

}