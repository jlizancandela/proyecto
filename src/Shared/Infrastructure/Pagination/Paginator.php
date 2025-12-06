<?php

namespace Shared\Infrastructure\Pagination;

class Paginator
{
    public static function getPagination(
        int $currentPage,
        int $totalPages,
        string $baseUrl
    ): array {
        $pages = [];

        for ($i = 1; $i <= $totalPages; $i++) {
            $pages[] = [
                'number' => $i,
                'url' => self::buildUrl($baseUrl, $i),
                'active' => $i == $currentPage
            ];
        }

        return [
            'pages' => $pages,
            'hasPrevious' => $currentPage > 1,
            'hasNext' => $currentPage < $totalPages,
            'previousUrl' => self::buildUrl($baseUrl, $currentPage - 1),
            'nextUrl' => self::buildUrl($baseUrl, $currentPage + 1),
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ];
    }

    public static function getTotalPages(int $totalItems, int $itemsPerPage): int
    {
        return (int) ceil($totalItems / $itemsPerPage);
    }

    public static function getOffset(int $page, int $itemsPerPage): int
    {
        return ($page - 1) * $itemsPerPage;
    }

    private static function buildUrl(string $baseUrl, int $page): string
    {
        $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
        return "{$baseUrl}{$separator}page={$page}";
    }

    public static function validatePage($page, int $totalPages): int
    {
        $page = (int) $page;

        if ($page < 1) {
            return 1;
        }

        if ($page > $totalPages) {
            return max(1, $totalPages);
        }

        return $page;
    }
}
