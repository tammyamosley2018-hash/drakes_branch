<?php
/**
 * Fallback template, and the announcements listing.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

$db_posts_page = (int) get_option( 'page_for_posts' );
$db_title      = $db_posts_page ? get_the_title( $db_posts_page ) : __( 'Announcements', 'drakes-branch' );

db_page_banner( $db_title );
?>

<div class="container section">
	<?php if ( have_posts() ) : ?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article class="dated" id="post-<?php the_ID(); ?>">
				<p class="dated__date">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
						<?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
					</time>
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
				'screen_reader_text' => __( 'Announcements navigation', 'drakes-branch' ),
			)
		);
		?>

	<?php else : ?>
		<p><?php esc_html_e( 'There are no announcements right now.', 'drakes-branch' ); ?></p>
	<?php endif; ?>
</div>

<?php
get_footer();
