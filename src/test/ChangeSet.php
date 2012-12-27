<?php

class ChangeSet {

	private $about;
	private $subjectOfChange;
	private $createdDate;
	private $creatorName;
	private $changeReason;
	private $removals;
	private $additions;

	function __construct( $about, $subjectOfChange, $createdDate, $creatorName, $changeReason, $additions, $removals ) {
		$this->about = $about;
		$this->subjectOfChange = $subjectOfChange;
		$this->createdDate = $createdDate;
		$this->creatorName = $creatorName;
		$this->changeReason = $changeReason;
		$this->removals = $removals;
		$this->additions = $additions;
	}

	public function getAbout() {
		return $this->about;
	}

	public function getSubjectOfChange() {
		return $this->subjectOfChange;
	}

	public function getCreatedDate() {
		return $this->createdDate;
	}

	public function getCreatorName() {
		return $this->creatorName;
	}

	public function getChangeReason() {
		return $this->changeReason;
	}

	public function getRemovals() {
		return $this->removals;
	}

	public function getAdditions() {
		return $this->additions;
	}

}

?>