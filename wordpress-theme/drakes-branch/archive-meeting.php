<?php
/**
 * Meetings archive: upcoming meetings, then the record of past ones.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

db_page_banner(
	__( 'Meetings', 'drakes-branch' ),
	__( 'Agendas are posted before each meeting. Minutes and recordings are added once the council approves them.', 'drakes-branch' )
);

$db_upcoming = db_get_meetings( 'upcoming', 10 );
$db_has_next = $db_upcoming->post_count > 0;
$db_note     = db_get( 'db_meeting_note' );
?>

<div class="container section">

	<?php if ( $db_has_next ) : ?>
		<section aria-labelledby="upcoming-heading">
			<div class="section__head">
				<h2 id="upcoming-heading"><?php esc_html_e( 'Upcoming meetings', 'drakes-branch' ); ?></h2>
				<?php if ( $db_note ) : ?>
					<p class="text-meta"><?php echo esc_html( $db_note ); ?></p>
				<?php endif; ?>
			</div>

			<?php
			while ( $db_upcoming->have_posts() ) :
				$db_upcoming->the_post();
				$db_cancelled = db_meeting_field( 'cancelled' );
				?>
				<article class="dated">
					<p class="dated__date">
						<time datetime="<?php echo esc_attr( db_meeting_datetime_attr() ); ?>">
							<?php echo esc_html( db_meeting_date( 0, 'M j, Y' ) ); ?>
						</time>
					</p>

					<div>
						<h3 class="dated__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<?php if ( $db_cancelled ) : ?>
								<strong>&nbsp;&mdash; <?php esc_html_e( 'Cancelled', 'drakes-branch' ); ?></strong>
							<?php endif; ?>
						</h3>

						<p class="dated__text">
							<?php
							$db_bits = array_filter( array( db_meeting_time(), db_meeting_field( 'location' ) ) );
							echo esc_html( implode( ' · ', $db_bits ) );
							?>
						</p>

						<?php if ( db_meeting_field( 'agenda_url' ) ) : ?>
							<p class="mb-0">
								<?php db_document_link( db_meeting_field( 'agenda_url' ), __( 'Agenda', 'drakes-branch' ) ); ?>
							</p>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</section>
	<?php endif; ?>

	<section aria-labelledby="past-heading" class="<?php echo $db_has_next ? 'mt-5' : ''; ?>">
		<div class="section__head">
			<h2 id="past-heading"><?php esc_html_e( 'Past meetings', 'drakes-branch' ); ?></h2>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="table-wrap">
				<table class="table">
					<caption><?php esc_html_e( 'Town council meetings, most recent first.', 'drakes-branch' ); ?></caption>
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Date', 'drakes-branch' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Meeting', 'drakes-branch' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Agenda', 'drakes-branch' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Minutes', 'drakes-branch' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Recording', 'drakes-branch' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						while ( have_posts() ) :
							the_post();
							?>
							<tr>
								<th scope="row">
									<time datetime="<?php echo esc_attr( db_meeting_datetime_attr() ); ?>">
										<?php echo esc_html( db_meeting_date( 0, 'M j, Y' ) ); ?>
									</time>
								</th>
								<td>
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									<?php if ( db_meeting_field( 'cancelled' ) ) : ?>
										<br><?php esc_html_e( 'Cancelled', 'drakes-branch' ); ?>
									<?php endif; ?>
								</td>
								<td>
									<?php
									if ( db_meeting_field( 'agenda_url' ) ) {
										db_document_link( db_meeting_field( 'agenda_url' ), __( 'Agenda', 'drakes-branch' ) );
									} else {
										echo '<span aria-hidden="true">&mdash;</span><span class="screen-reader-text">'
											. esc_html__( 'Not available', 'drakes-branch' ) . '</span>';
									}
									?>
								</td>
								<td>
									<?php
									if ( db_meeting_field( 'minutes_url' ) ) {
										db_document_link( db_meeting_field( 'minutes_url' ), __( 'Minutes', 'drakes-branch' ) );
									} else {
										echo '<span aria-hidden="true">&mdash;</span><span class="screen-reader-text">'
											. esc_html__( 'Not available', 'drakes-branch' ) . '</span>';
									}
									?>
								</td>
								<td>
									<?php if ( db_meeting_field( 'video_url' ) ) : ?>
										<a href="<?php the_permalink(); ?>#recording">
											<?php esc_html_e( 'Watch', 'drakes-branch' ); ?>
											<span class="screen-reader-text">
												<?php
												/* translators: %s: meeting title */
												printf( esc_html__( 'the recording of %s', 'drakes-branch' ), esc_html( get_the_title() ) );
												?>
											</span>
										</a>
									<?php else : ?>
										<span aria-hidden="true">&mdash;</span>
										<span class="screen-reader-text"><?php esc_html_e( 'Not available', 'drakes-branch' ); ?></span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>

			<?php
			the_posts_pagination(
				array(
					'class'              => 'pagination',
					'mid_size'           => 1,
					'prev_text'          => __( 'Previous', 'drakes-branch' ),
					'next_text'          => __( 'Next', 'drakes-branch' ),
					'screen_reader_text' => __( 'Meetings navigation', 'drakes-branch' ),
				)
			);
			?>

		<?php else : ?>
			<p><?php esc_html_e( 'No past meetings have been posted yet.', 'drakes-branch' ); ?></p>
		<?php endif; ?>
	</section>

</div>

<?php
get_footer();
