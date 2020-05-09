<?php

namespace Angeldm\Debug\Plugin\ProfileRepository;

use Angeldm\Debug\Api\Data\ProfileInterface;
use Angeldm\Debug\Api\ProfileRepositoryInterface;
use Angeldm\Debug\Model\Collector\TimeCollector;

class RequestTimePlugin
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \Angeldm\Debug\Api\ProfileRepositoryInterface $subject
     * @param \Angeldm\Debug\Api\Data\ProfileInterface      $profile
     * @return array
     */
    public function beforeSave(ProfileRepositoryInterface $subject, ProfileInterface $profile)
    {
        try {
            /** @var \Angeldm\Debug\Model\Collector\TimeCollector $timeCollector */
            $timeCollector = $profile->getCollector(TimeCollector::NAME);
        } catch (\InvalidArgumentException $e) {
            return [$profile];
        }

        $profile->setRequestTime($timeCollector->getDuration());

        return [$profile];
    }
}
