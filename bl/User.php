<?php

namespace vsm\bl {


    class User extends Model
    {
        /**
         * @var integer
         * @required
         * @primaryKey
         */
        protected $id;

        /**
         * @var integer
         * @required
         */
        protected $role_id;

        /**
         * @var string
         * @required
         */
        protected $username;

        /**
         * @var string
         */
        protected $firstname;
        /**
         * @var string
         */
        protected $lastname;
        /**
         * @var string
         * @required
         */
        protected $password;


        public function __construct()
        {
            parent::__construct("admin.users");
        }


    }

}

