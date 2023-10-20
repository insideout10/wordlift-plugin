<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Wordlift\Modules\Plugin_Diagnostics\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Wordlift\Modules\Plugin_Diagnostics\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Wordlift\Modules\Plugin_Diagnostics\Symfony\Component\DependencyInjection\Reference;
trait BindTrait
{
    /**
     * Sets bindings.
     *
     * Bindings map $named or FQCN arguments to values that should be
     * injected in the matching parameters (of the constructor, of methods
     * called and of controller actions).
     *
     * @param string $nameOrFqcn A parameter name with its "$" prefix, or a FQCN
     * @param mixed  $valueOrRef The value or reference to bind
     *
     * @return $this
     */
    public final function bind($nameOrFqcn, $valueOrRef)
    {
        $valueOrRef = static::processValue($valueOrRef, \true);
        if (isset($nameOrFqcn[0]) && '$' !== $nameOrFqcn[0] && !$valueOrRef instanceof Reference) {
            throw new InvalidArgumentException(\sprintf('Invalid binding for service "%s": named arguments must start with a "$", and FQCN must map to references. Neither applies to binding "%s".', $this->id, $nameOrFqcn));
        }
        $bindings = $this->definition->getBindings();
        $bindings[$nameOrFqcn] = $valueOrRef;
        $this->definition->setBindings($bindings);
        return $this;
    }
}
