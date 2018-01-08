<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2017/9/11
 * Time: 13:45
 */
namespace twitf\dropzone;
use yii\web\AssetBundle;
class DropZoneAsset extends AssetBundle
{
    public $js = [
        'dist/min/dropzone.min.js',
        'Sortable/Sortable.min.js',
    ];

    public $css=[
        'dist/min/dropzone.min.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR.'/assets/';
    }

}