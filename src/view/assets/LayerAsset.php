<?php

namespace benbanfa\raddy\view\assets;

use yii\web\AssetBundle;

/**
 * Layer前端资源文件的AssetBundle
 *
 * @see http://layer.layui.com/
 */
class LayerAsset extends AssetBundle
{
    public $sourcePath = '@npm/layerui/dist';
    public $js = [
        'layer.js',
    ];
}
