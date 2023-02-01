<?php

/**
 * Part of starter project.
 *
 * @copyright    Copyright (C) 2021 __ORGANIZATION__.
 * @license        __LICENSE__
 */

declare(strict_types=1);

namespace Lyrasoft\ShopGo\Repository;

use Lyrasoft\ShopGo\Entity\ProductTab;
use Lyrasoft\ShopGo\Entity\ShopCategoryMap;
use Lyrasoft\Luna\Entity\Article;
use Lyrasoft\Luna\Entity\Page;
use Unicorn\Attributes\ConfigureAction;
use Unicorn\Attributes\Repository;
use Unicorn\Repository\Actions\BatchAction;
use Unicorn\Repository\Actions\ReorderAction;
use Unicorn\Repository\Actions\SaveAction;
use Unicorn\Repository\ListRepositoryInterface;
use Unicorn\Repository\ListRepositoryTrait;
use Unicorn\Repository\ManageRepositoryInterface;
use Unicorn\Repository\ManageRepositoryTrait;
use Unicorn\Selector\ListSelector;
use Windwalker\Query\Query;

/**
 * The ProductTabRepository class.
 */
#[Repository(entityClass: ProductTab::class)]
class ProductTabRepository implements ManageRepositoryInterface, ListRepositoryInterface
{
    use ManageRepositoryTrait;
    use ListRepositoryTrait;

    public function getListSelector(): ListSelector
    {
        $selector = $this->createSelector();

        $selector->from(ProductTab::class)
            ->leftJoin(Article::class)
            ->leftJoin(Page::class);

        $selector->addFilterHandler(
            'category',
            function (Query $query, string $field, mixed $value) {
                if ((string) $value !== '') {
                    $query->whereExists(
                        fn(Query $query) => $query->from(ShopCategoryMap::class)
                            ->whereRaw('target_id = product_tab.id')
                            ->where('type', 'tab')
                            ->where('category_id', $value)
                    );
                }
            }
        );

        return $selector;
    }

    #[ConfigureAction(SaveAction::class)]
    protected function configureSaveAction(SaveAction $action): void
    {
        //
    }

    #[ConfigureAction(ReorderAction::class)]
    protected function configureReorderAction(ReorderAction $action): void
    {
        //
    }

    #[ConfigureAction(BatchAction::class)]
    protected function configureBatchAction(BatchAction $action): void
    {
        //
    }
}
