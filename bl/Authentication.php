<?php


namespace vsm\bl;


class Authentication
{

    private $UserRepository;
    private $user;
    public $err=null;

    public function __construct($UserRepository)
    {
        $this->UserRepository=$UserRepository;
    }

    private function authenticate($username,$password){

        $u=$this->UserRepository->SignIn($username,$password);
        if($u==null) return false;
        $this->user=$u;
        return true;
    }

    public function Login($username,$password){
        if($this->authenticate($username,$password)){
            $_SESSION['user']=$this->user;
            return true;
        }
        $this->err="Login or password invalid!";
        return false;
    }


}