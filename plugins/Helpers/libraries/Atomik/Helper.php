<?php

abstract class Atomik_Helper
{
    /**
     * @var Atomik
     */
    protected $helpers;
    
    public function __construct()
    {
        $this->helpers = Atomik::instance();
    }
}