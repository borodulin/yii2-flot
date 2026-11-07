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
 * Flot 4 and its plugins
 *
 * Most plugins (time, categories, stack, navigate, selection, legend, hover, axis labels...) are part
 * of the Flot bundle and are always available. The others are loaded on request:
 *
 * ```php
 * FlotAsset::register($view, [FlotAsset::PLUGIN_PIE]);
 * ```
 *
 * @link https://github.com/flot/flot
 * @author Andrey Borodulin
 */
class FlotAsset extends AssetBundle
{
    // plugins loaded on request
    const PLUGIN_CROSSHAIR = 'crosshair';
    const PLUGIN_IMAGE = 'image';
    const PLUGIN_PIE = 'pie';
    const PLUGIN_RESIZE = 'resize';
    const PLUGIN_THRESHOLD = 'threshold';

    // plugins of the Flot bundle, always loaded
    const PLUGIN_AXISLABELS = 'axislabels';
    const PLUGIN_CATEGORIES = 'categories';
    const PLUGIN_COLORHELPERS = 'colorhelpers';
    const PLUGIN_COMPOSEIMAGES = 'composeImages';
    const PLUGIN_ERRORBARS = 'errorbars';
    const PLUGIN_FILLBETWEEN = 'fillbetween';
    const PLUGIN_FLATDATA = 'flatdata';
    const PLUGIN_HOVER = 'hover';
    const PLUGIN_LEGEND = 'legend';
    const PLUGIN_LOGAXIS = 'logaxis';
    const PLUGIN_NAVIGATE = 'navigate';
    const PLUGIN_SELECTION = 'selection';
    const PLUGIN_STACK = 'stack';
    const PLUGIN_SYMBOL = 'symbol';
    const PLUGIN_TIME = 'time';
    const PLUGIN_TOUCH = 'touch';
    const PLUGIN_TOUCHNAVIGATE = 'touchNavigate';

    /**
     * @var string[] files of the plugins that are not in the Flot bundle
     */
    public static $pluginFiles = [
        self::PLUGIN_CROSSHAIR => 'source/jquery.flot.crosshair.js',
        self::PLUGIN_IMAGE => 'source/jquery.flot.image.js',
        self::PLUGIN_PIE => 'source/jquery.flot.pie.js',
        self::PLUGIN_RESIZE => 'source/jquery.flot.resize.js',
        self::PLUGIN_THRESHOLD => 'source/jquery.flot.threshold.js',
    ];

    /**
     * @inheritdoc
     */
    public $sourcePath = '@npm/flot';

    /**
     * @inheritdoc
     */
    public $js = ['dist/es5/jquery.flot.js'];

    /**
     * @inheritdoc
     */
    public $publishOptions = [
        'only' => ['dist/es5/jquery.flot.js', 'source/jquery.flot.*.js'],
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
     * @param string[] $plugins see the PLUGIN_* constants
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
     * @param string[] $plugins see the PLUGIN_* constants. File names of conquer/flot 1.x
     * (`jquery.flot.pie.js`) are accepted too.
     * @throws InvalidArgumentException for an unknown plugin
     */
    public function addPlugins(array $plugins)
    {
        $available = static::availablePlugins();
        foreach ($plugins as $plugin) {
            $name = static::normalizePluginName($plugin);
            if ($name === null) {
                throw new InvalidArgumentException("Unknown Flot plugin \"$plugin\". Available plugins: "
                    . implode(', ', $available) . '.');
            }
            if (isset(static::$pluginFiles[$name]) && !in_array(static::$pluginFiles[$name], $this->js, true)) {
                $this->js[] = static::$pluginFiles[$name];
            }
        }
    }

    /**
     * @return string[] plugin names (values of the PLUGIN_* constants)
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

    /**
     * @param string $plugin plugin name or 1.x file name
     * @return string|null plugin name, null if unknown
     */
    protected static function normalizePluginName($plugin)
    {
        $available = static::availablePlugins();
        if (in_array($plugin, $available, true)) {
            return $plugin;
        }
        if (is_string($plugin) && preg_match('/^jquery\.(?:flot\.)?(\w+)\.js$/', $plugin, $m)) {
            foreach ($available as $name) {
                if (strcasecmp($name, $m[1]) === 0) {
                    return $name;
                }
            }
        }
        return null;
    }
}
