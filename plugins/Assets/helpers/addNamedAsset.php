<?php

class AddNamedAssetHelper
{
    public function addNamedAsset($name)
    {
        Atomik_Assets::getInstance()->addNamedAsset($name);
    }
}