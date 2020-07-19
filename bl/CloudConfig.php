<?php


namespace vsm\bl;


class CloudConfig extends Model
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
    protected $memory_size;

    /**
     * @var integer
     * @required
     */
    protected $storage_size;

    /**
     * @var string
     * @required
     */
    protected $location;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $os;

    /**
     * @var integer
     * @required
     */
    protected $bandwith;


    public function __construct()
    {
        parent::__construct("admin.cloud_config");

    }


}