<?php

use Wordlift\FAQ\FAQ_Rest_Controller;
use Wordlift\FAQ\Faq_To_Jsonld_Converter;

/**
 * Tests: Tests the FAQ Rest Controller
 * @since 3.26.0
 * @package wordlift
 * @subpackage wordlift/tests
 *
 */

class Faq_To_Jsonld_Converter_Test extends Wordlift_Unit_Test_Case {
	public function test_if_converter_returns_correct_type() {
		$post_id = $this->factory()->post->create( array('post_title' => 'foo'));
		$data = Faq_To_Jsonld_Converter::get_jsonld_for_faq($post_id);
		$this->assertArrayHasKey( '@type', $data );
		$this->assertEquals( $data['@type'], 'FAQPage');
	}

	public function test_given_sample_faq_data_return_correct_jsonld() {
		$post_id = $this->factory()->post->create( array('post_title' => 'foo'));
		add_post_meta( $post_id, FAQ_Rest_Controller::FAQ_META_KEY, array(
			'question' => 'foo1',
			'answer' => 'bar1'
		));
		add_post_meta( $post_id, FAQ_Rest_Controller::FAQ_META_KEY, array(
			'question' => 'foo2',
			'answer' => 'bar2'
		));
		$data = Faq_To_Jsonld_Converter::get_jsonld_for_faq($post_id);
		echo json_encode($data);
		$this->assertArrayHasKey( 'mainEntity', $data );
		$this->assertCount( 2, $data['mainEntity'] );
		$single_faq_item = $data['mainEntity'][0];
		$this->assertArrayHasKey( '@type', $single_faq_item );
		$this->assertArrayHasKey( 'name', $single_faq_item );
		$this->assertEquals($single_faq_item['@type'], 'Question');
		$this->assertEquals($single_faq_item['name'], 'foo1');

		$this->assertArrayHasKey( 'acceptedAnswer', $single_faq_item );
		$single_answer = $single_faq_item['acceptedAnswer'];
		$this->assertArrayHasKey( '@type', $single_answer );
		$this->assertArrayHasKey( 'text', $single_answer );
		$this->assertEquals($single_answer['@type'], 'Answer');
		$this->assertEquals($single_answer['text'], 'bar1');

	}
}