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
 * @package Atomik
 * @subpackage Db
 */
interface Atomik_Db_Query_Generator_Interface
{
	public function generate(Atomik_Db_Query $query);
}
