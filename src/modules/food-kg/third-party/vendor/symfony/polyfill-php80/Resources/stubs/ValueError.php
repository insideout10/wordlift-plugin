<?php

namespace Wordlift\Modules\Food_Kg_Dependencies;

if (\PHP_VERSION_ID < 80000) {
    class ValueError extends \Error
    {
    }
}
