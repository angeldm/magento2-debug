<?php

namespace Angeldm\Debug\Model\Collector;

use Angeldm\Debug\Logger\DataLogger;
use Angeldm\Debug\Logger\LoggableInterface;

interface LoggerCollectorInterface
{
    public function log(LoggableInterface $value): LoggerCollectorInterface;
}
