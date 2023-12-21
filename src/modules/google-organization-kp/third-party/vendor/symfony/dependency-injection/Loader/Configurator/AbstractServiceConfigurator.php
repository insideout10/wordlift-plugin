<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\Loader\Configurator;

use Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\Definition;
use Wordlift\Modules\Google_Organization_Kp\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
abstract class AbstractServiceConfigurator extends AbstractConfigurator
{
    protected $parent;
    protected $id;
    private $defaultTags = [];
    public function __construct(ServicesConfigurator $parent, Definition $definition, $id = null, array $defaultTags = [])
    {
        $this->parent = $parent;
        $this->definition = $definition;
        $this->id = $id;
        $this->defaultTags = $defaultTags;
    }
    public function __destruct()
    {
        // default tags should be added last
        foreach ($this->defaultTags as $name => $attributes) {
            foreach ($attributes as $attributes) {
                $this->definition->addTag($name, $attributes);
            }
        }
        $this->defaultTags = [];
    }
    /**
     * Registers a service.
     *
     * @param string      $id
     * @param string|null $class
     *
     * @return ServiceConfigurator
     */
    public final function set($id, $class = null)
    {
        $this->__destruct();
        return $this->parent->set($id, $class);
    }
    /**
     * Creates an alias.
     *
     * @param string $id
     * @param string $referencedId
     *
     * @return AliasConfigurator
     */
    public final function alias($id, $referencedId)
    {
        $this->__destruct();
        return $this->parent->alias($id, $referencedId);
    }
    /**
     * Registers a PSR-4 namespace using a glob pattern.
     *
     * @param string $namespace
     * @param string $resource
     *
     * @return PrototypeConfigurator
     */
    public final function load($namespace, $resource)
    {
        $this->__destruct();
        return $this->parent->load($namespace, $resource);
    }
    /**
     * Gets an already defined service definition.
     *
     * @param string $id
     *
     * @return ServiceConfigurator
     *
     * @throws ServiceNotFoundException if the service definition does not exist
     */
    public final function get($id)
    {
        $this->__destruct();
        return $this->parent->get($id);
    }
    /**
     * Registers a service.
     *
     * @param string      $id
     * @param string|null $class
     *
     * @return ServiceConfigurator
     */
    public final function __invoke($id, $class = null)
    {
        $this->__destruct();
        return $this->parent->set($id, $class);
    }
}
