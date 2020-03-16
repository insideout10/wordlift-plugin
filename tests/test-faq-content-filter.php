<?php
class Faq_Content_Filter_Test extends Wordlift_Unit_Test_Case {
	/**
	 * @var \Wordlift\Faq\Faq_Content_Filter
	 */
	private $content_filter_service;

	public function setUp() {
		parent::setUp();
		$this->content_filter_service = new \Wordlift\Faq\Faq_Content_Filter();
	}
	/**
	 * Test if the highlighting done by FAQ is removed.
	 *
	 * @since 3.26.0
	 */
	public function test_faq_question_highlighting_tag_removed() {
		$src = <<<EOF
			<p><wl-faq-question class="123">this is a question?</wl-faq-question></p>
EOF;
		$expected_output = <<<EOF
			<p>this is a question?</p>
EOF;
		$result = $this->content_filter_service->remove_all_faq_question_and_answer_tags($src);
		$this->assertEquals($expected_output, $result);
	}

	/**
	 * Test if the highlighting done by FAQ is removed.
	 *
	 * @since 3.26.0
	 */
	public function test_faq_answer_highlighting_tag_removed() {
		$src = <<<EOF
			<p><wl-faq-answer class="123">this is a answer</wl-faq-answer></p>
EOF;
		$expected_output = <<<EOF
			<p>this is a answer</p>
EOF;
		$result = $this->content_filter_service->remove_all_faq_question_and_answer_tags($src);
		$this->assertEquals($expected_output, $result);
	}

	/**
	 * Testing if the nested tags inside the span should not affect the outside regex.
	 *
	 * @since 3.26.0
	 */
	public function test_faq_answer_highlighting_nested_tags_removed_correctly() {
		$src = <<<EOF
			<p>this<wl-faq-answer class="123"><span class="foo">is</span></wl-faq-answer> a answer</p>
EOF;
		$expected_output = <<<EOF
			<p>this<span class="foo">is</span> a answer</p>
EOF;
		$result = $this->content_filter_service->remove_all_faq_question_and_answer_tags($src);
		$this->assertEquals($expected_output, $result);
	}

	/**
	 * Testing if the nested tags inside the span should not affect the outside regex.
	 *
	 * @since 3.26.0
	 */
	public function test_faq_question_highlighting_nested_tags_removed_correctly() {
		$src = <<<EOF
			<p><wl-faq-question class="123">this<span class="foo">is</span> a question?</wl-faq-question></p>
EOF;
		$expected_output = <<<EOF
			<p>this<span class="foo">is</span> a question?</p>
EOF;
		$result = $this->content_filter_service->remove_all_faq_question_and_answer_tags($src);
		$this->assertEquals($expected_output, $result);
	}


}