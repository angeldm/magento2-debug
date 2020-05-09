<?php

namespace Angeldm\Debug\Model\Storage;

use Angeldm\Debug\Api\Data\ProfileInterface;

class ProfileMemoryStorage
{
    /**
     * @var \Angeldm\Debug\Model\Profile
     */
    private $profile;

    public function read(): ProfileInterface
    {
        return $this->profile;
    }

    public function write(ProfileInterface $profile)
    {
        $this->profile = $profile;
    }
}
