<?php

/**
 * Paginator
 *
 * Utility class for handling pagination logic.
 * Provides methods to calculate pagination details, build URLs, and validate page numbers.
 */

namespace Shared\Infrastructure\Pagination;

class Paginator
{
    /**
     * Generates pagination details for a given set of parameters.
     *
     * @param int $currentPage The current page number.
     * @param int $totalPages The total number of pages.
     * @param string $baseUrl The base URL for pagination links (e.g., "/users").
     * @return array An associative array containing pagination data.
     */
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

    /**
     * Calculates the total number of pages required for a given number of items.
     *
     * @param int $totalItems The total number of items.
     * @param int $itemsPerPage The number of items to display per page.
     * @return int The total number of pages.
     */
    public static function getTotalPages(int $totalItems, int $itemsPerPage): int
    {
        return (int) ceil($totalItems / $itemsPerPage);
    }

    /**
     * Calculates the offset for a given page number.
     *
     * @param int $page The current page number.
     * @param int $itemsPerPage The number of items to display per page.
     * @return int The offset to use in database queries.
     */
    public static function getOffset(int $page, int $itemsPerPage): int
    {
        return ($page - 1) * $itemsPerPage;
    }

    /**
     * Builds a pagination URL for a specific page.
     *
     * @param string $baseUrl The base URL without page parameters.
     * @param int $page The target page number.
     * @return string The constructed URL with the page parameter.
     */
    private static function buildUrl(string $baseUrl, int $page): string
    {
        $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
        return "{$baseUrl}{$separator}page={$page}";
    }

    /**
     * Validates a given page number, ensuring it is within valid bounds.
     *
     * @param mixed $page The page number to validate.
     * @param int $totalPages The total number of available pages.
     * @return int The validated and corrected page number.
     */
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
