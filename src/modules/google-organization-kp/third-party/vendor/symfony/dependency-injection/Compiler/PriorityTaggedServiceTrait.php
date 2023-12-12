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

use Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\ContainerBuilder;
use Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\Reference;
/**
 * Trait that allows a generic method to find and sort service by priority option in the tag.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
trait PriorityTaggedServiceTrait
{
    /**
     * Finds all services with the given tag name and order them by their priority.
     *
     * The order of additions must be respected for services having the same priority,
     * and knowing that the \SplPriorityQueue class does not respect the FIFO method,
     * we should not use that class.
     *
     * @see https://bugs.php.net/53710
     * @see https://bugs.php.net/60926
     *
     * @param string $tagName
     *
     * @return Reference[]
     */
    private function findAndSortTaggedServices($tagName, ContainerBuilder $container)
    {
        $services = [];
        foreach ($container->findTaggedServiceIds($tagName, \true) as $serviceId => $attributes) {
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $services[$priority][] = new Reference($serviceId);
        }
        if ($services) {
            \krsort($services);
            $services = \call_user_func_array('array_merge', $services);
        }
        return $services;
    }
}
