<?php

/**
 * Part of shopgo project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Lyraoft\ShopGo\Data;

use Lyraoft\ShopGo\Data\Contract\AddressAwareInterface;
use Windwalker\Data\ValueObject;

/**
 * The PaymentData class.
 */
class ShippingData extends ValueObject implements AddressAwareInterface
{
    use AddressAwaitTrait;

    public string $note = '';

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param  string  $note
     *
     * @return  static  Return self to support chaining.
     */
    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }
}
