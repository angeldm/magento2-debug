<?php

namespace Angeldm\Debug\Serializer;

interface SerializerInterface
{
    public function serialize($data): string;

    public function unserialize(string $data);
}
