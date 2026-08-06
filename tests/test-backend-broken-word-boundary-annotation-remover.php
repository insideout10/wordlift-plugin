<?php
/**
 * Test broken word-boundary annotation cleanup.
 *
 * @group backend
 */
class Broken_Word_Boundary_Annotation_Remover_Test extends Wordlift_Unit_Test_Case {

	/**
	 * @var \Wordlift\Cleanup\Broken_Word_Boundary_Annotation_Remover
	 */
	private $remover;

	public function setUp() {
		parent::setUp();

		$this->remover = new \Wordlift\Cleanup\Broken_Word_Boundary_Annotation_Remover();
	}

	public function test_removes_annotation_splitting_word_at_start_boundary() {
		$content = 'This code compiles a list of plugi<span id="urn:enhancement-1" class="textannotation disambiguated wl-creative-work" itemid="http://example.org/entity/plugin">ns. To</span> ensure uniqueness.';

		$result = $this->remover->remove( $content );

		$this->assertTrue( $result['changed'] );
		$this->assertSame( 1, $result['removed_count'] );
		$this->assertSame( 'This code compiles a list of plugins. To ensure uniqueness.', $result['content'] );
	}

	public function test_removes_annotation_splitting_word_at_end_boundary() {
		$content = 'Feedback, <span id="urn:enhancement-2" class="textannotation disambiguated wl-thing" itemid="http://example.org/entity/kinsta">as Kri</span>stof Siket shares.';

		$result = $this->remover->remove( $content );

		$this->assertTrue( $result['changed'] );
		$this->assertSame( 'Feedback, as Kristof Siket shares.', $result['content'] );
	}

	public function test_removes_annotation_splitting_word_at_both_boundaries() {
		$content = 'The Kinsta AP<span id="urn:enhancement-3" class="textannotation disambiguated wl-thing" itemid="http://example.org/entity/kinsta">I alre</span>ady has endpoints.';

		$result = $this->remover->remove( $content );

		$this->assertTrue( $result['changed'] );
		$this->assertSame( 'The Kinsta API already has endpoints.', $result['content'] );
	}

	public function test_keeps_valid_whole_phrase_annotation() {
		$content = 'This is a <span id="urn:enhancement-4" class="textannotation disambiguated wl-thing" itemid="http://example.org/entity/plugin">WordPress plugin</span> example.';

		$result = $this->remover->remove( $content );

		$this->assertFalse( $result['changed'] );
		$this->assertSame( $content, $result['content'] );
	}

	public function test_removes_non_disambiguated_annotation_without_itemid() {
		$content = 'The plug<span id="urn:enhancement-5" class="textannotation">in</span> is active.';

		$result = $this->remover->remove( $content );

		$this->assertTrue( $result['changed'] );
		$this->assertSame( 'The plugin is active.', $result['content'] );
	}

	public function test_keeps_valid_non_disambiguated_annotation_without_itemid() {
		$content = 'The <span id="urn:enhancement-6" class="textannotation">plugin</span> is active.';

		$result = $this->remover->remove( $content );

		$this->assertFalse( $result['changed'] );
		$this->assertSame( $content, $result['content'] );
	}

	public function test_preserves_inner_markup_when_unwrapping() {
		$content = 'Word<span class="textannotation">Li<strong>ft</strong></span> works.';

		$result = $this->remover->remove( $content );

		$this->assertTrue( $result['changed'] );
		$this->assertSame( 'WordLi<strong>ft</strong> works.', $result['content'] );
	}

	public function test_nested_span_does_not_break_matching() {
		$content = 'Word<span class="textannotation">Li<span>ft</span></span> works.';

		$result = $this->remover->remove( $content );

		$this->assertTrue( $result['changed'] );
		$this->assertSame( 'WordLi<span>ft</span> works.', $result['content'] );
	}

	public function test_supports_class_order_and_single_quoted_attributes() {
		$content = "Word<span class='selected textannotation wl-thing'>Lift</span> works.";

		$result = $this->remover->remove( $content );

		$this->assertTrue( $result['changed'] );
		$this->assertSame( 'WordLift works.', $result['content'] );
	}

	public function test_ignores_spans_without_textannotation_class() {
		$content = 'Word<span class="other">Lift</span> works.';

		$result = $this->remover->remove( $content );

		$this->assertFalse( $result['changed'] );
		$this->assertSame( $content, $result['content'] );
	}

	public function test_unmatched_span_is_unchanged() {
		$content = 'Word<span class="textannotation">Lift works.';

		$result = $this->remover->remove( $content );

		$this->assertFalse( $result['changed'] );
		$this->assertSame( $content, $result['content'] );
	}

	public function test_html_entities_are_decoded_for_boundary_checks() {
		$content = 'caf&eacute;<span class="textannotation">s</span> are open.';

		$result = $this->remover->remove( $content );

		$this->assertTrue( $result['changed'] );
		$this->assertSame( 'caf&eacute;s are open.', $result['content'] );
	}

	public function test_removes_multiple_broken_annotations() {
		$content = 'plug<span class="textannotation">in</span> and AP<span class="textannotation">I</span>.';

		$result = $this->remover->remove( $content );

		$this->assertTrue( $result['changed'] );
		$this->assertSame( 2, $result['removed_count'] );
		$this->assertSame( 'plugin and API.', $result['content'] );
	}

	public function test_has_broken_annotations_returns_true_for_broken_annotation() {
		$content = 'plug<span class="textannotation">in</span>';

		$this->assertTrue( $this->remover->has_broken_annotations( $content ) );
	}

	public function test_has_broken_annotations_returns_false_for_valid_annotation() {
		$content = '<span class="textannotation">plugin</span>';

		$this->assertFalse( $this->remover->has_broken_annotations( $content ) );
	}
}
