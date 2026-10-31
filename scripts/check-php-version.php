#!/usr/bin/env php
<?php

/**
 * Refuse to publish a PHP build that predates the patch for its own branch.
 *
 * Comparing against a single floor is not enough. Every branch was patched on
 * a different date, so 8.5.11 is greater than 8.4.27 and still carries
 * CVE-2026-109618 and CVE-2026-109619. A version check that only knows about
 * the 8.4 branch would wave that build straight through.
 *
 * Usage: php scripts/check-php-version.php [version]
 *
 * Without an argument the running interpreter is checked. With one, that
 * version is checked instead, which is what makes this testable.
 */

const PATCHED_MINIMUMS = [
    '8.2' => '8.2.35',
    '8.3' => '8.3.36',
    '8.4' => '8.4.27',
    '8.5' => '8.5.12',
];

$version = $argv[1] ?? PHP_VERSION;
$branch = implode('.', array_slice(explode('.', $version), 0, 2));

if (!isset(PATCHED_MINIMUMS[$branch])) {
    fwrite(
        STDERR,
        "Unknown PHP branch $branch (version $version). Add its patched minimum to scripts/check-php-version.php.\n"
    );
    exit(1);
}

$minimum = PATCHED_MINIMUMS[$branch];

if (version_compare($version, $minimum, '<')) {
    fwrite(
        STDERR,
        "PHP $version is below the patched $minimum for the $branch branch. Update before publishing.\n"
    );
    exit(1);
}

printf("PHP %s is at or above %s\n", $version, $minimum);
exit(0);
