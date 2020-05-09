<?php

namespace Angeldm\Debug\Model\Serializer;

use Angeldm\Debug\Api\Data\ProfileInterface;

class ProfileSerializer
{
    /**
     * @var \Angeldm\Debug\Serializer\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Angeldm\Debug\Model\Serializer\CollectorSerializer
     */
    private $collectorSerializer;

    /**
     * @var \Angeldm\Debug\Model\ProfileFactory
     */
    private $profileFactory;

    public function __construct(
        \Angeldm\Debug\Serializer\SerializerInterface $serializer,
        \Angeldm\Debug\Model\Serializer\CollectorSerializer $collectorSerializer,
        \Angeldm\Debug\Model\ProfileFactory $profileFactory
    ) {
        $this->serializer = $serializer;
        $this->collectorSerializer = $collectorSerializer;
        $this->profileFactory = $profileFactory;
    }

    public function serialize(ProfileInterface $profile): string
    {
        return $this->serializer->serialize(array_merge(
            $profile->getData(),
            ['collectors' => $this->collectorSerializer->serialize($profile->getCollectors())]
        ));
    }

    public function unserialize(string $data): ProfileInterface
    {
        $profileData = $this->serializer->unserialize($data);
        $collectors = $this->collectorSerializer->unserialize($profileData['collectors']);
        unset($profileData['collectors']);

        /** @var \Angeldm\Debug\Model\Profile $profile */
        $profile = $this->profileFactory->create(['token' => $profileData['token']])->setData($profileData);
        $profile->setCollectors($collectors);

        return $profile;
    }
}
