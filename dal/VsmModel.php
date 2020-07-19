<?php


namespace vsm\dal;



use vsm\api\lib\Database;
use vsm\bl\Generator;
use vsm\bl\Model;
use vsm\bl\QueryBuilder\QueryBuilder;

abstract class VsmModel implements IRepository
{

    protected $db = null;

    private $dbtable;

    protected $classDefinition;

    protected $debug = false;



    public function __destruct()
    {
        $db = null;
    }

    public function __construct()
    {

        $this->db = Database::get()->connect();
        $class=get_called_class();
        $this->class=$class::$model;
        $this->dbtable = $class::$table;
    }

    /**
     * @return QueryBuilder
     */
    protected function getIQuerable(){
        return new QueryBuilder($this->dbtable,$this->class);
    }


    private function ConvertToUTC($value){
        if($value==null) return null;

        $newValue = new \DateTime($value);
        $newValue->setTimezone(new \DateTimeZone('UTC'));
        return $newValue->format("Y-m-d H:i:s");
    }


    /**
     * @param $mixed
     * @return int|null
     */
    public function Add($mixed)
    {
        $query=$this->getIQuerable();
        if($query->Add($mixed)->Commit(true)){
            return $query->getLastInsertedId();
        }

        return null;
    }

    /**
     * @param $id
     * @return bool
     * @throws \PDOException
     */
    public function Remove($id)
    {
        $item = $this->Find($id);
        if($item!=null) {
            $query = $query=$this->getIQuerable();
            if ($query->Delete($id)->Commit(true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $id
     * @return Model|null
     * @throws \PDOException
     */
    public function Find($id)
    {

        return  $this->getIQuerable()->Select()
                        ->Where("id")
                        ->Equal($id)
                        ->GetOne(true);

    }

    public function Filter($Where,$First = false)
    {
        $query = $this->getIQuerable()->Select();
            foreach ($Where as $Field=>$value){
                $query->Where($Field)->Equal($value);
            }
        return $query->GetResult(true);
    }

    public function OnlyActive()
    {
        return  $this->getIQuerable()->Select()
            ->Where("is_active")
            ->Equal(1)
            ->GetResult(true);
    }

    /**
     * @return mixed
     */
    public function FindAll(){
        return $this->getIQuerable()->Select()
                                    ->GetResult(true);
    }

    private function filterQuery($columns,$query,&$iquery){
        if(!empty($query))
            foreach ($columns as $column){
                $iquery->OrWhere($column)->Contains($query);
            }
    }

    /**
     * @return int
     * @throws \PDOException
     */
    public function TotalRows($columns,$query)
    {

        $iquery = $this->getIQuerable()->Count();
        $this->filterQuery($columns,$query,$iquery);
        $result=$iquery->GetOne();
        return $result->id_count;
    }

    public function Paginate($pageNo,$pageSize){

        $pageNo = ($pageNo<=0)?1:$pageNo;
        $pageSize = ($pageSize>50)?10:$pageSize;

        return $this->getIQuerable()->Select()
                                    ->OrderDescBy("id")
                                    ->Limit($pageSize)
                                    ->Offset($pageSize * ($pageNo - 1))
                                    ->GetResult(true);
    }

    public function SearchAndPaginate($columns,$query, $pageNo, $pageSize)
    {
        $pageNo = ($pageNo<=0)?1:$pageNo;
        $pageSize = ($pageSize>50)?10:$pageSize;

        $iquery= $this->getIQuerable()->Select();

        $this->filterQuery($columns,$query,$iquery);


        return    $iquery->OrderDescBy("id")
            ->Limit($pageSize)
            ->Offset($pageSize * ($pageNo - 1))
            ->GetResult(true);
    }

    /**
     * @param $mixed
     * @return bool
     * @throws \PDOException
     */
    public function Update($mixed)
    {
        $item = $this->Find($mixed->id);
        if($item!=null){

            $query=$this->getIQuerable()->Update($mixed)
                                        ->Where("id")->Equal($mixed->id);
            if($query->Commit(true)){
                return true;
            }

        }
        return false;
    }

    public function Commit()
    {
        // TODO: Implement Commit() method.
    }



}