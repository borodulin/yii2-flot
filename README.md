# Flot widget for Yii2 framework

[![Latest Version](https://img.shields.io/packagist/v/conquer/flot.svg)](https://packagist.org/packages/conquer/flot)
[![Total Downloads](https://img.shields.io/packagist/dt/conquer/flot.svg)](https://packagist.org/packages/conquer/flot)
[![CI](https://github.com/borodulin/yii2-flot/actions/workflows/ci.yml/badge.svg)](https://github.com/borodulin/yii2-flot/actions/workflows/ci.yml)
[![License](https://img.shields.io/packagist/l/conquer/flot.svg)](LICENSE.md)

## Description

[Flot](https://www.flotcharts.org/) is a JavaScript plotting library for jQuery. This extension provides
a widget and an asset bundle for Flot 0.8.3 and its plugins.

## Requirements

- PHP 7.4 or 8.x
- Yii 2.0
- [asset-packagist](https://asset-packagist.org) repository configured in your application's `composer.json` (standard for Yii2 app templates)

## Installation

Install the extension via [Composer](https://getcomposer.org):

```bash
composer require conquer/flot:^1.2
```

## Usage

```php
use conquer\flot\FlotWidget;

<?= FlotWidget::widget([
    'htmlOptions' => ['class' => 'chart', 'style' => 'height: 250px'],
    'data' => [
        ['label' => 'Unique Visits', 'data' => array_map(function ($v) { return [$v, rand(10, 50)]; }, range(1, 30))],
        ['label' => 'Page Views', 'data' => array_map(function ($v) { return [$v, rand(10, 50)]; }, range(1, 30))],
    ],
    'options' => [
        'series' => [
            'lines' => [
                'show' => true,
                'lineWidth' => 2,
                'fill' => true,
                'fillColor' => ['colors' => [['opacity' => 0.05], ['opacity' => 0.01]]],
            ],
            'points' => ['show' => true],
            'shadowSize' => 2,
        ],
        'grid' => [
            'hoverable' => true,
            'clickable' => true,
            'tickColor' => '#eee',
            'borderWidth' => 0,
        ],
        'colors' => ['#d12610', '#37b7f3', '#52e136'],
        'xaxis' => ['ticks' => 11, 'tickDecimals' => 0],
        'yaxis' => ['ticks' => 11, 'tickDecimals' => 0],
    ],
]) ?>
```

| Property | Default | Description |
|---|---|---|
| `data` | `[]` | [Data series](https://github.com/flot/flot/blob/v0.8.3/API.md#data-format) |
| `options` | `[]` | [Plot options](https://github.com/flot/flot/blob/v0.8.3/API.md#plot-options); strings with the `js:` prefix are JavaScript |
| `plugins` | `[]` | Plugins to load, `FlotAsset::PLUGIN_*` |
| `htmlOptions` | `[]` | HTML attributes of the placeholder |
| `tag` | `'div'` | Placeholder tag |
| `defaultHeight` | `'300px'` | Placeholder height unless set by its `style` or CSS classes; `null` disables it |
| `encodeLabels` | `true` | HTML-encode the series labels and string data values |

Flot cannot draw in a placeholder without height, so the widget sets `defaultHeight` with a zero-specificity
CSS rule: a height from your stylesheet or the `style` attribute takes precedence.

### Plugins

```php
use conquer\flot\FlotAsset;
use yii\web\JsExpression;

<?= FlotWidget::widget([
    'plugins' => [FlotAsset::PLUGIN_TIME],
    'data' => [['label' => 'Orders', 'data' => [[1735689600000, 12], [1735776000000, 18]]]],
    'options' => [
        'xaxis' => ['mode' => 'time', 'timeformat' => '%d.%m'],
        'yaxis' => ['tickFormatter' => new JsExpression('function (v) { return v + " pcs"; }')],
    ],
]) ?>

<?= FlotWidget::widget([
    'plugins' => [FlotAsset::PLUGIN_PIE],
    'data' => [['label' => 'Desktop', 'data' => 70], ['label' => 'Mobile', 'data' => 30]],
    'options' => ['series' => ['pie' => ['show' => true]]],
]) ?>
```

Every widget on the page may request its own plugins, they are all loaded. An unknown plugin name throws
`yii\base\InvalidArgumentException`.

### JavaScript

The plot object is stored in the placeholder data:

```js
var plot = jQuery('#sales-chart').data('plot');
plot.setData(newData);
plot.setupGrid();
plot.draw();
```

## Security

- `data` is encoded as plain JSON: strings with the `js:` prefix are not turned into JavaScript.
  Put callbacks into `options`.
- Flot inserts series labels and the strings of the categories mode into the page as HTML
  (legend, pie labels, axis ticks). The widget HTML-encodes them (`encodeLabels`), so labels taken
  from users cannot inject markup. Set `'encodeLabels' => false` only for trusted data with intended markup.
- `options` are trusted: strings with the `js:` prefix are executed, tick labels (`ticks`) are HTML.
  Do not put user input there unencoded.

## Upgrading to 1.2

- `conquer/excanvas` (Internet Explorer 8 and earlier) is no longer required. If you still need it,
  require it in your application and add `conquer\excanvas\ExcanvasAsset` to the `depends` of your bundle.
- Series labels are HTML-encoded. If you used markup in labels, set `'encodeLabels' => false`.
- The placeholder gets a default height of 300px unless its height is set by CSS.

## License

**conquer/flot** is released under the MIT License. See the bundled [`LICENSE.md`](LICENSE.md) for details.
