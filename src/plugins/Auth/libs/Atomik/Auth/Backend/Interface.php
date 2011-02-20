<?php

interface Atomik_Auth_Backend_Interface
{
	function authentify($username, $password);
}