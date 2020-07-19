<?php


namespace vsm\bl;


class Cloud extends Model
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
    protected $account_id;

    /**
     * @var string
     * @required
     */
    protected $status;

    /**
     * @var string
     * @required
     */
    protected $username;

    /**
     * @var string
     * @required
     */
    protected $password;

    /**
     * @var integer
     * @required
     */
    protected $sshport;

    /**
     * @var integer
     * @required
     */
    protected $cloud_config_id;

    public function __construct()
    {
        parent::__construct("admin.cloud");
    }


}