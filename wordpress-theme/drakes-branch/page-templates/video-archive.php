<?php
/**
 * Template Name: Video archive
 * Template Post Type: page
 *
 * Every meeting that has a recording, newest first.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

$db_paged  = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$db_videos = new WP_Query(
	array(
		'post_type'      => 'meeting',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'paged'          => $db_paged,
		'meta_key'       => '_db_meeting_date',
		'orderby'        => 'meta_value',
		'order'          => 'DESC',
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'meta_query'     => array(
			array(
				'key'     => '_db_meeting_video_url',
				'value'   => '',
				'compare' => '!=',
			),
		),
	)
);

while ( have_posts() ) :
	the_post();

	db_page_banner( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>

	<div class="container section">
		<?php if ( trim( get_the_content() ) ) : ?>
			<div class="prose entry-content">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>

		<?php if ( $db_videos->have_posts() ) : ?>
			<ul class="grid grid--3 card-list mt-5">
				<?php
				while ( $db_videos->have_posts() ) :
					$db_videos->the_post();
					$db_youtube = db_youtube_id( db_meeting_field( 'video_url' ) );
					?>
					<li>
						<article class="card">
							<?php if ( $db_youtube ) : ?>
								<div class="video-embed">
									<iframe
										src="<?php echo esc_url( 'https://www.youtube-nocookie.com/embed/' . $db_youtube ); ?>"
										title="<?php echo esc_attr( sprintf( /* translators: %s: meeting title */ __( 'Video recording of %s', 'drakes-branch' ), get_the_title() ) ); ?>"
										allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
										allowfullscreen
										loading="lazy"></iframe>
								</div>
							<?php endif; ?>

							<div class="video-card__body">
								<p class="dated__date">
									<time datetime="<?php echo esc_attr( db_meeting_datetime_attr() ); ?>">
										<?php echo esc_html( db_meeting_date( 0, 'M j, Y' ) ); ?>
									</time>
								</p>

								<h2 class="card__title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h2>

								<?php if ( db_meeting_field( 'minutes_url' ) ) : ?>
									<p class="card__text">
										<?php db_document_link( db_meeting_field( 'minutes_url' ), __( 'Minutes', 'drakes-branch' ) ); ?>
									</p>
								<?php endif; ?>
							</div>
						</article>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php
			$db_links = paginate_links(
				array(
					'total'              => $db_videos->max_num_pages,
					'current'            => $db_paged,
					'prev_text'          => __( 'Previous', 'drakes-branch' ),
					'next_text'          => __( 'Next', 'drakes-branch' ),
					'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'drakes-branch' ) . ' </span>',
				)
			);

			if ( $db_links ) :
				?>
				<nav class="pagination" aria-label="<?php esc_attr_e( 'Recordings navigation', 'drakes-branch' ); ?>">
					<?php echo wp_kses_post( $db_links ); ?>
				</nav>
				<?php
			endif;

			wp_reset_postdata();
			?>

		<?php else : ?>
			<p class="mt-5"><?php esc_html_e( 'No recordings have been posted yet.', 'drakes-branch' ); ?></p>
		<?php endif; ?>
	</div>

	<?php
endwhile;

get_footer();
