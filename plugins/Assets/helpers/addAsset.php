<?php

class AddAssetHelper
{
    public function addAsset($url, $type = null, $dependencies = array())
    {
        if (is_array($url)) {
            Atomik_Assets::getInstance()->addAssets($url);
            return;
        }
        
        Atomik_Assets::getInstance()->addAsset($url, $type, $dependencies);
    }
}