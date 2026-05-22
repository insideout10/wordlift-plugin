<?php
/**
 * Removes WordLift text annotations that split words.
 *
 * @since 3.55.0
 */

namespace Wordlift\Cleanup;

class Broken_Word_Boundary_Annotation_Remover {

	/**
	 * Remove broken annotations from a content fragment.
	 *
	 * @param string $content The post content.
	 *
	 * @return array{changed:bool,content:string,removed_count:int}
	 */
	public function remove( $content ) {
		$replacements = $this->find_replacements( $content );

		if ( empty( $replacements ) ) {
			return array(
				'changed'       => false,
				'content'       => $content,
				'removed_count' => 0,
			);
		}

		// Apply replacements from the end so earlier offsets remain valid.
		for ( $i = count( $replacements ) - 1; 0 <= $i; --$i ) {
			$replacement = $replacements[ $i ];
			$content     = substr_replace(
				$content,
				$replacement['replacement'],
				$replacement['start'],
				$replacement['length']
			);
		}

		return array(
			'changed'       => true,
			'content'       => $content,
			'removed_count' => count( $replacements ),
		);
	}

	/**
	 * Check whether a content fragment contains broken annotations.
	 *
	 * @param string $content The post content.
	 *
	 * @return bool True when at least one broken annotation is found.
	 */
	public function has_broken_annotations( $content ) {
		$replacements = $this->find_replacements( $content, 1 );

		return ! empty( $replacements );
	}

	/**
	 * Find replacements for broken annotation spans.
	 *
	 * @param string   $content The post content.
	 * @param int|null $limit Optional replacement limit.
	 *
	 * @return array<int,array{start:int,length:int,replacement:string}>
	 */
	private function find_replacements( $content, $limit = null ) {
		$replacements = array();
		$offset       = 0;

		while ( preg_match( '/<span\b[^>]*>/i', $content, $matches, PREG_OFFSET_CAPTURE, $offset ) ) {
			$opening_tag = $matches[0][0];
			$start       = $matches[0][1];
			$open_end    = $start + strlen( $opening_tag );

			$close = $this->find_matching_span_close( $content, $open_end );
			if ( ! $this->is_textannotation_span( $opening_tag ) || ! isset( $close ) ) {
				$offset = $open_end;
				continue;
			}

			$inner = substr( $content, $open_end, $close['start'] - $open_end );
			if ( $this->splits_word_boundary( $content, $start, $inner, $close['end'] ) ) {
				$replacements[] = array(
					'start'       => $start,
					'length'      => $close['end'] - $start,
					'replacement' => $inner,
				);

				if ( isset( $limit ) && count( $replacements ) >= $limit ) {
					break;
				}

				$offset = $close['end'];
				continue;
			}

			$offset = $open_end;
		}

		return $replacements;
	}

	/**
	 * Find the matching closing span, tracking nested spans.
	 *
	 * @param string $content The post content.
	 * @param int    $offset Offset after the opening span.
	 *
	 * @return array{start:int,end:int}|null
	 */
	private function find_matching_span_close( $content, $offset ) {
		$depth = 1;

		while ( preg_match( '/<\/?span\b[^>]*>/i', $content, $matches, PREG_OFFSET_CAPTURE, $offset ) ) {
			$tag   = $matches[0][0];
			$start = $matches[0][1];
			$end   = $start + strlen( $tag );

			if ( 0 === stripos( $tag, '</span' ) ) {
				--$depth;
				if ( 0 === $depth ) {
					return array(
						'start' => $start,
						'end'   => $end,
					);
				}
			} elseif ( ! preg_match( '/\/\s*>$/', $tag ) ) {
				++$depth;
			}

			$offset = $end;
		}

		return null;
	}

	/**
	 * Check whether the opening span is a WordLift text annotation.
	 *
	 * @param string $opening_tag The opening span tag.
	 *
	 * @return bool True when the span has the textannotation class token.
	 */
	private function is_textannotation_span( $opening_tag ) {
		$attributes = $this->parse_attributes( $opening_tag );

		if ( ! isset( $attributes['class'] ) ) {
			return false;
		}

		$classes = preg_split( '/\s+/', trim( $attributes['class'] ) );

		return in_array( 'textannotation', $classes, true );
	}

	/**
	 * Parse attributes from a tag.
	 *
	 * @param string $tag The tag.
	 *
	 * @return array<string,string>
	 */
	private function parse_attributes( $tag ) {
		$attributes = array();

		if ( ! preg_match_all( '/([^\s\/=<>]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+))/i', $tag, $matches, PREG_SET_ORDER ) ) {
			return $attributes;
		}

		foreach ( $matches as $match ) {
			$value = '';
			if ( isset( $match[2] ) && '' !== $match[2] ) {
				$value = $match[2];
			} elseif ( isset( $match[3] ) && '' !== $match[3] ) {
				$value = $match[3];
			} elseif ( isset( $match[4] ) ) {
				$value = $match[4];
			}

			$attributes[ strtolower( $match[1] ) ] = $value;
		}

		return $attributes;
	}

	/**
	 * Check if the annotation boundaries split a word.
	 *
	 * @param string $content The full content.
	 * @param int    $start Opening span start.
	 * @param string $inner Inner span HTML.
	 * @param int    $end Closing span end.
	 *
	 * @return bool True when the start or end boundary splits a word.
	 */
	private function splits_word_boundary( $content, $start, $inner, $end ) {
		$previous = $this->last_visible_char( substr( $content, 0, $start ) );
		$first    = $this->first_visible_char( $inner );
		$last     = $this->last_visible_char( $inner );
		$next     = $this->first_visible_char( substr( $content, $end ) );

		return ( $this->is_word_char( $previous ) && $this->is_word_char( $first ) )
			|| ( $this->is_word_char( $last ) && $this->is_word_char( $next ) );
	}

	/**
	 * Get the first visible character from an HTML fragment.
	 *
	 * @param string $html HTML fragment.
	 *
	 * @return string|null
	 */
	private function first_visible_char( $html ) {
		$text = $this->visible_text( $html );

		if ( '' === $text ) {
			return null;
		}

		return preg_match( '/./us', $text, $matches ) ? $matches[0] : null;
	}

	/**
	 * Get the last visible character from an HTML fragment.
	 *
	 * @param string $html HTML fragment.
	 *
	 * @return string|null
	 */
	private function last_visible_char( $html ) {
		$text = $this->visible_text( $html );

		if ( '' === $text ) {
			return null;
		}

		return preg_match( '/.$/us', $text, $matches ) ? $matches[0] : null;
	}

	/**
	 * Convert an HTML fragment to visible text for boundary inspection.
	 *
	 * @param string $html HTML fragment.
	 *
	 * @return string
	 */
	private function visible_text( $html ) {
		if ( function_exists( 'wp_strip_all_tags' ) ) {
			return html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
		return html_entity_decode( strip_tags( $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}

	/**
	 * Check whether a character is part of a word.
	 *
	 * @param string|null $char The character.
	 *
	 * @return bool True for letters, numbers and underscores.
	 */
	private function is_word_char( $char ) {
		return isset( $char ) && 1 === preg_match( '/^[\p{L}\p{N}_]$/u', $char );
	}
}
