<?php

namespace conquer\flot\tests;

use conquer\flot\FlotAsset;
use conquer\flot\FlotWidget;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Widget;
use yii\helpers\FileHelper;
use yii\web\Application;
use yii\web\View;

class FlotWidgetTest extends TestCase
{
    private $assetPath;

    protected function setUp(): void
    {
        $this->assetPath = sys_get_temp_dir() . '/flot-test-' . uniqid();
        mkdir($this->assetPath);
        new Application([
            'id' => 'test',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'aliases' => ['@bower' => '@vendor/bower-asset', '@npm' => '@vendor/npm-asset'],
            'components' => [
                'request' => ['cookieValidationKey' => 'test', 'scriptFile' => __DIR__ . '/index.php', 'scriptUrl' => '/index.php'],
                'assetManager' => ['basePath' => $this->assetPath, 'baseUrl' => '/assets'],
                'view' => ['class' => View::class],
            ],
        ]);
        Widget::$counter = 0;
    }

    protected function tearDown(): void
    {
        FileHelper::removeDirectory($this->assetPath);
        Yii::$app = null;
    }

    /**
     * @param array[] $configs widget configurations
     * @return array [widgets html, page html]
     */
    private function render(array ...$configs)
    {
        $widgets = '';
        foreach ($configs as $config) {
            $widgets .= FlotWidget::widget($config);
        }
        $html = Yii::$app->view->renderFile(__DIR__ . '/views/page.php');
        return [$widgets, $html];
    }

    public function testChart()
    {
        list($widget, $html) = $this->render([
            'data' => [['label' => 'Visits', 'data' => [[1, 10], [2, 30]]]],
            'options' => ['series' => ['lines' => ['show' => true]]],
        ]);
        $this->assertSame('<div id="w0"></div>', $widget);
        $this->assertMatchesRegularExpression('#src="/assets/\w+/jquery\.js"#', $html);
        $this->assertMatchesRegularExpression('#src="/assets/\w+/jquery\.flot\.js"#', $html);
        $this->assertStringContainsString('jQuery.plot(jQuery(document.getElementById("w0")), '
            . '[{"label":"Visits","data":[[1,10],[2,30]]}], {"series":{"lines":{"show":true}}});', $html);
        $this->assertStringContainsString(':where([id="w0"]){height:300px}', $html);
    }

    public function testEmptyDataAndOptions()
    {
        list(, $html) = $this->render([]);
        $this->assertStringContainsString('jQuery.plot(jQuery(document.getElementById("w0")), [], {});', $html);
    }

    public function testPluginsOfAllWidgetsAreLoaded()
    {
        list(, $html) = $this->render(
            ['plugins' => [FlotAsset::PLUGIN_TIME]],
            ['plugins' => [FlotAsset::PLUGIN_PIE, FlotAsset::PLUGIN_TIME]]
        );
        $this->assertSame(1, substr_count($html, 'jquery.flot.time.js'));
        $this->assertSame(1, substr_count($html, 'jquery.flot.pie.js'));
        // plugins come after the core
        $this->assertGreaterThan(strpos($html, 'jquery.flot.js'), strpos($html, 'jquery.flot.time.js'));
    }

    public function testPluginsAreNotSharedBetweenApplications()
    {
        $this->render(['plugins' => [FlotAsset::PLUGIN_PIE]]);
        $this->tearDown();
        $this->setUp();
        list(, $html) = $this->render([]);
        $this->assertStringNotContainsString('jquery.flot.pie.js', $html);
    }

    public function testUnknownPlugin()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown Flot plugin "jquery.flot.foo.js". Available plugins: jquery.colorhelpers.js,');
        $this->render(['plugins' => ['jquery.flot.foo.js']]);
    }

    public function testAllPluginsArePublished()
    {
        list(, $html) = $this->render(['plugins' => FlotAsset::availablePlugins()]);
        $this->assertCount(15, FlotAsset::availablePlugins());
        preg_match('#src="/assets/(\w+)/jquery\.flot\.js"#', $html, $m);
        $dir = $this->assetPath . '/' . $m[1];
        foreach (FlotAsset::availablePlugins() as $plugin) {
            $this->assertFileExists("$dir/$plugin");
        }
        // only the scripts are published, not the examples
        $this->assertDirectoryDoesNotExist("$dir/examples");
    }

    public function testIdIsEscaped()
    {
        list($widget, $html) = $this->render(['htmlOptions' => ['id' => "a');alert(1);//</script>"]]);
        $this->assertStringContainsString('id="a&#039;);alert(1);//&lt;/script&gt;"', $widget);
        $u = '\\' . 'u';
        $this->assertStringContainsString('jQuery.plot(jQuery(document.getElementById("a' . $u . '0027);alert(1);\/\/' . $u . '003C\/script' . $u . '003E")), [], {});', $html);
        $this->assertStringNotContainsString("alert(1);//</script>", $html);
    }

    public function testDataIsNotExecuted()
    {
        list(, $html) = $this->render([
            'data' => [['label' => 'js:alert(1)', 'data' => [[1, '</script><script>alert(2)</script>']]]],
            'options' => ['xaxis' => ['tickFormatter' => 'js:function (v) { return v + "%"; }']],
        ]);
        $this->assertStringContainsString('"label":"js:alert(1)"', $html);
        $this->assertStringNotContainsString('<script>alert(2)', $html);
        $this->assertStringContainsString('{"xaxis":{"tickFormatter":function (v) { return v + "%"; }}}', $html);
    }

    public function testDefaultHeight()
    {
        list(, $html) = $this->render(
            ['htmlOptions' => ['id' => 'chart.1"}body{color:red']],
            ['defaultHeight' => null],
            ['defaultHeight' => '20em;}body{color:red']
        );
        $this->assertStringContainsString(':where([id="chart\2e 1\22 \7d body\7b color\3a red"]){height:300px}', $html);
        $this->assertStringNotContainsString('[id="w0"]', $html);
        $this->assertStringContainsString(':where([id="w1"]){height:20embodycolorred}', $html);
        preg_match_all('#<style>(.*?)</style>#s', $html, $styles);
        $this->assertCount(2, $styles[1]);
        $this->assertStringNotContainsString('body{', implode('', $styles[1]));
    }

    public function testBeginEnd()
    {
        ob_start();
        FlotWidget::begin(['tag' => 'section', 'htmlOptions' => ['class' => 'chart']]);
        FlotWidget::end();
        $this->assertSame('<section id="w0" class="chart"></section>', ob_get_clean());
    }

    public function testLabelsAreEncoded()
    {
        $label = '<img src=x onerror=alert(1)>';
        list(, $html) = $this->render(
            ['data' => [['label' => $label, 'data' => [[$label, 1], ['5', 2]]], [[$label, 3]]]],
            ['data' => [['label' => '<b>Bold</b>', 'data' => [[1, 2]]]], 'encodeLabels' => false]
        );
        $u = '\\' . 'u';
        $encoded = '&amp;lt;img src=x onerror=alert(1)&amp;gt;';
        $encoded = str_replace(['&amp;', '&'], ['&', $u . '0026'], $encoded);
        $encoded = '"' . $encoded . '"';
        $this->assertStringContainsString('[{"label":' . $encoded . ',"data":[[' . $encoded . ',1],["5",2]]},[[' . $encoded . ',3]]]', $html);
        $this->assertStringContainsString('[{"label":"' . $u . '003Cb' . $u . '003EBold' . $u . '003C\/b' . $u . '003E"', $html);
    }
}
