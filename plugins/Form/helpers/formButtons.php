<?php

class FormButtonsHelper extends Atomik_Helper
{
    public function formButtons($submitText = 'Submit', $cancelUrl = '#', $buttonAttrs = array())
    {
        $html = $this->helpers->formButton($submitText, 'submit', $buttonAttrs);
        
        if ($cancelUrl !== false) {
            $html .= sprintf('<span class="cancel">%s <a href="%s">%s</a></span>',
                __('or'), $cancelUrl, __('cancel'));
        }
        
        return $html;
    }
}
