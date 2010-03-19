<?php

class ModelFormHelper extends Atomik_Helper
{
    private static $_template;
    
    public static function setTemplate($template)
    {
        self::$_template = $template;
    }
    
    public static function getTemplate()
    {
        if (self::$_template === null) {
            self::$_template = dirname(__FILE__) . '/modelFormTemplate.php';
        }
        return self::$_template;
    }
    
    public function modelForm($model, $buttonLabel = 'Save')
    {
        $descriptor = Atomik_Model_Descriptor::factory($model);
        $fields = array();
        
        foreach ($descriptor->getFields() as $field) {
            if (isset($field->form)) {
                $fields[] = $field;
            }
        }
        
        $vars = array(
            'model' => $model,
            'descriptor' => $descriptor,
            'fields' => $fields,
            'buttonLabel' => $buttonLabel
        );
        return Atomik::renderFile(self::getTemplate(), $vars);
    }
}