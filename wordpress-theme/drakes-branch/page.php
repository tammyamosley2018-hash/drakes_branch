<?php
/**
 * Default page template.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	// Only a hand-written excerpt becomes the standfirst; an auto-generated
	// one would just repeat the opening of the page.
	db_page_banner( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>

	<div class="container section">
		<div class="prose entry-content">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<nav class="pagination" aria-label="' . esc_attr__( 'Page sections', 'drakes-branch' ) . '">',
					'after'  => '</nav>',
				)
			);
			?>
		</div>
	</div>

	<?php
endwhile;

get_footer();
