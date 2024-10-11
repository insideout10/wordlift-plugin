<?php

namespace Wordlift\Modules\Common;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Wordlift\Modules\Common\Symfony\Polyfill\Php81 as p;
if (\PHP_VERSION_ID >= 80100) {
    return;
}
if (\defined('MYSQLI_REFRESH_SLAVE') && !\defined('MYSQLI_REFRESH_REPLICA')) {
    \define('MYSQLI_REFRESH_REPLICA', 64);
}
if (!\function_exists('array_is_list') && !\function_exists('Wordlift\Modules\Common\array_is_list')) {
    function array_is_list(array $array): bool
    {
        return p\Php81::array_is_list($array);
    }
}
if (!\function_exists('enum_exists') && !\function_exists('Wordlift\Modules\Common\enum_exists')) {
    function enum_exists(string $enum, bool $autoload = \true): bool
    {
        return $autoload && \class_exists($enum) && \false;
    }
}
