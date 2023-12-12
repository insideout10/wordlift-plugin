<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\Compiler;

use Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\ContainerInterface;
use Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\Reference;
/**
 * Checks that all references are pointing to a valid service.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class CheckExceptionOnInvalidReferenceBehaviorPass extends AbstractRecursivePass
{
    protected function processValue($value, $isRoot = \false)
    {
        if (!$value instanceof Reference) {
            return parent::processValue($value, $isRoot);
        }
        if (ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE === $value->getInvalidBehavior() && !$this->container->has($id = (string) $value)) {
            throw new ServiceNotFoundException($id, $this->currentId);
        }
        return $value;
    }
}
