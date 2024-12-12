<?php

namespace pixeldeluxe\familytree\web\assets\element;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ElementAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@pixeldeluxe/familytree/web/assets/element/dist';
        $this->depends = [CpAsset::class];

        $this->css = [
            'style.css',
        ];

        $this->js = [
            'script.js',
        ];

        parent::init();
    }
}