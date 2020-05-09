<?php

namespace Angeldm\Debug\Test\Unit\Model\ValueObject;

use Angeldm\Debug\Model\ValueObject\Plugin;
use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    public function testObject()
    {
        $class = 'class';
        $name = 'name';
        $sortOrder = 1;
        $method = 'method';
        $type = 'type';

        $plugin = new Plugin($class, $name, $sortOrder, $method, $type);

        $this->assertEquals($class, $plugin->getClass());
        $this->assertEquals($name, $plugin->getName());
        $this->assertEquals($sortOrder, $plugin->getSortOrder());
        $this->assertEquals($method, $plugin->getMethod());
        $this->assertEquals($type, $plugin->getType());
    }
}
