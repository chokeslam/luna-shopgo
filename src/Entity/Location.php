<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    MIT
 */

declare(strict_types=1);

namespace Lyrasoft\ShopGo\Entity;

use DateTimeInterface;
use Lyrasoft\ShopGo\Enum\LocationType;
use Lyrasoft\Luna\Attributes\Author;
use Lyrasoft\Luna\Attributes\Modifier;
use Unicorn\Enum\BasicState;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\ORM\Attributes\AutoIncrement;
use Windwalker\ORM\Attributes\Cast;
use Windwalker\ORM\Attributes\CastNullable;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\CreatedTime;
use Windwalker\ORM\Attributes\CurrentTime;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\NestedSet;
use Windwalker\ORM\Attributes\PK;
use Windwalker\ORM\Cast\JsonCast;
use Windwalker\ORM\Metadata\EntityMetadata;
use Windwalker\ORM\Nested\NestedEntityInterface;
use Windwalker\ORM\Nested\NestedEntityTrait;

/**
 * The Location class.
 */
#[NestedSet('locations', 'location')]
class Location implements NestedEntityInterface
{
    use NestedEntityTrait;

    #[Column('id'), PK, AutoIncrement]
    protected ?int $id = null;

    #[Column('category_id')]
    protected int $categoryId = 0;

    #[Column('type')]
    #[Cast(LocationType::class)]
    protected LocationType $type;

    #[Column('region')]
    protected string $region = '';

    #[Column('subregion')]
    protected string $subregion = '';

    #[Column('title')]
    protected string $title = '';

    #[Column('code')]
    protected string $code = '';

    #[Column('code3')]
    protected string $code3 = '';

    #[Column('address_format')]
    protected string $addressFormat = '';

    #[Column('postcode_required')]
    #[Cast('bool', 'int')]
    protected bool $postcodeRequired = false;

    #[Column('native')]
    protected string $native = '';

    #[Column('has_states')]
    #[Cast('bool', 'int')]
    protected bool $hasStates = false;

    #[Column('call_prefix')]
    protected string $callPrefix = '';

    #[Column('created')]
    #[CastNullable(Chronos::class)]
    #[CreatedTime]
    protected ?Chronos $created = null;

    #[Column('modified')]
    #[CastNullable(Chronos::class)]
    #[CurrentTime]
    protected ?Chronos $modified = null;

    #[Column('created_by')]
    #[Author]
    protected int $createdBy = 0;

    #[Column('modified_by')]
    #[Modifier]
    protected int $modifiedBy = 0;

    #[Column('state')]
    #[Cast('int')]
    #[Cast(BasicState::class)]
    protected BasicState $state;

    #[Column('params')]
    #[Cast(JsonCast::class)]
    protected array $params = [];

    #[EntitySetup]
    public static function setup(EntityMetadata $metadata): void
    {
        //
    }

    public function getPrimaryKeyValue(): ?int
    {
        return $this->getId();
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

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): static
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getSubregion(): string
    {
        return $this->subregion;
    }

    public function setSubregion(string $subregion): static
    {
        $this->subregion = $subregion;

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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getCode3(): string
    {
        return $this->code3;
    }

    public function setCode3(string $code3): static
    {
        $this->code3 = $code3;

        return $this;
    }

    public function getAddressFormat(): string
    {
        return $this->addressFormat;
    }

    public function setAddressFormat(string $addressFormat): static
    {
        $this->addressFormat = $addressFormat;

        return $this;
    }

    public function isPostcodeRequired(): bool
    {
        return $this->postcodeRequired;
    }

    public function setPostcodeRequired(bool $postcodeRequired): static
    {
        $this->postcodeRequired = $postcodeRequired;

        return $this;
    }

    public function isHasStates(): bool
    {
        return $this->hasStates;
    }

    public function setHasStates(bool $hasStates): static
    {
        $this->hasStates = $hasStates;

        return $this;
    }

    public function getCallPrefix(): string
    {
        return $this->callPrefix;
    }

    public function setCallPrefix(string $callPrefix): static
    {
        $this->callPrefix = $callPrefix;

        return $this;
    }

    public function getCreated(): ?Chronos
    {
        return $this->created;
    }

    public function setCreated(DateTimeInterface|string|null $created): static
    {
        $this->created = Chronos::wrapOrNull($created);

        return $this;
    }

    public function getModified(): ?Chronos
    {
        return $this->modified;
    }

    public function setModified(DateTimeInterface|string|null $modified): static
    {
        $this->modified = Chronos::wrapOrNull($modified);

        return $this;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(int $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getModifiedBy(): int
    {
        return $this->modifiedBy;
    }

    public function setModifiedBy(int $modifiedBy): static
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): static
    {
        $this->params = $params;

        return $this;
    }

    public function getType(): LocationType
    {
        return $this->type;
    }

    public function setType(string|LocationType $type): static
    {
        $this->type = LocationType::wrap($type);

        return $this;
    }

    public function getNative(): string
    {
        return $this->native;
    }

    public function setNative(string $native): static
    {
        $this->native = $native;

        return $this;
    }

    public function getState(): BasicState
    {
        return $this->state;
    }

    public function setState(int|BasicState $state): static
    {
        $this->state = BasicState::wrap($state);

        return $this;
    }
}
