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

    // onPage page total

    private int $pagesCount;
    private int $currentPage;
    private int $itemsOnPage;


    public function __construct(
        int $currentPage,
        int $itemsOnPage
    ) {
        $this->currentPage = $currentPage < 1 ? 1 : $currentPage;
        $this->itemsOnPage = $itemsOnPage < 1 ? 1 : $itemsOnPage;
    }

    /**
     * calculates number of pages
     */
    public function calculatePagesCount(int $itemsCount): void
    {
        $pages = (int) ceil($itemsCount / $this->itemsOnPage);

        $this->pagesCount = $pages == 0 ? 1 : $pages;

        if ($this->currentPage > $this->pagesCount)
            $this->currentPage = $this->pagesCount;
    }

    /**
     * generates pagination SQL code
     */
    public function generateSQL(): string
    {
        $sql = ' LIMIT ' . $this->itemsOnPage . ' OFFSET ' . (($this->currentPage - 1) * $this->itemsOnPage);
        return $sql;
    }


    /**
     * {@inheritDoc}
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
