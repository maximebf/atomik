<?php

class ErrorsPlugin
{
	/** @var array */
    public static $config = array();
    
    public static function start(&$config)
    {
        $config = array_merge(array(
        
            /* @var bool */
            'catch_errors'           => false,
            
            /* @var bool */
            'throw_errors'           => false,
            
            'error_view'             => false,
            
            '404_view'             => false,
            
            /* @var array */
            'error_report_attrs'     => array(
                'atomik-error'               => 'style="padding: 10px"',
                'atomik-error-title'         => 'style="font-size: 1.3em; font-weight: bold; color: #FF0000"',
                'atomik-error-lines'         => 'style="width: 100%; margin-bottom: 20px; background-color: #fff;'
                                              . 'border: 1px solid #000; font-size: 0.8em"',
                'atomik-error-line'          => '',
                'atomik-error-line-error'    => 'style="background-color: #ffe8e7"',
                'atomik-error-line-number'   => 'style="background-color: #eeeeee"',
                'atomik-error-line-text'     => '',
                'atomik-error-stack'         => ''
            )
        	
        ), $config);
        self::$config = &$config;
    }
    
    public static function onAtomikError($e)
    {
        self::handle($e);
    }
    
    public static function onAtomikHttperror($e, &$cancel)
    {
        $cancel = true;
        self::handle($e);
    }
    
    public static function handle(Exception $e)
    {
        if ($e instanceof AtomikHttpException) {
            header('Location: ', false, $e->getCode());
            if ($e->getCode() === 404) {
                header('Content-type: text/html');
                if (file_exists($filename = self::$config['404_view'])) {
                    include($filename);
                } else {
                    echo '<h1>Page not found</h1>';
                }
            }
        } else {
            header('Location: ', false, 500);
            if (self::$config['catch_errors']) {
                echo self::renderException($e);
            } else if (self::$config['throw_errors']) {
                throw $e;
            }
        }
    }
    
    /**
     * Renders an exception
     * 
     * @param Exception $exception The exception which sould ne rendered
     * @param bool $return Return the output instead of printing it
     * @return string
     */
    public static function renderException($exception, $return = false)
    {
        // checks if the user defined error file is available
        if (file_exists($filename = self::$config['error_view'])) {
            include($filename);
            return;
        }
        
        $attributes = self::$config['error_report_attrs'];
    
        $html = '<div ' . $attributes['atomik-error'] . '>'
           . '<span ' . $attributes['atomik-error-title'] . '>' 
           . $exception->getMessage() . '</span>'
           . '<br />An error of type <strong>' . get_class($exception) . '</strong> '
           . 'was caught at <strong>line ' . $exception->getLine() . '</strong><br />'
           . 'in file <strong>' . $exception->getFile() . '</strong>'
           . '<p>' . $exception->getMessage() . '</p>'
           . '<table ' . $attributes['atomik-error-lines'] . '>';
        
        // builds the table which display the lines around the error
        $lines = file($exception->getFile());
        $start = $exception->getLine() - 7;
        $start = $start < 0 ? 0 : $start;
        $end = $exception->getLine() + 7;
        $end = $end > count($lines) ? count($lines) : $end; 
        for($i = $start; $i < $end; $i++) {
            // color the line with the error. with standard Exception, lines are
            if($i == $exception->getLine() - (get_class($exception) != 'ErrorException' ? 1 : 0)) {
                $html .= '<tr ' . $attributes['atomik-error-line-error'] . '><td>';
            }
            else {
                $html .= '<tr ' . $attributes['atomik-error-line'] . '>'
                       . '<td ' . $attributes['atomik-error-line-number'] . '>';
            }
            $html .= $i . '</td><td ' . $attributes['atomik-error-line-text'] . '>' 
                   . (isset($lines[$i]) ? htmlspecialchars($lines[$i]) : '') . '</td></tr>';
        }
        
        $html .= '</table>'
               . '<strong>Stack:</strong><p ' . $attributes['atomik-error-stack'] . '>' 
               . nl2br($exception->getTraceAsString())
               . '</p></div>';
        
        return $html;
    }
}
