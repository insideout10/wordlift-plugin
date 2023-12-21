<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait FileTrait
{
    /**
     * Sets a file to require before creating the service.
     *
     * @param string $file A full pathname to include
     *
     * @return $this
     */
    public final function file($file)
    {
        $this->definition->setFile($file);
        return $this;
    }
}
