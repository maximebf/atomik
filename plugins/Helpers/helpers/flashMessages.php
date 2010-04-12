<?php

class FlashMessagesHelper extends Atomik_Helper
{
    public function flashMessages($id = 'flash-messages')
    {
        $html = '';
    	foreach (A('flash:all') as $label => $messages) {
    	    foreach ($messages as $message) {
    	        $html .= sprintf('<li class="%s">%s</li>', $label, $message);
    	    }
    	}
    	if (empty($html)) {
    	    return '';
    	}
    	return '<ul id="' . $id . '">' . $html . '</ul>';
    }
}