<?php

namespace Wordlift\Modules\Common;

if (\PHP_VERSION_ID < 80000 && \extension_loaded('tokenizer')) {
    class PhpToken extends \Wordlift\Modules\Common\Symfony\Polyfill\Php80\PhpToken
    {
    }
}
