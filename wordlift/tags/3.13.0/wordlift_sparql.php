<?php
/**
 * This file contains SPARQL related functions.
 */


/**
 * Convert a time string to a SPARQL datetime.
 * @param string $time The time string (in 2014-03-03T08:15:55+00:00 format).
 * @return string A sparql dateTime string (e.g. "2014-03-03T08:15:55.000Z"^^<http://www.w3.org/2001/XMLSchema#dateTime>)
 */
function wl_get_sparql_time($time)
{

    return '"' . str_replace('+00:00', '.000Z', $time) . '"^^<http://www.w3.org/2001/XMLSchema#dateTime>';
}