<?php
namespace Flow\JSONPath\Filters;

class IndexFilter extends AbstractFilter
{
    /**
     * @param array $collection
     * @return array
     */
    public function filter($collection)
    {
        $return = [];

//        echo( "==== index filter ====\n" );var_dump( $collection); var_dump( $this->value );
//        echo( 'keyExists ' . ( $this->keyExists($collection, $this->value) ? 'true' : 'false' ) . "\n" );

        if ($this->keyExists($collection, $this->value)) {
            $return[] = $this->getValue($collection, $this->value);
        } else if ($this->value === "*") {
            return $this->arrayValues($collection);
        }

        return $return;
    }

}
 