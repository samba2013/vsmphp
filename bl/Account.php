<?php


namespace vsm\bl;


class Account extends Model
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
    protected $account_type_id;

    /**
     * @var string
     * @required
     */
    protected $token;




    public function __construct()
    {

        parent::__construct("admin.accounts");


    }



}