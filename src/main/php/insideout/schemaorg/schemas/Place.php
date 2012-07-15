<?php
require_once 'Thing.php';

class Place extends Thing {
	
	/**
	 * Get the friendly name for this schema.
	 * @return string The friendly name for this schema.
	 */
	public static function getFriendlyName() {
		return 'Place';
	}
	
    // /**
    //  * @type PostalAddress
    //  * @description Physical address of the item.
    //  */
    // public $address;     
	
    // /**
    //  * @type AggregateRating
    //  * @description The overall rating, based on a collection of reviews or ratings, of the item.
    //  */
    // public $aggregateRating;

	/**
	 * @type Place
	 * @description The basic containment relation between places.
	 */
	public $containedIn;
	
    // /**
    //  * @type Event
    //  * @description Upcoming or past events associated with this place or organization.
    //  */
    // public $events;
	
	/**
	 * @type Text
	 * @description The fax number.
	 */
	public $faxNumber;	 	
	
	/**
	 * @type GeoCoordinates
	 * @additionalTypes GeoShape
	 * @description The geo coordinates of the place.
	 */
	public $geo;
	
    // /**
    //  * @type Text
    //  * @description A count of a specific user interactions with this item-for example, 20 UserLikes, 5 UserComments, or 300 UserDownloads. The user interaction type should be one of the sub types of UserInteraction.
    //  */
    // public $interactionCount;
	
	/**
	 * @type URL
	 * @description A URL to a map of the place.
	 */
	public $maps;
	
    // /**
    //  * @type Photograph or ImageObject
    //  * @description Photographs of this place.
    //  */
    // public $photos;
	
    // /**
    //  * @type Review
    //  * @description Review of the item.
    //  */
    // public $reviews;
	
	/**
	 * @type Text
	 * @description The telephone number.
	 */
	public $telephone;
	
}

?>