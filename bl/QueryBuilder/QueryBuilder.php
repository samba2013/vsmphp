<?php


namespace vsm\bl\QueryBuilder;


use vsm\api\lib\Database;
use vsm\bl\Generator;

class QueryBuilder extends SQLBuilder implements IQueryBuilder
{


    public function __construct($table,$class)
    {
        $this->table = $table;
        $this->db=Database::get()->connect();
        $this->class = $class;
        $this->generator = new Generator($class);

    }

    public function GetOne($bindClass=false)
    {
        if(count($this->Query)){
            $values=$this->Values;
            $query=$this->AsQuery();

            $stmt = $this->db->prepare($query);
            $this->AssignValues($stmt,$values);
            $stmt->execute();
            if($bindClass)
                return $stmt->fetchObject($this->class);
            else
                return $stmt->fetchObject();
        }
        return NULL;
    }

    public function GetResult($bindClass=false)
    {
        if(count($this->Query)){
            $values=$this->Values;
            $funcused = $this->functionUsed;
            $query=$this->AsQuery();


            $stmt = $this->db->prepare($query);
            $this->AssignValues($stmt,$values,true);
            $stmt->execute();
            if($bindClass)
                return $stmt->fetchAll(\PDO::FETCH_CLASS, $this->class);
            elseif($funcused)
                return $stmt->fetchObject();
            else
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        return NULL;
    }


    /**
     * @param bool $bindValue
     * @return bool
     * @throws \PDOException
     */
    public function Commit($bindValue = false)
    {
        if(count($this->Query)){


            $values=$this->Values;
            $query=$this->AsQuery();
            $stmt = $this->db->prepare($query);

            $this->AssignValues($stmt,$values,$bindValue);

            $stmt->execute();


            return true;
        }
        return false;
    }



    /**
     * @return string
     */
    public function AsQuery(){
        $query= implode(" ",$this->Query).";";
        $this->resetObject();
        return $query;
    }


    /**
     * @param array $args
     * @return $this
     */
    public function Select($args=[])
    {
        $this->resetObject();
        $_Columns = count($args)?implode(",",$args):"*";
        $QuerySelect = "SELECT ".$_Columns." FROM {$this->table}";
        $this->Query[]=$QuerySelect;
        $this->canUseWhere=true;
        $this->selectUsed=true;
        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function Where($field)
    {
        if($this->canUseWhere && !empty($field)){
            if(!$this->whereUsed){
                $this->InitializeWhere();
            }else{
                $this->Query[]="AND";
            }

            $this->filterField = $field;
            $this->Query[]=$field;
            $this->Index++;
        }
        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function OrWhere($field)
    {
        if($this->canUseWhere && !empty($field)){

            if(!$this->whereUsed){
                $this->InitializeWhere();
            }else{
                $this->Query[]="OR";
            }

            $this->Query[]=$field;
            $this->filterField = $field;
            $this->Index++;
        }
        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function OrderBy($field)
    {
        if($this->selectUsed && !empty($field) && !$this->offsetUsed){
            if(!$this->orderUsed){
                $this->Query[]='Order by ';
                $this->Query[]=$field.' asc';

                $this->orderUsed=true;
            }
        }
        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function OrderDescBy($field)
    {
        if($this->selectUsed && !empty($field) && !$this->offsetUsed){
            if(!$this->orderUsed){
                $this->Query[]='Order by ';
                $this->Query[]=$field.' desc';
                $this->orderUsed=true;
            }
        }
        return $this;
    }

    public function GroupBy($fields)
    {
        if($this->selectUsed && !empty($fields)
            && $this->functionUsed
            && !$this->orderUsed
            && !$this->offsetUsed){

            if(!$this->groupByUsed){
                $this->Query[]='GROUP BY '.implode(",",$fields);
                $this->groupByUsed=true;
            }
        }
        return $this;
    }

    public function ThenBy($field)
    {
        if($this->selectUsed && !empty($field)){
            if($this->orderUsed){
                $this->Query[]=','.$field.' asc';
            }
        }
        return $this;
    }


    public function ThenByDesc($field)
    {
        if($this->selectUsed && !empty($field)){
            if($this->orderUsed){
                $this->Query[]=','.$field.' desc';
            }
        }
        return $this;
    }

    public function Offset($offset)
    {
        if($this->selectUsed && is_int($offset)){
            if(!$this->offsetUsed && $this->limitUsed) {
                $this->Query[] = 'OFFSET ' . $offset;
                $this->offsetUsed=true;
            }
        }
        return $this;
    }

    public function Limit($limit)
    {
        if($this->selectUsed && is_int($limit)){
            if(!$this->limitUsed) {
                $this->Query[] = 'LIMIT ' . $limit;
                $this->limitUsed = true;
            }
        }
        return $this;
    }

    public function Count( $field = "id", $prefix = '_count')
    {
        $this->resetObject();
        $QuerySelect = "SELECT COUNT($field) as {$field}{$prefix} FROM {$this->table}";
        $this->Query[]=$QuerySelect;
        $this->canUseWhere=true;
        $this->selectUsed=true;
        $this->functionUsed = true;
        return $this;
    }

    public function Sum($field = "id", $prefix = '_sum')
    {
        $this->resetObject();
        $QuerySelect = "SELECT SUM($field) as {$field}{$prefix} FROM {$this->table}";
        $this->Query[]=$QuerySelect;
        $this->canUseWhere=true;
        $this->selectUsed=true;
        return $this;
    }

    public function Update($mixed)
    {
        $this->resetObject();
        $mixed->modified_at = $mixed->getModifiedAt("UTC");
        $mixed->modified_by = $_SESSION['user']->id;
        $QuerySelect = "UPDATE {$this->table} SET";
        $queryFields = [];
        $definitions = $this->generator->getDefinitions();
        foreach ($this->generator->getProperties() as $field){
            if($field=='id' || $field=='generator') continue;
            $queryFields[]="{$field} = :{$field}";

            $this->Values[$this->Index]['field']=$field;
            $this->Values[$this->Index][":{$field}"]=$mixed->$field;
            $this->Index++;
        }
        $QuerySelect.=" ".implode(",",$queryFields);
        $this->Query[]=$QuerySelect;
        $this->updateUsed = true;
        $this->canUseWhere = true;
        return $this;

    }

    public function Add($mixed)
    {
        $this->resetObject();
        $QuerySelect = "INSERT INTO {$this->table}";
        $queryFields = [];
        $queryFieldsValues = [];
        $definitions = $this->generator->getDefinitions();
        foreach ($this->generator->getProperties() as $field){

            if($field=='id' || $field=='generator') continue;

            $queryFields[]=$field;
            $queryFieldsValues[]=":{$field}";
            $this->Values[$this->Index]['field']=$field;
            $this->Values[$this->Index][":{$field}"]=$mixed->$field;
            $this->Index++;
        }
        $QuerySelect.=" (".implode(",",$queryFields).") VALUES (".implode(",",$queryFieldsValues).")";
        $this->Query[]=$QuerySelect;
        $this->insertUsed = true;
        return $this;
    }

    public function Delete($id)
    {
        $this->resetObject();
        $QuerySelect = "DELETE FROM {$this->table}";
        $this->Query[]=$QuerySelect;
        $this->deleteUsed = true;
        $this->canUseWhere = true;
        return $this->Where("id")->Equal($id);
    }

    public function InValues($values)
    {
        $this->InitializeWhere();
       if($this->whereUsed){
           $cond = [];
           $this->Values[$this->Index]['values']=[];
           $this->Values[$this->Index]['field']=$this->filterField;
           foreach ($values as $k=>$value){
               $cond[]=":val_{$k}";
               $this->Values[$this->Index]['values'][":val_{$k}"]=$value;
           }
           $this->Query[]='IN ('.implode(',',$cond).')';
       }
       return $this;
    }

    public function Between($value1, $value2)
    {
        $this->InitializeWhere();
        if($this->whereUsed){

            $this->Values[$this->Index]['field']=$this->filterField;
            $this->Values[$this->Index][':val1']=$value1;
            $this->Values[$this->Index][':val2']=$value2;
            $this->Query[]='BETWEEN :val1 AND :val2';
        }
        return $this;
    }

    public function Equal($value)
    {
        $this->InitializeWhere();
        if($this->whereUsed){
            $this->Values[$this->Index]['field']=$this->filterField;
            $this->Values[$this->Index][':eq_'.$this->Index]=$value;
            $this->Query[]='= :eq_'.$this->Index;
        }
        return $this;
    }

    public function GreatThen($value)
    {
        $this->InitializeWhere();

        if($this->whereUsed){
            $this->Values[$this->Index]['field']=$this->filterField;
            $this->Values[$this->Index][':gt_'.$this->Index]=$value;
            $this->Query[]='> :gt_'.$this->Index;
        }
        return $this;
    }

    public function GreatOrEqualThen($value)
    {
        $this->InitializeWhere();
        if($this->whereUsed){

            $this->Values[$this->Index]['field']=$this->filterField;
            $this->Values[$this->Index][':gte_'.$this->Index]=$value;
            $this->Query[]='>= :gte_'.$this->Index;
        }
        return $this;
    }

    public function LessOrEqualThen($value)
    {
        $this->InitializeWhere();
        if($this->whereUsed){
            $this->Values[$this->Index]['field']=$this->filterField;
            $this->Values[$this->Index][':lte_'.$this->Index]=$value;
            $this->Query[]='<= :lte_'.$this->Index;
        }
        return $this;
    }

    public function LessThen($value)
    {
        $this->InitializeWhere();
        if($this->whereUsed){
            $this->Values[$this->Index]['field']=$this->filterField;
            $this->Values[$this->Index][':lt_'.$this->Index]=$value;
            $this->Query[]='< :lt_'.$this->Index;
        }
        return $this;
    }


    public function Contains($value)
    {
        $this->InitializeWhere();
        if($this->whereUsed){
            if($this->checkIfNumber($this->filterField)) return $this->Equal(intval($value));
            $this->Values[$this->Index]['field']=$this->filterField;
            $this->Values[$this->Index][':like_'.$this->Index]="%{$value}%";
            $this->Query[]='LIKE :like_'.$this->Index;
        }
        return $this;
    }

    public function StartsWith($value)
    {
        $this->InitializeWhere();
        if($this->whereUsed){
            if($this->checkIfNumber($this->filterField)) return $this->Equal(intval($value));
            $this->Values[$this->Index]['field']=$this->filterField;
            $this->Values[$this->Index][':like_'.$this->Index]="{$value}%";
            $this->Query[]='LIKE :like_'.$this->Index;
        }
        return $this;
    }

    public function EndWith($value)
    {
        $this->InitializeWhere();
        if($this->whereUsed){
            if($this->checkIfNumber($this->filterField)) return $this->Equal(intval($value));
            $this->Values[$this->Index]['field']=$this->filterField;
            $this->Values[$this->Index][':like_'.$this->Index]="%{$value}";;
            $this->Query[]='LIKE :like_'.$this->Index;
        }
        return $this;
    }
}