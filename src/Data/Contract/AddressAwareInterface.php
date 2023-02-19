<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    MIT
 */

declare(strict_types=1);

namespace Lyrasoft\ShopGo\Data\Contract;

/**
 * Interface AddressAwareInterface
 */
interface AddressAwareInterface
{
    /**
     * @return string
     */
    public function getFirstname(): string;

    /**
     * @return string
     */
    public function getLastname(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @return string
     */
    public function getPhone(): string;

    /**
     * @return string
     */
    public function getMobile(): string;

    /**
     * @return int
     */
    public function getAddressId(): int;

    /**
     * @return string
     */
    public function getCompany(): string;

    /**
     * @return string
     */
    public function getVat(): string;

    /**
     * @return string
     */
    public function getCountry(): string;

    /**
     * @return string
     */
    public function getState(): string;

    /**
     * @return string
     */
    public function getCity(): string;

    /**
     * @return string
     */
    public function getAddress1(): string;

    /**
     * @return string
     */
    public function getAddress2(): string;

    /**
     * @return int
     */
    public function getLocationId(): int;
}
