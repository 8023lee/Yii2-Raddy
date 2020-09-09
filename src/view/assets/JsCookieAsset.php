<?php

namespace benbanfa\raddy\view\assets;

use yii\web\AssetBundle;

/**
 * bower-asset/js-cookie前端资源文件的AssetBundle
 */
class JsCookieAsset extends AssetBundle
{
    public $sourcePath = '@npm/js-cookie/src';
    public $js = [
        'js.cookie.js',
    ];
}
