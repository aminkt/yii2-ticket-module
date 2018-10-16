<?php
namespace yiiunit\gii;
use Yii;
use yii\gii\Module;
class ModuleTest extends TestCase
{
    public function testDefaultVersion()
    {
        Yii::$app->extensions['aminkt/yii2-ticket-module'] = [
            'name' => 'aminkt/yii2-ticket-modulei',
            'version' => '1.0.0',
        ];
        $module = new Module('ticket');
        $this->assertEquals('1.0.0', $module->getVersion());
    }
}