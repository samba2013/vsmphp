<?php

namespace vsm\dal;

interface IRepository
{

    public function Add($mixed);

    public function Remove($id);

    public function Find($id);

    public function FindAll();

    public function OnlyActive();

    public function Update($mixed);

    public function TotalRows($columns,$keyword);

    public function Commit();

    public function Paginate($pageNo,$pageSize);

    public function SearchAndPaginate($columns,$query,$pageNo,$pageSize);

    public function Filter($Where,$First = false);

}