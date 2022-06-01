<?php

namespace Wordlift\Modules\Plugin_Installer_Dependencies;

if (\PHP_VERSION_ID < 80000 && \extension_loaded('tokenizer')) {
    class PhpToken extends \Wordlift\Modules\Plugin_Installer_Dependencies\Symfony\Polyfill\Php80\PhpToken
    {
    }
}
