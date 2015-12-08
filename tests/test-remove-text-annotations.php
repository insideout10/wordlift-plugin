<?php
require_once 'functions.php';

class RemoveTextAnnotationsTest extends WP_UnitTestCase
{

    /**
     * Set up the test.
     */
    function setUp()
    {
        parent::setUp();
        wl_configure_wordpress_test();
    }

    function testRemoveATextAnnotation() {

        $content = <<<EOF
Sono nato a <span id="urn:enhancement-69d1fcf5-878b-4462-68f4-8066eb93c0f9" class="textannotation" contenteditable="false">Roma</span>.
EOF;
        $expected_content = <<<EOF
Sono nato a Roma.
EOF;
        // addslashes is used here to simulate a content sent in $_POST
        $data = array( 'post_content' => addslashes( $content ) );
        $output = wl_remove_text_annotations( $data ); 
        $this->assertEquals( addslashes( $expected_content ), $output[ 'post_content' ] );
    }

    function testRemoveASelectedTextAnnotation() {

        $content = <<<EOF
Sono nato a <span id="urn:enhancement-69d1fcf5-878b-4462-68f4-8066eb93c0f9" class="textannotation selected" contenteditable="false">Roma</span>.
EOF;
        $expected_content = <<<EOF
Sono nato a Roma.
EOF;
        // addslashes is used here to simulate a content sent in $_POST
        $data = array( 'post_content' => addslashes( $content ) );
        $output = wl_remove_text_annotations( $data ); 
        $this->assertEquals( addslashes( $expected_content ), $output[ 'post_content' ] );
    }

    function testRemoveAnUnlinkedTextAnnotation() {

        $content = <<<EOF
Sono nato a <span id="urn:enhancement-69d1fcf5-878b-4462-68f4-8066eb93c0f9" class="textannotation unlinked" contenteditable="false">Roma</span>.
EOF;
        $expected_content = <<<EOF
Sono nato a Roma.
EOF;
        // addslashes is used here to simulate a content sent in $_POST
        $data = array( 'post_content' => addslashes( $content ) );
        $output = wl_remove_text_annotations( $data ); 
        $this->assertEquals( addslashes( $expected_content ), $output[ 'post_content' ] );
    }

    function testRemoveAnUnlinkedAndSelectedTextAnnotation() {

        $content = <<<EOF
Sono nato a <span id="urn:enhancement-69d1fcf5-878b-4462-68f4-8066eb93c0f9" class="textannotation unlinked selected" contenteditable="false">Roma</span>.
EOF;
        $expected_content = <<<EOF
Sono nato a Roma.
EOF;
        // addslashes is used here to simulate a content sent in $_POST
        $data = array( 'post_content' => addslashes( $content ) );
        $output = wl_remove_text_annotations( $data ); 
        $this->assertEquals( addslashes( $expected_content ), $output[ 'post_content' ] );
    }

    function testKeepADisambiguatedTextAnnotation() {

        $content = <<<EOF
<span id="urn:enhancement-1dd737ba-ad9f-68e5-d372-6c98a9cda3c0" class="textannotation" contenteditable="false">Sono</span> nato a <span id="urn:enhancement-7616be76-a52b-b728-6b3b-f94d2499a87b" class="textannotation disambiguated wl-place" contenteditable="false" itemid="http://dbpedia.org/resource/Rome">Roma</span>
EOF;
        $expected_content = <<<EOF
Sono nato a <span id="urn:enhancement-7616be76-a52b-b728-6b3b-f94d2499a87b" class="textannotation disambiguated wl-place" contenteditable="false" itemid="http://dbpedia.org/resource/Rome">Roma</span>
EOF;
        // addslashes is used here to simulate a content sent in $_POST
        $data = array( 'post_content' => addslashes( $content ) );
        $output = wl_remove_text_annotations( $data ); 
        $this->assertEquals( addslashes( $expected_content ), $output[ 'post_content' ] );
    }

}