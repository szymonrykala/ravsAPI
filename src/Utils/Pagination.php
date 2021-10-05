<?php

declare(strict_types=1);

namespace App\Utils;

use JsonSerializable;

class Pagination implements JsonSerializable
{
    // prepare for links generation
    public const PAGES_COUNT = 'pagesCount';
    public const CURRENT_PAGE = 'currentPage';
    public const ITEMS_ON_PAGE = 'itemsOnPage';

    private int $pagesCount;
    private int $currentPage;
    private int $itemsOnPage;

    /**
     * @param int currentPage =1
     * @param int itemsCount = 30
     */
    public function __construct(
        int $currentPage,
        int $itemsOnPage
    ) {
        $this->currentPage = $currentPage < 1 ? 1 : $currentPage;
        $this->itemsOnPage = $itemsOnPage < 1 ? 1 : $itemsOnPage;
    }

    /**
     * @param int pagesCount
     * @return void
     */
    public function calculatePagesCount(int $itemsCount): void
    {
        $pages = (int) ceil($itemsCount / $this->itemsOnPage);

        $this->pagesCount = $pages == 0 ? 1 : $pages;

        if ($this->currentPage > $this->pagesCount)
            $this->currentPage = $this->pagesCount;
    }

    public function generateSQL(): string
    {
        $sql = ' LIMIT ' . ($this->itemsOnPage * ($this->currentPage - 1))
            . ',' . $this->itemsOnPage;
        return $sql;
    }


    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            Pagination::PAGES_COUNT => $this->pagesCount,
            Pagination::CURRENT_PAGE => $this->currentPage,
            Pagination::ITEMS_ON_PAGE => $this->itemsOnPage,
        ];
    }
}
