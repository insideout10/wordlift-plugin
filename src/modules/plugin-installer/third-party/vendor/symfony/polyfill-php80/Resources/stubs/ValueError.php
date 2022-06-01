<?php

namespace Wordlift\Modules\Plugin_Installer_Dependencies;

if (\PHP_VERSION_ID < 80000) {
    class ValueError extends \Error
    {
    }
}
