<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Models;

// TODO: Convert most properties into methods and only keep int $currentPage, int $totalPages, string $collectionSlug, int $itemsTotal
// TODO: Make $path ans class property
class CollectionPaginator
{
    public function __construct(
        public ?string $next,
        public ?string $previous,
        public int $currentPage,
        public int $totalPages,
        public int $total,
    ) {}

    public static function create(int $currentPage, int $totalPages, string $collectionSlug, int $itemsTotal): CollectionPaginator
    {
        $shouldHaveNextPage = $currentPage < $totalPages;
        $nextPage = $currentPage + 1;
        $nextSlug = "{$collectionSlug}/page/{$nextPage}";

        $shouldHavePreviousPage = $currentPage > 1;
        $previousPage = $currentPage - 1;
        $previousPageUrl = $previousPage !== 1 ? '/page/'.$previousPage : '/';
        $previousSlug = $collectionSlug.$previousPageUrl;

        return new CollectionPaginator(
            next: $shouldHaveNextPage ? url($nextSlug) : null,
            previous: $shouldHavePreviousPage ? url($previousSlug) : null,
            currentPage: $currentPage,
            totalPages: $totalPages,
            total: $itemsTotal,
        );
    }

    public function isLast(): bool
    {
        return $this->currentPage === $this->totalPages;
    }
}
