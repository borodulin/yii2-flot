<?php
/**
 * @link https://github.com/borodulin/yii2-flot
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-flot/blob/master/LICENSE.md
 */

namespace conquer\flot;

use yii\base\InvalidArgumentException;
use yii\web\AssetBundle;

/**
 * Flot and its plugins
 *
 * ```php
 * FlotAsset::register($view, [FlotAsset::PLUGIN_TIME, FlotAsset::PLUGIN_PIE]);
 * ```
 *
 * @link https://www.flotcharts.org
 * @author Andrey Borodulin
 */
class FlotAsset extends AssetBundle
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

    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/flot';

    /**
     * @inheritdoc
     */
    public $js = ['jquery.flot.js'];

    /**
     * @inheritdoc
     */
    public $publishOptions = [
        'only' => ['jquery.*.js'],
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * Registers the bundle with the view and adds the plugins to it.
     * Plugins requested by every widget on the page are loaded.
     *
     * @param \yii\web\View $view
     * @param string[] $plugins plugin files, see the PLUGIN_* constants
     * @return static the registered asset bundle instance
     * @throws InvalidArgumentException for an unknown plugin
     */
    public static function register($view, $plugins = [])
    {
        /* @var $bundle static */
        $bundle = $view->registerAssetBundle(get_called_class());
        $bundle->addPlugins((array)$plugins);
        return $bundle;
    }

    /**
     * @param string[] $plugins plugin files, see the PLUGIN_* constants
     * @throws InvalidArgumentException for an unknown plugin
     */
    public function addPlugins(array $plugins)
    {
        $available = static::availablePlugins();
        foreach ($plugins as $plugin) {
            if (!in_array($plugin, $available, true)) {
                throw new InvalidArgumentException("Unknown Flot plugin \"$plugin\". Available plugins: "
                    . implode(', ', $available) . '.');
            }
            if (!in_array($plugin, $this->js, true)) {
                $this->js[] = $plugin;
            }
        }
    }

    /**
     * @return string[] plugin files (values of the PLUGIN_* constants)
     */
    public static function availablePlugins()
    {
        $plugins = [];
        foreach ((new \ReflectionClass(get_called_class()))->getConstants() as $name => $value) {
            if (strncmp($name, 'PLUGIN_', 7) === 0) {
                $plugins[] = $value;
            }
        }
        return $plugins;
    }
}
