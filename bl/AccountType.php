<?php


namespace vsm\bl;


class AccountType extends Model
{

    /**
     * @var integer
     * @required
     * @primaryKey
     */
    protected $id;

    /**
     * @var string
     * @required
     */
    protected $name;

    /**
     * @var integer
     * @required
     */
    protected $maxinstances;




    public function __construct()
    {

        parent::__construct("admin.account_type");

    }


}