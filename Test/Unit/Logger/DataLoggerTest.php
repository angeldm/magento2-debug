<?php

namespace Angeldm\Debug\Test\Unit\Logger;

use Angeldm\Debug\Logger\DataLogger;
use Angeldm\Debug\Logger\LoggableInterface;
use PHPUnit\Framework\TestCase;

class DataLoggerTest extends TestCase
{
    private $logger;

    private $loggableMock;

    protected function setUp()
    {
        parent::setUp();

        $this->loggableMock = $this->getMockForAbstractClass(LoggableInterface::class);
        $this->logger = new DataLogger();
    }

    public function testGetLogs()
    {
        $this->assertEquals([], $this->logger->getLogs());
    }

    public function testLog()
    {
        $this->loggableMock->expects($this->once())->method('getId')->willReturn('ID');
        $this->assertInstanceOf(DataLogger::class, $this->logger->log($this->loggableMock));
        $this->assertEquals(['ID' => $this->loggableMock], $this->logger->getLogs());
    }
}
