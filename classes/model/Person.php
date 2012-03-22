<?php
require_once 'Thing.php';

/**
 * @schema http://schema.org/Person
 * @author david
 *
 */
class Person extends Thing {
	
	/**
	 * @type Text
	 * @description An additional name for a Person, can be used for a middle name.
	 */
	public $additionalName;
	
	/**
	 * @type PostalAddress
	 * @description Physical address of the item.
	 */
	public $address;
	
	/**
	 * @type Organization
	 * @description An organization that this person is affiliated with. For example, a school/university, a club, or a team.
	 */
	public $affiliation;
	
	/**
	 * @type EducationalOrganization
	 * @description An educational organizations that the person is an alumni of.
	 */
	public $alumniOf;

	/**
	 * @type Text
	 * @description Awards won by this person or for this creative work. 
	 */
	public $awards;

	/**
	 * @type Date
	 * @description Date of birth.
	 */
	public $birthDate;
	
	/**
	 * @type Person
	 * @description A child of the person.
	 */
	public $children;
	
	/**
	 * @type Person
	 * @description A colleague of the person.
	 */
	public $colleagues;
		
	/**
	 * @type ContactPoint
	 * @description A contact point for a person or organization.
	 */
	public $contactPoints;
		
	/**
	 * @type Date
	 * @description Date of death.
	 */
	public $deathDate;
		
	/**
	 * @type Text
	 * @description Email address. 
	 */
	public $email;
		
	/**
	 * @type Text
	 * @description Family name. In the U.S., the last name of an Person. This can be used along with givenName instead of the Name property.
	 */
	public $familyName;
		
	/**
	 * @type Text
	 * @description The fax number.
	 */
	public $faxNumber;
		
	/**
	 * @type Person
	 * @description The most generic uni-directional social relation.
	 */
	public $follows;
		
	/**
	 * @type Text
	 * @description Gender of the person. 
	 */
	public $gender;
		
	/**
	 * @type Text
	 * @description Given name. In the U.S., the first name of a Person. This can be used along with familyName instead of the Name property.
	 */
	public $givenName;
		
	/**
	 * @type Place or ContactPoint
	 * @description A contact location for a person's residence.
	 */
	public $homeLocation;
		
	/**
	 * @type Text
	 * @description An honorific prefix preceding a Person's name such as Dr/Mrs/Mr.
	 */
	public $honorificPrefix;
		
	/**
	 * @type Text
	 * @description An honorific suffix preceding a Person's name such as M.D. /PhD/MSCSW.
	 */
	public $honorificSuffix;
		
	/**
	 * @type Text
	 * @description A count of a specific user interactions with this item-for example, 20 UserLikes, 5 UserComments, or 300 UserDownloads. The user interaction type should be one of the sub types of UserInteraction.
	 */
	public $interactionCount;
		
	/**
	 * @type Text
	 * @description The job title of the person (for example, Financial Manager).
	 */
	public $jobTitle;
		
	/**
	 * @type Person
	 * @description The most generic bi-directional social/work relation. 
	 */
	public $knows;
		
	/**
	 * @type Organization
	 * @description An organization to which the person belongs.
	 */
	public $memberOf;
		
	/**
	 * @type Country
	 * @description Nationality of the person.
	 */
	public $nationality;
		
	/**
	 * @type Person
	 * @description A parents of the person.
	 */
	public $parents;
		
	/**
	 * @type Event
	 * @description Event that this person is a performer or participant in.
	 */
	public $performerIn;
		
	/**
	 * @type Person
	 * @description The most generic familial relation.
	 */
	public $relatedTo;
		
	/**
	 * @type Person
	 * @description A sibling of the person.
	 */
	public $siblings;
		
	/**
	 * @type Person
	 * @description The person's spouse. 
	 */
	public $spouse;
		
	/**
	 * @type Text
	 * @description The telephone number.
	 */
	public $telephone;
		
	/**
	 * @type Place or ContactPoint
	 * @description A contact location for a person's place of work.
	 */
	public $workLocation;
		
	/**
	 * @type Organization
	 * @description Organizations that the person works for.
	 */
	public $worksFor;
	
}

?>