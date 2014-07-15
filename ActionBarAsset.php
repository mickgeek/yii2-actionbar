<?php
namespace mickgeek\actionbar;

use yii\web\AssetBundle;

/**
 * ActionBarAsset represents a collection of asset files, such as CSS, JS, images.
 *
 * @author Oleg Belostotskiy <olgblst@gmail.com>
 */
class ActionBarAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@mickgeek/actionbar/assets';
    /**
     * @inheritdoc
     */
    public $css = [
        'css/actionbar.css',
    ];
}
