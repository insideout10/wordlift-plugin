<?php

class ChangeSetService {

	const CHANGESET_PREFIX = "cs";
	const CHANGESET_ABOUT_PREFIX = "cs-";

	public function create( $subjectOfChange, $creatorName, $changeReason, $additions, $removals ) {
		$createdDate = new DateTime();
		return $this->createWithCreatedDate( $subjectOfChange, $createdDate, $creatorName, $changeReason, $additions, $removals );
	}

	public function createWithCreatedDate( $subjectOfChange, $createdDate, $creatorName, $changeReason, $additions, $removals ) {
		$about = uniqid( self::CHANGESET_ABOUT_PREFIX );
		return $this->createWithChangeSetId( $about, $subjectOfChange, $createdDate, $creatorName, $changeReason, $additions, $removals );
	}

	public function createWithChangeSetId( $about, $subjectOfChange, $createdDate, $creatorName, $changeReason, $additions, $removals ) {
		return new ChangeSet( $about, $subjectOfChange, $createdDate, $creatorName, $changeReason, $additions, $removals );
	}

	public function convertToArray( $changeSet ) {
		return array(
			$changeSet->getAbout() => array(
				self::CHANGESET_PREFIX . "subjectOfChange" => $changeSet->getSubjectOfChange(),
				self::CHANGESET_PREFIX . "creatorName" => $changeSet->getCreatorName(),
				self::CHANGESET_PREFIX . "changeReason" => $changeSet->getChangeReason(),
				self::CHANGESET_PREFIX . "createdDate" => $changeSet->getCreatedDate()
			)
		);
	}

}

?>