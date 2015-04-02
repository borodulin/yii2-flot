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

/**
 * Flot home page {@link http://www.flotcharts.org/}
 */
class FlotWidget extends \yii\base\Widget
{
	
	public $plugins=[];
	
	/**
	 * Html attributes of placeholder
	 * @var array()
	 */
	public $htmlOptions;
	
	/**
	 * General Plot options
	 * @link https://github.com/flot/flot/blob/master/API.md
	 * @var array()
	 */
	public $options;
	
	/**
	 * The data is an array of data series:
	 * @link https://github.com/flot/flot/blob/master/API.md#data-format
	 * @var array()
	 */
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