<?php
// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
$articles = $this->get_last_wordlift_articles(); ?>
<div id='news_container'>
	<?php if ( ! empty( $articles['posts_data'] ) ) { ?>
		<?php
		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		foreach ( $articles['posts_data'] as $key => $item ) {
			?>
			<div>
				<a target="_blank"
				   href="<?php echo esc_url( $item['post_url'] ); ?>"><?php echo esc_html( $item['post_title'] ); ?></a>
				<p><?php echo wp_kses( $item['post_description'], wp_kses_allowed_html( 'post' ) ); ?></p>
			</div>
		<?php } ?>
	<?php } ?>
	<div>
		<a href="#" id="max_posts_count_3"
		   class="wl_more_posts"><?php echo esc_html__( 'More posts', 'wordlift' ); ?></a>
	</div>
</div>
