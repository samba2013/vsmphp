<?php


namespace vsm\dal;


use vsm\bl\Account;

class AccountRepository extends VsmModel
{

    public static $table = "admin.accounts";
    public static $model = Account::class;

    private static $Instance;

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