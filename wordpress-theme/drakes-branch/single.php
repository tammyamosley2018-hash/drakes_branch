<?php
/**
 * Single announcement.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	db_page_banner( get_the_title() );
	?>

	<div class="container section">
		<article>
			<p class="entry-meta">
				<?php esc_html_e( 'Posted', 'drakes-branch' ); ?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
			</p>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure>
					<?php the_post_thumbnail( 'db-hero' ); ?>
				</figure>
			<?php endif; ?>

			<div class="prose entry-content">
				<?php the_content(); ?>
			</div>
		</article>

		<?php
		$db_prev = get_previous_post();
		$db_next = get_next_post();

		if ( $db_prev || $db_next ) :
			?>
			<nav class="pagination" aria-label="<?php esc_attr_e( 'More announcements', 'drakes-branch' ); ?>">
				<?php if ( $db_prev ) : ?>
					<a class="page-numbers" href="<?php echo esc_url( get_permalink( $db_prev ) ); ?>">
						<?php esc_html_e( 'Previous:', 'drakes-branch' ); ?>
						<?php echo esc_html( get_the_title( $db_prev ) ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $db_next ) : ?>
					<a class="page-numbers" href="<?php echo esc_url( get_permalink( $db_next ) ); ?>">
						<?php esc_html_e( 'Next:', 'drakes-branch' ); ?>
						<?php echo esc_html( get_the_title( $db_next ) ); ?>
					</a>
				<?php endif; ?>
			</nav>
		<?php endif; ?>
	</div>

	<?php
endwhile;

get_footer();
