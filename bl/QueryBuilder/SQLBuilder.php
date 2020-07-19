<?php


namespace vsm\bl\QueryBuilder;


abstract class SQLBuilder 
{
    protected $properties = [];
    protected $Query=[];
    protected $Values=[];
    protected $Index=0;
    protected $filterField=null;
    protected $table;
    protected $db;
    protected $class;
    protected $generator;
    protected $canUseWhere=false;
    protected $whereUsed=false;
    protected $selectUsed=false;
    protected $orderUsed=false;
    protected $offsetUsed=false;
    protected $limitUsed=false;
    protected $groupByUsed=false;
    protected $functionUsed=false;
    protected $updateUsed=false;
    protected $insertUsed=false;
    protected $deleteUsed=false;

    public function getLastInsertedId(){
        return $this->db->lastInsertId();
    }

    /**
     * @param $stmt
     * @param $values
     * @param bool $bindValue
     */
    protected function AssignValues(&$stmt,$values,$bindValue=false){
        foreach ($values as $index=>$val){
            $field = $val['field'];
            $definition = $this->generator->getDefinitions($field);
            unset($val['field']);
            $items = (isset($val['values']) && count($val['values'])) ? $val['values']:$val;

            foreach ($items as $k=>$v){
                $pdoType = ($v==null)?\PDO::PARAM_NULL: $definition->PdoType;
                if(!$bindValue)
                    $stmt->bindParam($k,$v,$pdoType);
                else
                    $stmt->bindValue($k,$v,$pdoType);

            }
        }
    }



    protected function resetObject() {
        $blankInstance = new static($this->table,$this->class); //requires PHP 5.3+  for older versions you could do $blankInstance = new get_class($this);
        $reflBlankInstance = new \ReflectionClass($blankInstance);
        foreach ($reflBlankInstance->getProperties() as $prop) {
            $prop->setAccessible(true);
            $this->{$prop->name} = $prop->getValue($blankInstance);
        }
    }

    protected function InitializeWhere()
    {
        if (!$this->whereUsed) {
            $this->Query[] = 'WHERE';
            $this->whereUsed = true;
        }
    }

    protected function checkIfNumber($field){
        $definition = $this->generator->getDefinitions($field);
        if($definition->PdoType == \PDO::PARAM_INT){
            return true;
        }
        return  false;
    }

}