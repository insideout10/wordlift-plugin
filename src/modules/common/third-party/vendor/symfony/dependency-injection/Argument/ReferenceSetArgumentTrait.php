<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Argument;

use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Wordlift\Modules\Common\Symfony\Component\DependencyInjection\Reference;
/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait ReferenceSetArgumentTrait
{
    private $values;
    /**
     * @param Reference[] $values
     */
    public function __construct(array $values)
    {
        $this->setValues($values);
    }
    /**
     * @return Reference[]
     */
    public function getValues()
    {
        return $this->values;
    }
    /**
     * @param Reference[] $values The service references to put in the set
     */
    public function setValues(array $values)
    {
        foreach ($values as $k => $v) {
            if (null !== $v && !$v instanceof Reference) {
                throw new InvalidArgumentException(sprintf('A "%s" must hold only Reference instances, "%s" given.', __CLASS__, get_debug_type($v)));
            }
        }
        $this->values = $values;
    }
}
