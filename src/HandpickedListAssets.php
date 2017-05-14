<?php
/**
 * Created by PhpStorm.
 * User: evgeny
 * Date: 11.03.17
 * Time: 13:43
 */

namespace atlant5\handpickedlist;
use yii\web\AssetBundle;


class HandpickedListAssets extends AssetBundle
{
    public $sourcePath = '@atlant5/handpicked-list/assets';
    //public $baseUrl = '@web';
    public $css = [];
    public $js = ['js/jquery.filtertable.js'];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
