<?php

namespace Wordlift\Modules\Dashboard\Stats;

use JsonSerializable;

class Stats_Settings implements JsonSerializable {

	private $description;
	private $title;
	private $label;
	private $color;
	private $show_all_link;
	private $total;
	private $lifted;

	/**
	 * @param $description
	 * @param $title
	 * @param $label
	 * @param $color
	 * @param $show_all_link
	 * @param $total
	 * @param $lifted
	 */
	public function __construct( $description, $title, $label, $color, $show_all_link, $total, $lifted ) {
		$this->description   = $description;
		$this->title         = $title;
		$this->label         = $label;
		$this->color         = $color;
		$this->show_all_link = $show_all_link;
		$this->total         = $total;
		$this->lifted        = $lifted;
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return array(
			'description'   => $this->description,
			'title'         => $this->title,
			'label'         => $this->label,
			'color'         => $this->color,
			'show_all_link' => $this->show_all_link,
			'total'         => $this->total,
			'lifted'        => $this->lifted,
		);
	}

}
