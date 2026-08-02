<?php
/**
 * A single meeting: when it is, the agenda, the minutes and the recording.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$db_cancelled = db_meeting_field( 'cancelled' );
	$db_video     = db_meeting_field( 'video_url' );
	$db_youtube   = db_youtube_id( $db_video );

	db_page_banner( get_the_title() );
	?>

	<div class="container section">
		<div class="with-rail">

			<div class="with-rail__main stack">

				<?php if ( $db_cancelled ) : ?>
					<div class="form-message form-message--error">
						<p><?php esc_html_e( 'This meeting was cancelled.', 'drakes-branch' ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( trim( get_the_content() ) ) : ?>
					<div class="prose entry-content">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>

				<?php if ( $db_youtube ) : ?>
					<section aria-labelledby="recording-heading" id="recording">
						<div class="section__head">
							<h2 id="recording-heading"><?php esc_html_e( 'Recording', 'drakes-branch' ); ?></h2>
						</div>

						<div class="video-embed">
							<iframe
								src="<?php echo esc_url( 'https://www.youtube-nocookie.com/embed/' . $db_youtube ); ?>"
								title="<?php echo esc_attr( sprintf( /* translators: %s: meeting title */ __( 'Video recording of %s', 'drakes-branch' ), get_the_title() ) ); ?>"
								allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
								allowfullscreen
								loading="lazy"></iframe>
						</div>

						<p class="text-meta mt-4">
							<?php esc_html_e( 'Captions are available in the video player.', 'drakes-branch' ); ?>
							<?php if ( db_meeting_field( 'transcript_url' ) ) : ?>
								<a href="<?php echo esc_url( db_meeting_field( 'transcript_url' ) ); ?>">
									<?php esc_html_e( 'Read the transcript instead', 'drakes-branch' ); ?>
								</a>.
							<?php endif; ?>
						</p>
					</section>
				<?php elseif ( $db_video ) : ?>
					<p>
						<a class="arrow-link" href="<?php echo esc_url( $db_video ); ?>">
							<?php esc_html_e( 'Watch the recording', 'drakes-branch' ); ?>
						</a>
					</p>
				<?php endif; ?>

			</div>

			<aside class="with-rail__rail" aria-label="<?php esc_attr_e( 'Meeting details', 'drakes-branch' ); ?>">
				<div class="rail-card rail-card--meeting">
					<h2 class="rail-card__title"><?php esc_html_e( 'Details', 'drakes-branch' ); ?></h2>

					<dl class="fact-list">
						<div class="fact-list__row">
							<dt class="fact-list__key"><?php esc_html_e( 'Date', 'drakes-branch' ); ?></dt>
							<dd class="fact-list__val">
								<time datetime="<?php echo esc_attr( db_meeting_datetime_attr() ); ?>">
									<?php echo esc_html( db_meeting_date( 0, 'M j, Y' ) ); ?>
								</time>
							</dd>
						</div>

						<?php if ( db_meeting_time() ) : ?>
							<div class="fact-list__row">
								<dt class="fact-list__key"><?php esc_html_e( 'Time', 'drakes-branch' ); ?></dt>
								<dd class="fact-list__val"><?php echo esc_html( db_meeting_time() ); ?></dd>
							</div>
						<?php endif; ?>

						<div class="fact-list__row">
							<dt class="fact-list__key"><?php esc_html_e( 'Where', 'drakes-branch' ); ?></dt>
							<dd class="fact-list__val"><?php echo esc_html( db_meeting_field( 'location' ) ); ?></dd>
						</div>
					</dl>

					<?php if ( db_meeting_field( 'agenda_url' ) || db_meeting_field( 'minutes_url' ) ) : ?>
						<ul class="rail-card__links">
							<?php if ( db_meeting_field( 'agenda_url' ) ) : ?>
								<li>
									<a href="<?php echo esc_url( db_meeting_field( 'agenda_url' ) ); ?>">
										<?php esc_html_e( 'Agenda', 'drakes-branch' ); ?>
										<span class="screen-reader-text"><?php esc_html_e( '(PDF document)', 'drakes-branch' ); ?></span>
									</a>
								</li>
							<?php endif; ?>

							<?php if ( db_meeting_field( 'minutes_url' ) ) : ?>
								<li>
									<a href="<?php echo esc_url( db_meeting_field( 'minutes_url' ) ); ?>">
										<?php esc_html_e( 'Minutes', 'drakes-branch' ); ?>
										<span class="screen-reader-text"><?php esc_html_e( '(PDF document)', 'drakes-branch' ); ?></span>
									</a>
								</li>
							<?php endif; ?>

							<li>
								<a href="<?php echo esc_url( get_post_type_archive_link( 'meeting' ) ); ?>">
									<?php esc_html_e( 'All meetings', 'drakes-branch' ); ?>
								</a>
							</li>
						</ul>
					<?php endif; ?>
				</div>
			</aside>

		</div>
	</div>

	<?php
endwhile;

get_footer();
