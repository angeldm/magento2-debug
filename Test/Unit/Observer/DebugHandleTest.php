<?php

namespace Angeldm\Debug\Test\Unit\Observer;

use Angeldm\Debug\Model\Profiler;
use Angeldm\Debug\Observer\DebugHandle;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class DebugHandleTest extends TestCase
{
    private $layoutMock;

    private $updateMock;

    private $configMock;

    private $observerMock;

    private $observer;

    protected function setUp()
    {
        $this->layoutMock = $this->getMockForAbstractClass(\Magento\Framework\View\LayoutInterface::class);

        $this->updateMock = $this->getMockForAbstractClass(\Magento\Framework\View\Layout\ProcessorInterface::class);

        $this->configMock = $this->getMockBuilder(\Angeldm\Debug\Helper\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->observerMock = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->setMethods(['getLayout', 'getFullActionName'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->observer = (new ObjectManager($this))->getObject(DebugHandle::class, [
            'config' => $this->configMock,
        ]);
    }

    public function testExecute()
    {
        $this->configMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->observerMock->expects($this->once())
            ->method('getFullActionName')
            ->willReturn(Profiler::TOOLBAR_FULL_ACTION_NAME);

        $this->observerMock->expects($this->exactly(2))->method('getLayout')->willReturn($this->layoutMock);
        $this->layoutMock->expects($this->exactly(2))->method('getUpdate')->willReturn($this->updateMock);
        $this->updateMock->expects($this->once())->method('addHandle');
        $this->updateMock->expects($this->once())->method('removeHandle');

        $this->observer->execute($this->observerMock);
    }
}
