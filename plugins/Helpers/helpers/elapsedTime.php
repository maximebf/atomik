<?php

class ElapsedTimeHelper extends Atomik_Helper
{
    /**
     * From http://www.weberdev.com/get_example-4769.html
     */
    public function elapsedTime($timestamp)
    {
        if ($timestamp instanceof DateTime) {
            $timestamp = $timestamp->format('U');
        } else if (is_string($timestamp)) {
            $time = strtotime($timestamp);
        }
        
        $diff = time() - $timestamp; 
        $periods = array("second", "minute", "hour", "day", "week", "month", "years", "decade"); 
        $lengths = array(60, 60, 24, 7, 4.35, 12, 10); 
        $ending = 'ago';
        
        if ($diff < 60) {
            return 'less than a minute ago';
        } else if ($diff < 0) {
            $diff = -$diff; 
            $ending = "to go"; 
        }
              
        for($j = 0; $j < count($lengths) && $diff >= $lengths[$j]; $j++) {
            $diff /= $lengths[$j]; 
        }
        $diff = round($diff); 
        if($diff != 1) {
            $periods[$j].= "s"; 
        }
        
        return "$diff $periods[$j] $ending"; 
    }
}