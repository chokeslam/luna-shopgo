<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Entity;

use Windwalker\ORM\Attributes\AutoIncrement;
use Windwalker\ORM\Attributes\Cast;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\PK;
use Windwalker\ORM\Attributes\Table;
use Windwalker\ORM\EntityInterface;
use Windwalker\ORM\EntityTrait;
use Windwalker\ORM\Metadata\EntityMetadata;

/**
 * The OrderState class.
 */
#[Table('order_states', 'order_state')]
class OrderState implements EntityInterface
{
    use EntityTrait;

    #[Column('attach_invoice')]
    #[Cast('bool', 'int')]
    protected bool $attachInvoice = false;

    #[Column('cancel')]
    #[Cast('bool', 'int')]
    protected bool $cancel = false;

    #[Column('color')]
    protected string $color = '';

    #[Column('default')]
    #[Cast('bool', 'int')]
    protected bool $default = false;

    #[Column('done')]
    #[Cast('bool', 'int')]
    protected bool $done = false;

    #[Column('id'), PK, AutoIncrement]
    protected ?int $id = null;

    #[Column('image')]
    protected string $image = '';

    #[Column('notice')]
    #[Cast('bool', 'int')]
    protected bool $notice = false;

    #[Column('paid')]
    #[Cast('bool', 'int')]
    protected bool $paid = false;

    #[Column('returned')]
    #[Cast('bool', 'int')]
    protected bool $returned = false;

    #[Column('rollback')]
    #[Cast('bool', 'int')]
    protected bool $rollback = false;

    #[Column('shipped')]
    #[Cast('bool', 'int')]
    protected bool $shipped = false;

    #[Column('title')]
    protected string $title = '';

    #[EntitySetup]
    public static function setup(EntityMetadata $metadata): void
    {
        //
    }

    public function isAttachInvoice(): bool
    {
        return $this->attachInvoice;
    }

    public function setAttachInvoice(bool $attachInvoice): static
    {
        $this->attachInvoice = $attachInvoice;

        return $this;
    }

    public function isCancel(): bool
    {
        return $this->cancel;
    }

    public function setCancel(bool $cancel): static
    {
        $this->cancel = $cancel;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): static
    {
        $this->default = $default;

        return $this;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function setDone(bool $done): static
    {
        $this->done = $done;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isNotice(): bool
    {
        return $this->notice;
    }

    public function setNotice(bool $notice): static
    {
        $this->notice = $notice;

        return $this;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): static
    {
        $this->paid = $paid;

        return $this;
    }

    public function isReturned(): bool
    {
        return $this->returned;
    }

    public function setReturned(bool $returned): static
    {
        $this->returned = $returned;

        return $this;
    }

    public function isRollback(): bool
    {
        return $this->rollback;
    }

    public function setRollback(bool $rollback): static
    {
        $this->rollback = $rollback;

        return $this;
    }

    public function isShipped(): bool
    {
        return $this->shipped;
    }

    public function setShipped(bool $shipped): static
    {
        $this->shipped = $shipped;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
