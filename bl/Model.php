<?php
namespace vsm\bl;


abstract class Model implements IModel {

    protected $table;
    protected $generator;

    /**
     * @var integer
     */
    protected $is_active = 1;

    /**
     * @var date
     * @required
     */
    protected $created_at;
    /**
     * @var integer
     * @required
     */
    protected $created_by;
    /**
     * @var date
     */
    protected $modified_at = NULL;
    /**
     * @var integer
     */
    protected $modified_by = NULL;


    public function __construct($table)
    {
        $this->table = $table;
        $this->generator = new Generator(get_class($this));


    }

    function printObject($end=false){
        echo "<pre>";
        print_r($this);
        echo "</pre>";
        if($end) exit(0);
    }


    function vardumpObject($end=false){
        echo "<pre>";
        var_dump($this);
        echo "</pre>";
        if($end) exit(0);
    }

    public function getProperties(){

        $mixed= get_object_vars($this);

        unset($mixed['table']);
        unset($mixed['generator']);

        return $mixed;
    }

    public function getGenerator(){
        return $this->generator;
    }

    /**
     * @param string $timezone
     * @return date
     * @throws \Exception
     */
    public function getModifiedAt($timezone=DATE_TIMEZONE)
    {
        if($this->modified_at==null) return null;

        $modifiedAt = new \DateTime($this->modified_at);
        $modifiedAt->setTimezone(new \DateTimeZone($timezone));
        return $modifiedAt->format("Y-m-d H:i:s");
    }

    /**
     * @param string $timezone
     * @return date
     * @throws \Exception
     */
    public function getCreatedAt($timezone=DATE_TIMEZONE)
    {
        $createdAt = new \DateTime($this->created_at);
        $createdAt->setTimezone(new \DateTimeZone($timezone));
        return $createdAt->format("Y-m-d H:i:s");
    }

    public function __get($name)
    {
        $params = get_object_vars($this);
        if(array_key_exists($name,$params)){
            return $params[$name];
        }

    }

    public function __set($name, $value)
    {


        $params = get_object_vars($this);

        if(array_key_exists($name,$params)){
            $this->$name = $value;
            if($name!='modified_at') {
                $modifiedAt = new \DateTime("now");
                $modifiedAt->setTimezone(new \DateTimeZone("UTC"));
                $this->modified_at= $modifiedAt->format("Y-m-d H:i:s");
            }

            if($name!='modified_by') {
                $this->modified_by= $_SESSION['user']->id ;
            }

        }
    }

    public function Fill($params){
        $value = new \DateTime("now");
        $value->setTimezone(new \DateTimeZone("UTC"));
        $this->created_at=$value->format("Y-m-d H:i:s");
        foreach ($params as $key=>$value){
            $this->$key = $value;
        }


    }

    public function getAddQuery()
    {


        $params = $this->getProperties();

        unset($params['id']);

        $query = "INSERT INTO {$this->table} (".implode(',',array_keys($params)).")";

        $keyWords =[];
        foreach ($params as $key=>$param){
            $keyWords[]=":{$key}";
        }
        $query.=" VALUES (".implode(', ',$keyWords).");";
        return $query;
    }

    public function getRemoveQuery($removeRecord=false)
    {
        if(!$removeRecord)
            return "UPDATE {$this->table} set is_active=0,modified_by=:modified_by,modified_at=:modified_by WHERE id = :id;";
        else
            return "DELETE FROM {$this->table} WHERE id = :id;";
    }

    public function getUpdateQuery()
    {
        $params = $this->getProperties();
        unset($params['id']);

        $query = "UPDATE {$this->table} ";

        $keyWords =[];
        foreach ($params as $key=>$param){
            $keyWords[]="{$key} = :{$key}";
        }
        $query.=" set ".implode(',',$keyWords)." WHERE id = :id;";
        return $query;
    }

    public function getSelectQuery($params = [])
    {
        $where=array();
        foreach ($params as $field=>$value){
            $where[]="{$field} $value ?";
        }
        return "SELECT * FROM {$this->table} ".((count($where))?"WHERE ".implode(" AND ",$where):"").";";
    }

}