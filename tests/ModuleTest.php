<?php
namespace yiiunit\ticket;

use PHPUnit\Framework\TestCase;
use Yii;
use aminkt\ticket\Ticket;

class ModuleTest extends TestCase
{
    public function testDefaultVersion()
    {
        Yii::$app->extensions['aminkt/yii2-ticket-module'] = [
            'name' => 'aminkt/yii2-ticket-modulei',
            'version' => '1.0.0',
        ];
        $module = new Ticket('ticket');
        $this->assertEquals('1.0.0', $module->getVersion());
    }
}