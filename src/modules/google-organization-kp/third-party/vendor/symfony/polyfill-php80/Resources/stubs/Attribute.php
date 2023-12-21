<?php

namespace Wordlift\Modules\Google_Organization_Kp;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
<<<<<<< HEAD
#[\Attribute(\Attribute::TARGET_CLASS)]
=======
#[Attribute(Attribute::TARGET_CLASS)]
>>>>>>> See #1717: Initial work on Google Organization KP API
final class Attribute
{
    public const TARGET_CLASS = 1;
    public const TARGET_FUNCTION = 2;
    public const TARGET_METHOD = 4;
    public const TARGET_PROPERTY = 8;
    public const TARGET_CLASS_CONSTANT = 16;
    public const TARGET_PARAMETER = 32;
    public const TARGET_ALL = 63;
    public const IS_REPEATABLE = 64;
    /** @var int */
    public $flags;
    public function __construct(int $flags = self::TARGET_ALL)
    {
        $this->flags = $flags;
    }
}
