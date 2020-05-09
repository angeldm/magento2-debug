<?php

namespace Angeldm\Debug\Model;

class DataCollector
{
    /**
     * @var array
     */
    private $data = [];

    public function setData(array $data): DataCollector
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function getData(string $key = '')
    {
        if ($key) {
            return $this->data[$key] ?? null;
        }

        return $this->data;
    }

    public function addData(string $key, $value): DataCollector
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function appendData(string $key, $value): DataCollector
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = [];
        }
        $this->data[$key][] = $value;

        return $this;
    }

    public function removeData(string $key): DataCollector
    {
        unset($this->data[$key]);

        return $this;
    }
}
