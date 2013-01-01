<?php

class WordLift_SparqlEndPointAjaxService {

	public function process() {

/* MySQL and endpoint configuration */ 
$config = array(
  /* db */
  'db_host' => 'localhost', /* optional, default is localhost */
  'db_name' => 'wordlift_dev',
  'db_user' => 'wordlift',
  'db_pwd' => 'wordliftpwd',

  /* store name */
  'store_name' => 'wordlift',

  /* endpoint */
  'endpoint_features' => array(
    'select', 'construct', 'ask', 'describe', 
    'load', 'insert', 'delete', 
    'dump' /* dump is a special command for streaming SPOG export */
  ),
  'endpoint_timeout' => 60, /* not implemented in ARC2 preview */
  'endpoint_read_key' => '', /* optional */
  'endpoint_write_key' => 'REPLACE_THIS_WITH_SOME_KEY', /* optional, but without one, everyone can write! */
  'endpoint_max_limit' => 250, /* optional */
);

/* instantiation */
$ep = ARC2::getStoreEndpoint($config);

if (!$ep->isSetUp()) {
  $ep->setUp(); /* create MySQL tables */
}

/* request handling */
$ep->go();
	}

}

?>