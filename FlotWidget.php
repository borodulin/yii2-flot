<?php
/**
 * @link https://github.com/borodulin/yii2-flot
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-flot/blob/master/LICENSE.md
 */

namespace conquer\flot;

use conquer\helpers\Json;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json as DataJson;

/**
 * Flot chart
 *
 * ```php
 * <?= FlotWidget::widget([
 *     'data' => [['label' => 'Visits', 'data' => [[1, 10], [2, 30], [3, 20]]]],
 *     'options' => ['series' => ['lines' => ['show' => true]]],
 * ]) ?>
 * ```
 *
 * The plot object is available in JavaScript as `jQuery('#id').data('plot')`.
 *
 * @link https://github.com/flot/flot
 * @author Andrey Borodulin
 */
class FlotWidget extends Widget
{
    /**
     * Plugins to load, see the FlotAsset::PLUGIN_* constants (most plugins are always loaded)
     * @var string[]
     */
    public $plugins = [];

    /**
     * Placeholder tag
     * @var string
     */
    public $tag = 'div';

    /**
     * HTML attributes of the placeholder
     * @var array
     */
    public $htmlOptions = [];

    /**
     * Flot options. Strings with the "js:" prefix are turned into JavaScript (callbacks such as
     * `tickFormatter`), so do not put data coming from users here.
     * @link https://github.com/flot/flot/blob/master/docs/API.md#plot-options
     * @var array
     */
    public $options = [];

    /**
     * Data series.
     * The data is encoded as plain JSON: strings with the "js:" prefix are NOT turned into JavaScript
     * (put callbacks into $options), and labels are HTML-encoded, see [[encodeLabels]].
     * @link https://github.com/flot/flot/blob/master/docs/API.md#data-format
     * @var array
     */
    public $data = [];

    /**
     * Whether to HTML-encode the series labels: Flot inserts them into the legend and pie labels
     * as markup. Disable it only if the labels are trusted and contain intended markup.
     * Axis tick labels (including the strings of the categories mode) are plain text in Flot 4.
     * @var bool
     */
    public $encodeLabels = true;

    /**
     * Height of the placeholder unless set by its style or CSS classes (Flot cannot draw
     * in a placeholder with zero height). Null disables it.
     * @var string|null
     */
    public $defaultHeight = '300px';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty($this->htmlOptions['id'])) {
            $this->htmlOptions['id'] = $this->getId();
        }
        echo Html::beginTag($this->tag, $this->htmlOptions);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        FlotAsset::register($view, (array)$this->plugins);

        $id = (string)$this->htmlOptions['id'];
        if ($this->defaultHeight !== null) {
            // :where() has no specificity, so any height from CSS classes or the style attribute wins
            $cssId = preg_replace_callback('/[^A-Za-z0-9_-]/u', function ($m) {
                return '\\' . dechex(mb_ord($m[0], 'UTF-8')) . ' ';
            }, $id);
            $view->registerCss(':where([id="' . $cssId . '"]){height:'
                . preg_replace('/[^\w.%() -]/', '', $this->defaultHeight) . '}');
        }

        $data = empty($this->data) ? [] : (array)$this->data;
        if ($this->encodeLabels) {
            $data = array_map([$this, 'encodeSeries'], $data);
        }
        $data = DataJson::htmlEncode($data);
        $options = empty($this->options) ? '{}' : Json::encode($this->options);
        $view->registerJs('jQuery.plot(jQuery(document.getElementById(' . DataJson::htmlEncode($id) . ')), '
            . $data . ', ' . $options . ');');

        return Html::endTag($this->tag);
    }

    /**
     * HTML-encodes the label of a series
     * @param mixed $series a series options array or an array of points
     * @return mixed
     */
    protected function encodeSeries($series)
    {
        if (is_array($series) && isset($series['label']) && is_string($series['label'])) {
            $series['label'] = Html::encode($series['label']);
        }
        return $series;
    }
}
