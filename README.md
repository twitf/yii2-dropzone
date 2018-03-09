twitf/yii2-dropzone
==============
[![Latest Stable Version](https://poser.pugx.org/twitf/yii2-dropzone/v/stable)](https://packagist.org/packages/twitf/yii2-dropzone)  [![Total Downloads](https://poser.pugx.org/twitf/yii2-dropzone/downloads)](https://packagist.org/packages/twitf/yii2-dropzone)  [![License](https://poser.pugx.org/twitf/yii2-dropzone/license)](https://packagist.org/packages/twitf/yii2-dropzone)


Yii2 Dropzone Extention , Supports sorting
> PS：再三强调不要使用jquery2.2.4以上版本，不兼容jquery2.2.4以上版本

```
可以在此处自定义资源包 xxxx/config/config.php配置如下
'components'=>[
            'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,// 一定不要发布该资源
                    'js' => [
                        '/js/jquery-2.2.4.min.js',
                    ]
                ],
            ],
        ],
]

```

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist twitf/yii2-dropzone "*"
```

or add

```
"twitf/yii2-dropzone": "*"
```

to the require section of your `composer.json` file.


Use
-----

Once the extension is installed, simply use it in your code by  :

>Controller Example

```php
    <?php
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'twitf\dropzone\UploadAction',
                'config' => [
                    "filePathFormat" => "/uploads/image/".date('YmdHis').'/', //上传保存路径
                    "fileRoot" => Yii::getAlias("@webroot"),//上传根目录
                ],
            ],
            'remove' => [
                'class' => 'twitf\dropzone\RemoveAction',
                'config' => [
                    "fileRoot" => Yii::getAlias("@webroot"),//上传根目录
                ],
            ],

        ];
    }
   
   ```
    
>view Example   详情`\你的项目\vendor\twitf\yii2-dropzone\DropZone.php`的注释（我感觉已经很详细了）
    
   ```php
    <?php
    echo \twitf\dropzone\DropZone::widget();
    //Or
    echo \twitf\dropzone\DropZone::widget(
        [
            //开启拖拽排序        
            'sortable'=>true,
            /**
             * Sortable配置参数
             * 详情参阅 https://github.com/RubaXa/Sortable#options
             * @var array
             */
            'sortableOption' => [],
            //回显的数据 内容我格式大概就这样子
            'mockFiles'=>['/uploads/image/20180107152242/xxxxxx.jpg','/uploads/image/20180107152242/xxxxxxx.jpg'],
            /*
            * dropzone配置选项，
            * 详情参阅 http://www.dropzonejs.com/#configuration-options
            * @var array
            */
            'clientOptions' => [
                    'maxFiles'=>5,
                'maxFilesize' => '7',
                'autoProcessQueue'=>false,
                'dictCancelUpload'=>'取消上传',
                'dictRemoveFile'=>'删除文件',
                'addRemoveLinks'=>true
            ],
           /**dropzone事件侦听
            * 详情参阅 http://www.dropzonejs.com/#event-list
            * @var array
            */
            'clientEvents'=>[
                'success'=>'function (file, response) {console.log(response)}',
            ]
        ]
    );

    //Or
    echo $form->field($model, 'file')->widget('twitf\dropzone\DropZone', [
        'sortable'=>true,
        'clientOptions' => [
            'maxFilesize' => '7',
            'autoProcessQueue'=>true,
            'dictCancelUpload'=>'取消上传',
            'dictRemoveFile'=>'删除文件',
            'addRemoveLinks'=>true
        ]
    ]);

    
    //Or
    echo \twitf\dropzone\DropZone::widget([
        'sortable'=>true,
        'model' => $model,
        'attribute' => 'file',
        'clientOptions' => [
            'maxFilesize' => '7',
            'autoProcessQueue'=>true,
            'dictCancelUpload'=>'取消上传',
            'dictRemoveFile'=>'删除文件',
            'addRemoveLinks'=>true
        ]
    ]);
    ?>

   ```

> 有问题欢迎致电我的邮箱 837422076@qq.com

    
