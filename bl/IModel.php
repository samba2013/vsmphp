<?php


namespace vsm\bl;


interface IModel
{

    public function getAddQuery();

    public function getRemoveQuery();

    public function getUpdateQuery();

    public function getSelectQuery($params=[]);



}