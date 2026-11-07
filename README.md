# Flot widget for Yii2 framework

[![Latest Version](https://img.shields.io/packagist/v/conquer/flot.svg)](https://packagist.org/packages/conquer/flot)
[![Total Downloads](https://img.shields.io/packagist/dt/conquer/flot.svg)](https://packagist.org/packages/conquer/flot)
[![CI](https://github.com/borodulin/yii2-flot/actions/workflows/ci.yml/badge.svg)](https://github.com/borodulin/yii2-flot/actions/workflows/ci.yml)
[![License](https://img.shields.io/packagist/l/conquer/flot.svg)](LICENSE.md)

## Description

[Flot](https://github.com/flot/flot) is a JavaScript plotting library for jQuery. This extension provides
a widget and an asset bundle for Flot 4 and its plugins.

| conquer/flot | Flot | Branch |
|---|---|---|
| 2.x | 4.2 | `master` |
| 1.x | 0.8.3 | [`1.x`](https://github.com/borodulin/yii2-flot/tree/1.x) |

## Requirements

- PHP 7.4 or 8.x
- Yii 2.0
- [asset-packagist](https://asset-packagist.org) repository configured in your application's `composer.json` (standard for Yii2 app templates)

## Installation

Install the extension via [Composer](https://getcomposer.org):

```bash
composer require conquer/flot:^2.0
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
| `data` | `[]` | [Data series](https://github.com/flot/flot/blob/master/docs/API.md#data-format) |
| `options` | `[]` | [Plot options](https://github.com/flot/flot/blob/master/docs/API.md#plot-options); strings with the `js:` prefix are JavaScript |
| `plugins` | `[]` | Plugins to load, `FlotAsset::PLUGIN_*` (most plugins are always loaded) |
| `htmlOptions` | `[]` | HTML attributes of the placeholder |
| `tag` | `'div'` | Placeholder tag |
| `defaultHeight` | `'300px'` | Placeholder height unless set by its `style` or CSS classes; `null` disables it |
| `encodeLabels` | `true` | HTML-encode the series labels |

Flot cannot draw in a placeholder without height, so the widget sets `defaultHeight` with a zero-specificity
CSS rule: a height from your stylesheet or the `style` attribute takes precedence.

### Plugins

```php
use conquer\flot\FlotAsset;
use yii\web\JsExpression;

<?= FlotWidget::widget([
    'plugins' => [FlotAsset::PLUGIN_TIME],
    'data' => [['label' => 'Orders', 'data' => [[1735689600, 12], [1735776000, 18]]]],
    'options' => [
        // Flot 4 expects seconds by default; use 'timeBase' => 'milliseconds' for JavaScript timestamps
        'xaxis' => ['mode' => 'time', 'timeformat' => '%d.%m'],
        'yaxis' => ['tickFormatter' => new JsExpression('function (v) { return v + " pcs"; }')],
    ],
]) ?>

<?= FlotWidget::widget([
    'plugins' => [FlotAsset::PLUGIN_PIE],
    'data' => [['label' => 'Desktop', 'data' => 70], ['label' => 'Mobile', 'data' => 30]],
    'options' => ['series' => ['pie' => ['show' => true]], 'legend' => ['show' => true]],
]) ?>
```

The Flot bundle includes the time, categories, stack, fillbetween, errorbars, symbol, navigate, selection,
hover, legend, axislabels, logaxis, flatdata, touch and composeImages plugins: they are always available,
their `PLUGIN_*` constants are accepted and load nothing. The pie, crosshair, image, resize and threshold
plugins are loaded on request. Every widget on the page may request its own plugins, they are all loaded.
An unknown plugin name throws `yii\base\InvalidArgumentException`.

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
- Flot inserts series labels into the legend and pie labels as markup. The widget HTML-encodes them
  (`encodeLabels`), so labels taken from users cannot inject markup. Set `'encodeLabels' => false`
  only for trusted labels with intended markup. Axis tick labels are plain text.
- `options` are trusted: strings with the `js:` prefix are executed, and `legend.labelFormatter`
  or the pie label `formatter` return markup. Do not put user input there.

## Upgrading from 1.x

2.0 uses Flot 4 (`npm-asset/flot`) instead of Flot 0.8.3 (`bower-asset/flot`). See the
[Flot changelog](https://github.com/flot/flot/blob/master/CHANGELOG.md) for the details; the most visible changes:

- **Time axis values are seconds by default.** Divide JavaScript timestamps by 1000 or set
  `'xaxis' => ['mode' => 'time', 'timeBase' => 'milliseconds']`.
- **The legend is hidden by default** and is drawn as SVG. Set `'legend' => ['show' => true]`;
  `legend.labelBoxBorderColor`, `backgroundColor` and `backgroundOpacity` are not supported.
- Tick labels are SVG `<text>` elements, not HTML `<div>`s: CSS rules for `.tickLabel` divs need updating.
- `grid.hoverable` events work as before (the hover plugin is in the bundle).

Plugins:

- `FlotAsset::PLUGIN_*` values are plugin names now (`'pie'`), file names of 1.x (`'jquery.flot.pie.js'`)
  are still accepted.
- `PLUGIN_CANVAS` is removed: Flot 4 has no canvas text plugin.
- New constants for the plugins of the Flot bundle: `PLUGIN_AXISLABELS`, `PLUGIN_LOGAXIS`, `PLUGIN_HOVER`,
  `PLUGIN_LEGEND`, `PLUGIN_TOUCH`, `PLUGIN_TOUCHNAVIGATE`, `PLUGIN_COMPOSEIMAGES`, `PLUGIN_FLATDATA`.

Strings of the categories mode are no longer HTML-encoded: Flot 4 draws tick labels as plain text.

Need Flot 0.8.3? Stay on `conquer/flot:^1.2`, maintained in the [`1.x`](https://github.com/borodulin/yii2-flot/tree/1.x) branch.

## License

**conquer/flot** is released under the MIT License. See the bundled [`LICENSE.md`](LICENSE.md) for details.
