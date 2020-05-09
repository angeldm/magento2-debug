<?php

namespace Angeldm\Debug\Model\Collector;

interface LateCollectorInterface
{
    public function lateCollect(): LateCollectorInterface;
}
