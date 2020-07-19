<?php


namespace vsm\bl\QueryBuilder;


interface IQueryBuilder
{
    public function Select($args=[]);

    public function Where($field);

    public function OrWhere($field);

    public function OrderBy($field);

    public function OrderDescBy($field);

    public function GroupBy($fields);

    public function ThenBy($field);

    public function ThenByDesc($field);

    public function Offset($offset);

    public function Limit($limit);

    public function Count($field='id',$prefix = '_count');

    public function Sum($field="id",$prefix = '_sum');

    public function Update($mixed);

    public function Add($mixed);

    public function Delete($id);

    public function AsQuery();

    public function InValues($values);

    public function Between($value1,$value2 );

    public function Equal($value);

    public function Contains($value);

    public function StartsWith($value);

    public function EndWith($value);

    public function GreatThen($value);

    public function GreatOrEqualThen($value);

    public function LessOrEqualThen($value);

    public function LessThen($value);

    public function Commit($bindValue=false);

    public function GetResult($bindClass=false);

    public function GetOne($bindClass=false);




}