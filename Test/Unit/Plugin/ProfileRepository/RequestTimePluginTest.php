<?php

namespace Angeldm\Debug\Test\Unit\Plugin\ProfileRepository;

use Angeldm\Debug\Model\Collector\TimeCollector;
use Angeldm\Debug\Plugin\ProfileRepository\RequestTimePlugin;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

class RequestTimePluginTest extends TestCase
{
    private $timeCollectorMock;

    private $profileMock;

    private $subjectMock;

    private $plugin;

    protected function setUp()
    {
        $this->timeCollectorMock = $this->getMockBuilder(\Angeldm\Debug\Model\Collector\TimeCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->profileMock = $this->getMockForAbstractClass(\Angeldm\Debug\Api\Data\ProfileInterface::class);

        $this->subjectMock = $this->getMockForAbstractClass(\Angeldm\Debug\Api\ProfileRepositoryInterface::class);

        $this->plugin = (new ObjectManager($this))->getObject(RequestTimePlugin::class);
    }

    public function testBeforeSave()
    {
        $this->profileMock->expects($this->once())
            ->method('getCollector')
            ->with(TimeCollector::NAME)
            ->willReturn($this->timeCollectorMock);

        $this->timeCollectorMock->expects($this->once())->method('getDuration')->willReturn(1);
        $this->profileMock->expects($this->once())->method('setRequestTime')->with(1);

        $this->assertEquals([$this->profileMock], $this->plugin->beforeSave($this->subjectMock, $this->profileMock));
    }

    public function testBeforeSaveException()
    {
        $this->profileMock->expects($this->once())
            ->method('getCollector')
            ->willThrowException(new \InvalidArgumentException());

        $this->profileMock->expects($this->never())->method('setRequestTime');

        $this->assertEquals([$this->profileMock], $this->plugin->beforeSave($this->subjectMock, $this->profileMock));
    }
}
