<?php
/**
 * @link https://github.com/borodulin/yii2-flot
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-flot/blob/master/LICENSE.md
 */
namespace conquer\flot;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

class FlotWidget extends \yii\base\Widget
{
	
	public $plugins=[];
	
	public $htmlOptions;
	
	public $options;
	
	public $data;
	
	/**
	 * Initializes the widget.
	 * If you override this method, make sure you call the parent implementation first.
	 */
	public function init()
	{
		parent::init();
		FlotAsset::register($this->getView(), $this->plugins);
	}
	
	/**
	 * @inheritdoc
	 */
	public function run()
	{
		$htmlOptions=$this->htmlOptions;
		if(empty($htmlOptions['id']))
			$htmlOptions['id']=$this->getId();
		$data=Json::encode($this->data);
		$options=Json::encode($this->options);
		$this->view->registerJs("jQuery.plot('#{$htmlOptions['id']}',$data,$options);");
		return Html::tag('div','',$htmlOptions);
	}

}