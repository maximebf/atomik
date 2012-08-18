<?php
/*
 * This file is part of the Atomik package.
 *
 * (c) 2012 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represent a non-escapable value
 * 
 * @package Atomik
 * @subpackage Db
 */
class Atomik_Db_Query_Expr
{
	/**
	 * @var string
	 */
	public $value = '';
	
	/**
	 * Constructor
	 * 
	 * @param string $value
	 */
	public function __construct($value = '')
	{
		$this->value = $value;
	}
	
	/**
	 * PHP Magic method. Returns the value
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->value;
	}
}
