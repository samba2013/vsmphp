<?php


namespace vsm\dal;

use vsm\bl\AccountType;

class AccountTypeRepository extends VsmModel
{

    public static $table="admin.account_type";
    private static $Instance;
    public static $model = AccountType::class;


    public static function getInstance(){
        if(self::$Instance == null){
            self::$Instance = new self();

        }
        return self::$Instance;
    }


    public function __construct()
    {
        parent::__construct();
    }


}