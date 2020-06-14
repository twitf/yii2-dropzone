<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2017/9/11
 * Time: 13:33
 */

namespace twitf\dropzone;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\InputWidget;

class DropZone extends InputWidget
{
    /**
     * dropzone配置选项，
     * 详情参阅 http://www.dropzonejs.com/#configuration-options
     * @var array
     */
    public $clientOptions = [];

    /**dropzone事件侦听
     * 详情参阅 http://www.dropzonejs.com/#event-list
     * @var array
     */
    public $clientEvents = [];

    /**
     * dropzone默认配置
     * @var array
     */
    public $options = [];

    /**
     * dropzone默认侦听
     * @var array
     */
    public $events = [];
    /**
     * 禁用dropzone自动发现所有元素
     * @var bool
     */
    public $autoDiscover = false;

    /**
     * dropzone容器
     * @var string
     */
    public $containerId = 'myDropzone';

    /**
     * dropzone预览容器
     * @var string
     */
    public $previewsId = 'dz-previews';

    /**
     * 回显的图片数组  数组格式['/uploads/xxxxx.jpg','/uploads/xxxxx.jpg']
     * @var array
     */
    public $mockFiles=[];
    /**
     * 开启图片排序
     * @var bool
     */
    public $sortable = true;

    /**
     * Sortable配置参数
     * 详情参阅 https://github.com/RubaXa/Sortable#options
     * @var array
     */

    public $sortableOption = [];
    /**
     * 自动上传关闭时,上传按钮的html
     * @var string
     */
    public $upload_button='';

    /**
     * input Name名 默认file
     * @var string
     */
    public $inputName='file';

    /**
     * 初始化小部件
     */
    public function init()
    {
        $this->initOptions();
        $this->initConainerId();
        $this->initInputName();
        $this->initEvents();
        $this->registerAssets();
        $this->initMockFile();
        $this->initSortable();
        //模型存在
        if ($this->hasModel()){
            parent::init();
        }
    }

    /**
     * 初始化inputName
     */
    public function initInputName(){
        if ($this->hasModel()){
            $this->inputName= Html::getInputName($this->model, $this->attribute);
        }
    }

    /**
     * 初始化容器id
     */
    public function initConainerId(){
        if ($this->hasModel()) {
            $this->containerId = 'myDropzone_'.$this->id;
        }
    }

    public function initOptions(){
        $this->options = [
            'url' => Url::to(['upload']),
            'addRemoveLinks'=>true,
            'dictCancelUpload'=>'取消上传',
            'parallelUploads'=>255,//并行处理的文件数量 默认无限放大 不做限制
            'dictRemoveFile'=>'删除文件',
            'autoProcessQueue' => true, //自动上传
            'maxFilesize' => get_cfg_var("post_max_size") ? (int)get_cfg_var("post_max_size") : 0,//上传大小限制
        ];

        //构造请求参数csrf
        if (\Yii::$app->getRequest()->enableCsrfValidation) {
            $this->options['headers'][\yii\web\Request::CSRF_HEADER] = \Yii::$app->getRequest()->getCsrfToken();
            $this->options['params'][\Yii::$app->getRequest()->csrfParam] = \Yii::$app->getRequest()->getCsrfToken();
        }

        $this->options = ArrayHelper::merge($this->options, $this->clientOptions);
    }

    /**
     * 初始化事件侦听
     */
    public function initEvents(){
        $this->events=[
            'success'=>'function (file, response) {
                if(response.status==="success"){
                    var input = document.createElement("input");
                    input.setAttribute("type", "hidden");
                    input.setAttribute("name", "'.$this->inputName.'[]");
                    input.setAttribute("value", response.savePath);
                    file.previewElement.appendChild(input);
                }
            }',
            'queuecomplete'=>'function(file){
                '.$this->containerId.'.on("removedfile",function(file){
                        $.ajax({
                            type: "POST",
                            url: "'.Url::to(['remove']).'",
                            data: {
                                url: file.previewElement.getElementsByTagName("input")[0].value
                            },
                            dataType: "json",
                            success: function(response){
                                if(response.status === "success"){
                                    '.$this->containerId.'.options.maxFiles++;
                                }
                            }
                        });
                    }
                );
            }',

        ];
        $this->events = ArrayHelper::merge($this->events, $this->clientEvents);
    }

    /**
     * 初始化排序
     */
    public function initSortable(){
        if (isset($this->sortable) && $this->sortable !== false) {
            $this->getView()->registerJs('Sortable.create(document.getElementById("' . $this->containerId . '"),' . Json::encode($this->sortableOption) . ');');
        }
    }

    /**
     * 图片回显
     * @param $mockFiles
     */
    public function initMockFile(){
        if (!empty($this->mockFiles)){
            //回显数据处理后的数组
            $mockFile_arr=[];
            foreach ($this->mockFiles as $key=>$value){
                $mockFile_arr[$key]['name']=@basename($value);
                $mockFile_arr[$key]['size']=@ceil(@filesize(\Yii::getAlias("@webroot").$value));
                $mockFile_arr[$key]['url']=$value;
            }
            $mockFileJson=Json::encode($mockFile_arr);
            $this->getView()->registerJs(
                '$.each('.$mockFileJson.',function(index,data){'.
                        'var mockFile = {name:data.name, size: data.size,data_url:data.url};'.
                        $this->containerId.'.emit("addedfile", mockFile);'.
                        $this->containerId.'.emit("thumbnail", mockFile,data.url);'.
                        $this->containerId.'.emit("success", mockFile,{"status":"success","savePath":data.url});'.
                        $this->containerId.'.emit("complete", mockFile);'.
                        $this->containerId.'.options.maxFiles ='.$this->containerId.'.options.maxFiles -1'.
                    '});');
        }
    }

    public function run()
    {
        if ($this->hasModel()){
            $input= Html::hiddenInput($this->inputName);
        }
        return Html::tag('div',isset($input)?$input.$this->upload_button:$this->upload_button, ['id' => $this->containerId, 'class' => 'dropzone','style'=>'position: relative']);

    }

    /**
     * 注册小部件至页面
     */
    protected function registerAssets()
    {
        $this->autoDiscover=$this->autoDiscover?'true':'false';
        $js = 'Dropzone.autoDiscover = ' . $this->autoDiscover . '; var ' . $this->containerId . ' = new Dropzone("div#' . $this->containerId . '", ' . Json::encode($this->options) . ');';

        if (!empty($this->events)) {
            foreach ($this->events as $key => $value) {
                $js .= "$this->containerId.on('$key', $value);";
            }
        }

        //是否开启自动上传
        if ($this->options['autoProcessQueue']===false&&empty($this->upload_button)){
            $this->upload_button='<button type="submit" class="btn btn-primary pull-right" id="upload_button" style="position:absolute;right:0.5rem;top: 0.5rem;">上传</button>';
            $js.=$this->containerId.'.element.querySelector("button[id=upload_button]").addEventListener("click",function (e) {
                e.preventDefault();
                e.stopPropagation();
                '.$this->containerId.'.processQueue();
            });';
        }

        $this->getView()->registerJs($js);
        $this->registerFancBox();
        DropZoneAsset::register($this->getView());
    }

    public function registerFancBox()
    {
        $js = <<<JS
$('[data-fancybox="gallery"]').fancybox({

  // Internationalization
  // ====================

  lang:"zh_cn",
  i18n: {
    en: {
      CLOSE: "Close",
      NEXT: "Next",
      PREV: "Previous",
      ERROR: "The requested content cannot be loaded. <br/> Please try again later.",
      PLAY_START: "Start slideshow",
      PLAY_STOP: "Pause slideshow",
      FULL_SCREEN: "Full screen",
      THUMBS: "Thumbnails",
      DOWNLOAD: "Download",
      SHARE: "Share",
      ZOOM: "Zoom"
    },
    zh_cn: {
      CLOSE: "关闭",
      NEXT: "下一张",
      PREV: "上一张",
      ERROR: "无法加载所请求的内容。<br>请稍后再试。",
      PLAY_START: "开始幻灯片放映",
      PLAY_STOP: "暂停幻灯片放映",
      FULL_SCREEN: "全屏",
      THUMBS: "缩略图",
      DOWNLOAD: "下载",
      SHARE: "分享",
      ZOOM: "缩放"
    }
  }
});
JS;
        $this->getView()->registerJs($js);
    }
}
