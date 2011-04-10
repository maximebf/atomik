<?php

class Atomik_Assets_Compressor
{
    public function setAssetsDirectory($dir)
    {
        $this->_dir = $dir;
    }
    
    public function getAssetsDirectory()
    {
        return $this->_dir;
    }
    
    public function compressCss($css)
    {
        // comments
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		// spaces
		$css = preg_replace('/(?!".+)\s+(?!.+")/', ' ', $css);
		// linebreaks
		$css = preg_replace('/\n|\t|\r|\r\n/', '', $css);
		// useless spaces
		$css = preg_replace('/\s*((\{|\}|:|;))\s*/', '$1', $css);
		return $css;
    }
}