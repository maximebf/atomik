<?php

class SqlDateHelper
{
	public function sqlDate($sqlDate, $format = 'H:i:s m/d/Y')
	{
		$date = explode(' ', $sqlDate);
		$time = explode(':', $date[1]);
		$date = explode('-', $date[0]);
		$timestamp = @mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);
		return @date($format, $timestamp);
	}
}