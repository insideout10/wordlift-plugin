<?php

namespace Wordlift\Jsonld;

class Jsonld_Article_Wrapper {

    /**
     * @var Wordlift_Post_To_Jsonld_Converter
     */
	private $post_to_jsonld_converter;

    /**
     * @var Wordlift_Jsonld_Service
     */
    private $jsonld_service;

	public function __construct( $post_to_jsonld_converter, $jsonld_service ) {

		$this->post_to_jsonld_converter = $post_to_jsonld_converter;
		$this->jsonld_service           = $jsonld_service;

		add_filter( 'wl_after_get_jsonld', array( $this, 'after_get_jsonld' ), 10, 2 );

	}

	public function after_get_jsonld( $jsonld, $post_id ) {

        if ( ! is_array( $jsonld ) || count( $jsonld ) === 0 ) {
            return $jsonld;
        }

        // Copy the 1st array element
        $post_jsonld    = $jsonld[0];
        $post_jsonld_id = array_key_exists( '@id', $post_jsonld ) ? $post_jsonld['@id'] : false;

        if ( ! $post_jsonld_id ) {
            return $jsonld;
        }

        $mocked_data = $this->post_to_jsonld_converter->convert( $post_id );

        foreach ( $post_jsonld as $key => $value ) {
            if ( $key === '@id' ) {
                $post_jsonld[ $key ] = $post_jsonld_id . '#article';
            }

            if ( $key === '@type' ) {
                $post_jsonld[ $key ]          = 'Article';
                $post_jsonld['headline']      = $post_jsonld['name'];
                $post_jsonld['datePublished'] = $mocked_data['datePublished'];
                $post_jsonld['dateModified']  = $mocked_data['dateModified'];

                if ( isset( $mocked_data['image'] ) ) {
                    $post_jsonld['image'] = $mocked_data['image'];
                }
                if ( isset( $mocked_data['author'] ) ) {
                    $post_jsonld['author'] = $mocked_data['author'];
                }
                if ( isset( $mocked_data['publisher'] ) ) {
                    $post_jsonld['publisher'] = $mocked_data['publisher'];
                }

                $post_jsonld['about'] = array( '@id' => $post_jsonld_id );
                unset( $post_jsonld['name'] );
            }
        }

        // Add back the post jsonld to first position of array.
        array_unshift( $jsonld, $post_jsonld );

        return $jsonld;

	}

}