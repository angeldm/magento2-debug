<?php

namespace Angeldm\Debug\Api;

use Angeldm\Debug\Api\Data\ProfileInterface;
use Angeldm\Debug\Model\Profile\Criteria;

interface ProfileRepositoryInterface
{
    /**
     * @param \Angeldm\Debug\Api\Data\ProfileInterface $profile
     * @return \Angeldm\Debug\Api\ProfileRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ProfileInterface $profile): ProfileRepositoryInterface;

    /**
     * @param string $token
     * @return \Angeldm\Debug\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(string $token): ProfileInterface;

    /**
     * @param \Angeldm\Debug\Api\Data\ProfileInterface $profile
     * @return \Angeldm\Debug\Api\ProfileRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ProfileInterface $profile): ProfileRepositoryInterface;

    /**
     * @param string $token
     * @return \Angeldm\Debug\Api\ProfileRepositoryInterface
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(string $token): ProfileRepositoryInterface;

    public function find(Criteria $criteria): array;

    /**
     * @return \Angeldm\Debug\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function findLatest(): ProfileInterface;
}
