<?php

use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;
use Wordlift\Videoobject\Sitemap\Xml_Generator;

/**
 * Class Videoobject_Sitemap_Test
 * @group videoobject
 */
class Videoobject_Sitemap_Test extends \Wordlift_Videoobject_Unit_Test_Case {


	/**
	 * Video object sitemap should be available for all the public CPTS.
	 */
	public function test_videoobject_sitemap_should_be_enabled_on_custom_post_types() {
		// create a page and add videoobject data.
		$page                  = $this->factory()->post->create( array( 'post_type' => 'page' ) );
		$permalink             = get_permalink( $page );
		$storage               = Video_Storage_Factory::get_storage();
		$video                 = new Video();
		$video->id             = 'video_id';
		$video->name           = 'video_name';
		$video->description    = 'video_description';
		$video->views          = null;
		$video->embed_url      = null;
		$video->thumbnail_urls = array( 'thumbnail_url' );
		$storage->add_video( $page, $video );
		$xml             = Xml_Generator::get_xml_for_all_posts_with_videos();
		$expected_output = <<<EOF
   <url>
     <loc>${permalink}</loc>
     <video:video>
       <video:thumbnail_loc>thumbnail_url</video:thumbnail_loc>
       <video:title>video_name</video:title>
       <video:description>video_description</video:description>
       <video:live>no</video:live>
     </video:video>
   </url>
EOF;
		$this->assertEquals( self::remove_all_whitespaces( $xml ), self::remove_all_whitespaces( $expected_output ) );
	}

	public function test_when_optional_keys_not_present_dont_output_tags() {
		$post_id               = $this->factory()->post->create();
		$video                 = new Video();
		$video->id             = 'video_id';
		$video->name           = 'video_name';
		$video->description    = 'video_description';
		$video->views          = null;
		$video->embed_url      = null;
		$video->thumbnail_urls = array( 'thumbnail_url' );
		$sitemap               = Xml_Generator::get_xml_for_single_video( $video, $post_id );
		$permalink             = get_the_permalink( $post_id );
		$expected_output       = <<<EOF
   <url>
     <loc>${permalink}</loc>
     <video:video>
       <video:thumbnail_loc>thumbnail_url</video:thumbnail_loc>
       <video:title>video_name</video:title>
       <video:description>video_description</video:description>
       <video:live>no</video:live>
     </video:video>
   </url>
EOF;
		$this->assertEquals( self::remove_all_whitespaces( $sitemap ), self::remove_all_whitespaces( $expected_output ) );
	}


	public function test_should_print_full_video_sitemap() {
		$post_id            = $this->factory()->post->create();
		$video              = new Video();
		$video->id          = 'video_id';
		$video->name        = 'video_name';
		$video->description = 'video_description';
		$video->views       = 10;
		$video->embed_url   = 'embed_url';
		$video->content_url = 'content_url';
		$video->duration    = 'PT10S';

		$video->thumbnail_urls = array( 'thumbnail_url' );
		$sitemap               = Xml_Generator::get_xml_for_single_video( $video, $post_id );
		$permalink             = get_the_permalink( $post_id );
		$expected_output       = <<<EOF
   <url>
     <loc>${permalink}</loc>
     <video:video>
       <video:thumbnail_loc>thumbnail_url</video:thumbnail_loc>
       <video:title>video_name</video:title>
       <video:description>video_description</video:description>
       <video:content_loc>content_url</video:content_loc>
		<video:player_loc>embed_url</video:player_loc>
		<video:duration>10</video:duration>
		<video:view_count>10</video:view_count>
       <video:live>no</video:live>
     </video:video>
   </url>
EOF;
		$this->assertEquals( self::remove_all_whitespaces( $sitemap ), self::remove_all_whitespaces( $expected_output ) );
	}


}