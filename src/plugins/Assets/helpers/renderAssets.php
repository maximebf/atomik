<?php

class RenderAssetsHelper
{
    public function renderAssets()
    {
        $themeAssets = '';
        if (($theme = AssetsPlugin::getTheme()) !== null) {
            $themeAssets = $theme->render();
        }
        
        return Atomik_Assets::getInstance()->render() . $themeAssets;
    }
}