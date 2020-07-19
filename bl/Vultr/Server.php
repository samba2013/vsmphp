<?php


namespace vsm\bl\Vultr;


use vsm\api\lib\Helpers\VsmAssistance;

class Server extends Vultr implements IVultr
{
    private $key;
    private $regionId;


    private static $Instance;

    public static function getInstance($apiKey,$regionId){
        if(self::$Instance == null ){
            self::$Instance = new self($apiKey,$regionId);

        }else{
            self::$Instance->setKey($apiKey);
            self::$Instance->setRegionId($regionId);
        }
        return self::$Instance;
    }

    public function getLastError(){
        return $this->lastError;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * @param mixed $regionId
     */
    public function setRegionId($regionId)
    {
        $this->regionId = $regionId;
    }

    public function __construct($key,$regionId)
    {
        $this->key = $key;
        $this->regionId = $regionId;
    }

    public function getList()
    {
        $result = $this->GetResult($this->GetLink("server","list"),[],$this->key);
        $rows = [];
        if($result==null){
            return [];
        }
        elseif($this->regionId == -1){
            return $result;
        }elseif($this->regionId>0){
            foreach ($result as $row){
                if($this->regionId != $row->DCID){
                    continue;
                }
                $rows[]=$row;
            }
            return  $rows;
        }
    }

    public function getIps($serverId){
        $result = $this->GetResult($this->GetLink("server","list_ipv4?SUBID=".$serverId),[],$this->key);

        return $result==null?[]:$result->$serverId;
    }

    public function StopServer($serverId){
        $result = $this->GetResult($this->GetLink("server","halt"),["SUBID"=>$serverId],$this->key);

        return $result==null?[]:$result->$serverId;
    }

    public function StartServer($serverId){
        $result = $this->GetResult($this->GetLink("server","start"),["SUBID"=>$serverId],$this->key);

        return $result==null?[]:$result->$serverId;
    }

    public function RebootServer($serverId){
        $result = $this->GetResult($this->GetLink("server","reboot"),["SUBID"=>$serverId],$this->key);

        return $result==null?[]:$result->$serverId;
    }

    public function Destroy($serverId){
        $result = $this->GetResult($this->GetLink("server","destroy"),["SUBID"=>$serverId],$this->key);

        return $result==null?[]:$result->$serverId;
    }

    /**
     * @param $osid
     * @param $machineid
     * @param $regionid
     * @param $hosname
     * @param $label
     * @param $userdata
     * @return integer|null
     */
    public function Deploy($osid,$machineid,$regionid,$hosname,$label,$scriptid,$ipv6enabled,$tag){
        $result = $this->GetResult($this->GetLink("server","create"),[
            "DCID"=>$regionid,
            "VPSPLANID"=>$machineid,
            "OSID"=>$osid,
            "label"=>$label,
            "hostname"=>$hosname,
            "SCRIPTID"=>$scriptid,
            "enable_ipv6"=>$ipv6enabled,
            "tag"=>$tag
        ],$this->key);

        return $result==null?NULL:$result->SUBID;
    }
}