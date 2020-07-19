<?php

namespace vsm\bl\SessionHandler;

class SessionHandler
{
    private static $instance;
    private $session;
    public function __construct()
    {
        $this->session = $_SESSION;

    }

    public static function getInstance(){
        if(!self::$instance)
            self::$instance = new static();
        return self::$instance;
    }

    public function getUser(){
        return $this->session['user'];
    }

    public function isConnected(){
        return isset($this->session['user']);
    }


    public function isAppAllowed($app){
        if(array_key_exists($app,APP_ROLES)){
            if($this->getUser()->role_id <= APP_ROLES[$app]){
                return true;
            }
        }
            return false;
    }
}