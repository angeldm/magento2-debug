<?php

namespace Angeldm\Debug\Test\Unit\Plugin\ErrorHandler;

use Angeldm\Debug\Model\Config\Source\ErrorHandler;
use Angeldm\Debug\Plugin\ErrorHandler\WhoopsPlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class WhoopsPluginTest extends TestCase
{
    public function testBeforeCatchException()
    {
        $subjectMock = $this->getMockBuilder(\Magento\Framework\App\Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $bootstrapMock = $this->getMockBuilder(\Magento\Framework\App\Bootstrap::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configMock = $this->getMockBuilder(\Angeldm\Debug\Helper\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $whoopsFactoryMock = $this->getMockBuilder(\Whoops\RunFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $whoopsMock = $this->getMockForAbstractClass(\Whoops\RunInterface::class);

        $prettyPageHandlerFactoryMock = $this->getMockBuilder(\Whoops\Handler\PrettyPageHandlerFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $prettyPageHandlerMock = $this->getMockBuilder(\Whoops\Handler\PrettyPageHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exception = new \Exception();

        $configMock->expects($this->once())->method('getErrorHandler')->willReturn(ErrorHandler::WHOOPS);
        $whoopsFactoryMock->expects($this->once())->method('create')->willReturn($whoopsMock);
        $prettyPageHandlerFactoryMock->expects($this->once())->method('create')->willReturn($prettyPageHandlerMock);
        $whoopsMock->expects($this->once())->method('pushHandler')->with($prettyPageHandlerMock);
        $whoopsMock->expects($this->once())->method('handleException')->with($exception);

        $plugin = (new ObjectManager($this))->getObject(WhoopsPlugin::class, [
            'config' => $configMock,
            'whoopsFactory' => $whoopsFactoryMock,
            'prettyPageHandlerFactory' => $prettyPageHandlerFactoryMock,
        ]);

        $this->assertEquals([
            $bootstrapMock, $exception
        ], $plugin->beforeCatchException($subjectMock, $bootstrapMock, $exception));
    }
}
