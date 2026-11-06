<?php
/**
 * @link https://github.com/borodulin/yii2-flot
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-flot/blob/master/LICENSE.md
 */
namespace conquer\flot;

use yii\helpers\Html;
use yii\helpers\Json as DataJson;
use conquer\helpers\Json;

/**
 * Flot home page @link http://www.flotcharts.org
 */
class FlotWidget extends \yii\base\Widget
{
	
	public $plugins = [];

	/**
	 * Container tag
	 * @var string
	 */
	public $tag = 'div';
	
	/**
	 * Html attributes of placeholder
	 * @var array()
	 */
	public $htmlOptions;
	
	/**
	 * General Flot options
	 * @link https://github.com/flot/flot/blob/master/API.md
	 * @var array()
	 */
	public $options;
	
	/**
	 * The data is an array of data series:
	 * @link https://github.com/flot/flot/blob/master/API.md#data-format
	 * The data is encoded as plain JSON: strings with the "js:" prefix are NOT turned into JavaScript,
	 * so labels coming from users are safe. Put callbacks into $options.
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
		if (!isset($this->htmlOptions['id'])) {
		    $this->htmlOptions['id'] = $this->getId();
		}
		echo Html::beginTag($this->tag, $this->htmlOptions);
	}
	
	/**
	 * @inheritdoc
	 */
	public function run()
	{
	    echo Html::endTag($this->tag);
	    
	    $view = $this->getView();
	    
	    FlotAsset::register($view, $this->plugins);
	    
		$data = DataJson::htmlEncode($this->data);
		$options = Json::encode($this->options);
		
		$view->registerJs("jQuery.plot('#{$this->htmlOptions['id']}', $data, $options);");
	}

}