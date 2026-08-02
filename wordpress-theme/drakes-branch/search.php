<?php
/**
 * Search results.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

$db_query = get_search_query();
$db_found = (int) $GLOBALS['wp_query']->found_posts;

db_page_banner( __( 'Search results', 'drakes-branch' ) );
?>

<div class="container section">
	<?php // Announced so screen reader users hear the result count. ?>
	<p class="entry-meta" role="status">
		<?php
		if ( $db_found ) {
			printf(
				/* translators: 1: number of results, 2: search term */
				esc_html( _n( '%1$s result for “%2$s”', '%1$s results for “%2$s”', $db_found, 'drakes-branch' ) ),
				esc_html( number_format_i18n( $db_found ) ),
				esc_html( $db_query )
			);
		} else {
			printf(
				/* translators: %s: search term */
				esc_html__( 'Nothing found for “%s”.', 'drakes-branch' ),
				esc_html( $db_query )
			);
		}
		?>
	</p>

	<?php if ( have_posts() ) : ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article class="dated">
				<p class="dated__date">
					<?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?>
				</p>
				<div>
					<h2 class="dated__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h2>
					<p class="dated__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
				</div>
			</article>
		<?php endwhile; ?>

		<?php
		the_posts_pagination(
			array(
				'class'              => 'pagination',
				'mid_size'           => 1,
				'prev_text'          => __( 'Previous', 'drakes-branch' ),
				'next_text'          => __( 'Next', 'drakes-branch' ),
				'screen_reader_text' => __( 'Search results navigation', 'drakes-branch' ),
			)
		);
		?>

	<?php else : ?>
		<div class="prose">
			<p><?php esc_html_e( 'Try a different word, or use one of these:', 'drakes-branch' ); ?></p>
			<ul>
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'meeting' ) ); ?>"><?php esc_html_e( 'Meetings and minutes', 'drakes-branch' ); ?></a></li>
				<?php if ( get_page_by_path( 'contact' ) ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>"><?php esc_html_e( 'Contact the town office', 'drakes-branch' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="mt-5">
			<?php get_search_form(); ?>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
