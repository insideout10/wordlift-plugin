<?php
require_once('Thing.php');

class Organization extends Thing {
	
	/**
	 * @type PostalAddress
	 * @description Physical address of the item.
	 */
	public $address;

	/**
	 * @type AggregateRating
	 * @description The overall rating, based on a collection of reviews or ratings, of the item.
	 */
	public $aggregateRating;
	
	/**
	 * @type ContactPoint
	 * @description A contact point for a person or organization.
	 */
	public $contactPoints;
	
	/**
	 * @type Text;
	 * @description Email address.
	 */
	public $email;
	
	/**
	 * @type Person
	 * @description People working for this organization.
	 */
	public $employees;
	
	/**
	 * @type Event
	 * @description Upcoming or past events associated with this place or organization.
	 */
	public $events;
	
	/**
	 * @type Text
	 * @description The fax number.
	 */
	public $faxNumber;
	
	/**
	 * @type Person
	 * @description A person who founded this organization.
	 */
	public $founders;
	
	/**
	 * @type Date
	 * @description The date that this organization was founded.
	 */
	public $foundingDate;
	
	/**
	 * @type Text
	 * @description A count of a specific user interactions with this item-for example, 20 UserLikes, 5 UserComments, or 300 UserDownloads. The user interaction type should be one of the sub types of UserInteraction.
	 */
	public $interactionCount;
	
	/**
	 * @type Place or PostalAddress
	 * @description The location of the event or organization.
	 */
	public $location;
	
	/**
	 * @type Person or Organization
	 * @description A member of this organization.
	 */
	public $members;
	
	/**
	 * @type Review
	 * @description Review of the item.
	 */
	public $reviews;
	
	/**
	 * @type Text
	 * @description The telephone number.
	 */
	public $telephone;
	
}

?>