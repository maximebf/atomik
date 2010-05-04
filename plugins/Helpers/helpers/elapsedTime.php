<?php

class ElapsedTimeHelper extends Atomik_Helper
{
    public static $texts = array(
        'times' => array("second", "minute", "hour", "day", "week", "month", "years", "decade"),
        'less_than_a_minute' => 'less than a minute ago',
        'ago' => 'ago',
        'togo' => 'to go'
    );
    
    public static $agoAfter = true;
    
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
        $periods = self::$texts['times']; 
        $lengths = array(60, 60, 24, 7, 4.35, 12, 10); 
        $ending = self::$texts['ago'];
        
        if ($diff < 60) {
            return self::$texts['less_than_a_minute'];
        } else if ($diff < 0) {
            $diff = -$diff; 
            $ending = self::$texts['togo']; 
        }
              
        for($j = 0; $j < count($lengths) && $diff >= $lengths[$j]; $j++) {
            $diff /= $lengths[$j]; 
        }
        $diff = round($diff); 
        if($diff != 1) {
            $periods[$j].= "s"; 
        }
        
        if (self::$agoAfter) {
            return "$diff $periods[$j] $ending";
        } else {
            return "$ending $diff $periods[$j]";
        }
    }
}