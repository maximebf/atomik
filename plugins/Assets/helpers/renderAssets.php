<?php

class RenderAssetsHelper
{
    public function renderAssets()
    {
        return Atomik_Assets::getInstance()->render()
             . AssetsPlugin::getTheme()->render();
    }
}