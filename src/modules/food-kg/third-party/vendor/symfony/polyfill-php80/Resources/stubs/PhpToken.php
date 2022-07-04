<?php

namespace Wordlift\Modules\Food_Kg_Dependencies;

if (\PHP_VERSION_ID < 80000 && \extension_loaded('tokenizer')) {
    class PhpToken extends \Wordlift\Modules\Food_Kg_Dependencies\Symfony\Polyfill\Php80\PhpToken
    {
    }
}
