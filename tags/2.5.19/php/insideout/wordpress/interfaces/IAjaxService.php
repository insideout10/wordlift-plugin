<?php
/**
 * User: david
 * Date: 21/07/12 10:34
 */

interface WordPress_IAjaxService {

    public function bindAction( $instance, $method, $action, $authentication = false, $capabilities = "any", $compression = true );

}