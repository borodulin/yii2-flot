<?php
/**
 * @link https://github.com/borodulin/yii2-flot
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-flot/blob/master/LICENSE.md
 */
namespace conquer\flot;

use yii\helpers\ArrayHelper;

/**
 * Flot home page {@link http://www.flotcharts.org/}
 */
class FlotAsset extends \yii\web\AssetBundle
{
	
	const PLUGIN_COLORHELPERS = 'jquery.colorhelpers.js';
	const PLUGIN_CANVAS = 'jquery.flot.canvas.js';
	const PLUGIN_CATEGORIES = 'jquery.flot.categories.js';
	const PLUGIN_CROSSHAIR = 'jquery.flot.crosshair.js';
	const PLUGIN_ERRORBARS = 'jquery.flot.errorbars.js';
	const PLUGIN_FILLBETWEEN = 'jquery.flot.fillbetween.js';
	const PLUGIN_IMAGE = 'jquery.flot.image.js';
	const PLUGIN_NAVIGATE = 'jquery.flot.navigate.js';
	const PLUGIN_PIE = 'jquery.flot.pie.js';
	const PLUGIN_RESIZE = 'jquery.flot.resize.js';
	const PLUGIN_SELECTION = 'jquery.flot.selection.js';
	const PLUGIN_STACK = 'jquery.flot.stack.js';
	const PLUGIN_SYMBOL = 'jquery.flot.symbol.js';
	const PLUGIN_THRESHOLD = 'jquery.flot.threshold.js';
	const PLUGIN_TIME = 'jquery.flot.time.js';
	
	private static $_plugins = [];
	
	// The files are not web directory accessible, therefore we need
	// to specify the sourcePath property. Notice the @bower alias used.
	public $sourcePath = '@bower/flot';
	
	
	public $js = [];
	
	
	public $depends = [
		'yii\web\JqueryAsset',
		'conquer\excanvas\ExcanvasAsset',
	];
	
	/**
	 * Initializes the bundle.
	 * If you override this method, make sure you call the parent implementation in the last.
	 */
	public function init()
	{
		parent::init();
		$this->js = self::$_plugins;
		array_unshift($this->js, 'jquery.flot.js');
	}
	
	
	/**
	 * Registers this asset bundle with a view.
	 * @param View $view the view to be registered with
	 * @return static the registered asset bundle instance
	 */
	public static function register($view, $plugins = [])
	{
	    if (!empty($plugins)) {
			self::$_plugins = array_unique(ArrayHelper::merge(self::$_plugins, $plugins));
	    }
		return $view->registerAssetBundle(get_called_class());
	}
	
}