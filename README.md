# Flot widget for Yii2 framework

[![Latest Version](https://img.shields.io/packagist/v/conquer/flot.svg)](https://packagist.org/packages/conquer/flot)
[![Total Downloads](https://img.shields.io/packagist/dt/conquer/flot.svg)](https://packagist.org/packages/conquer/flot)
[![License](https://img.shields.io/packagist/l/conquer/flot.svg)](LICENSE.md)

## Description

Flot is a pure JavaScript plotting library for jQuery, with a focus on simple usage, attractive looks and interactive features.
Works with Internet Explorer 6+, Chrome, Firefox 2+, Safari 3+ and Opera 9.5+
For more information please visit [Flot](http://www.flotcharts.org/) 

## Requirements

- PHP 7.4 or 8.x
- Yii 2.0
- [asset-packagist](https://asset-packagist.org) repository configured in your application's `composer.json` (standard for Yii2 app templates)

## Installation

Install the extension via [Composer](https://getcomposer.org):

```bash
composer require conquer/flot:^1.0
```

## Usage

```php
use conquer\flot\FlotWidget;

<= FlotWidget::widget([
    'htmlOptions'=>['class'=>'chart'],
    'data'=>[
        ['labels'=>'Unique Visits', 'data'=>array_map(function($v){return [$v, rand(10,50)];}, range(1, 30))],
        ['labels'=>'Page Views', 'data'=>array_map(function($v){return [$v, rand(10,50)];}, range(1, 30))],
    ],
    'options'=>[
        'series'=>[
            'lines'=> [
                'show'=> true,
                'lineWidth'=> 2,
                'fill'=> true,
                'fillColor'=> ['colors'=> [
                            ['opacity'=>0.05],
                            ['opacity'=>0.01],
                ]],
            ],
            'points'=>['show'=>true],
            'shadowSize'=> 2,
        ],
        'grid'=> [
            'hoverable'=> true,
            'clickable'=> true,
            'tickColor'=> "#eee",
            'borderWidth'=> 0,
        ],
        'colors'=> ["#d12610", "#37b7f3", "#52e136"],
        'xaxis' => [
            'ticks'=> 11,
            'tickDecimals'=> 0,
        ],
        'yaxis'=> [
            'ticks'=> 11,
            'tickDecimals'=> 0,
        ]       
    ],
]); =>
```

## License

**conquer/flot** is released under the MIT License. See the bundled [`LICENSE.md`](LICENSE.md) for details.
