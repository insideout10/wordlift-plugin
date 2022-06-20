<?php

namespace Wordlift\Modules\Common;

if (\PHP_VERSION_ID < 80000) {
    class UnhandledMatchError extends \Error
    {
    }
}
