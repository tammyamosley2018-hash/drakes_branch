<?php
/**
 * The civic rail: the next meeting and how to reach the office.
 *
 * On a phone this sits directly below the hero, because "when is the meeting"
 * and "is the office open" are the two things people most often open a town
 * website to find out.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

$db_next      = db_get_meetings( 'upcoming', 1 );
$db_phone     = db_get( 'db_phone' );
$db_email     = db_get( 'db_email' );
$db_hours     = db_get( 'db_office_hours' );
$db_note      = db_get( 'db_hours_note' );
$db_pay       = db_get( 'db_pay_url' );
$db_works     = db_get( 'db_works_phone' );
$db_emergency = db_get( 'db_emergency_note' );
?>

<div class="rail-card rail-card--meeting">
	<h2 class="rail-card__title"><?php esc_html_e( 'Next meeting', 'drakes-branch' ); ?></h2>

	<?php if ( $db_next->have_posts() ) : ?>
		<?php
		while ( $db_next->have_posts() ) :
			$db_next->the_post();
			$db_cancelled = db_meeting_field( 'cancelled' );
			$db_time      = db_meeting_time();
			?>
			<p class="rail-card__headline">
				<time datetime="<?php echo esc_attr( db_meeting_datetime_attr() ); ?>">
					<?php echo esc_html( db_meeting_date( 0, 'D, M j' ) ); ?>
				</time>
			</p>

			<p class="rail-card__meta">
				<?php if ( $db_cancelled ) : ?>
					<strong><?php esc_html_e( 'Cancelled', 'drakes-branch' ); ?></strong><br>
				<?php elseif ( $db_time ) : ?>
					<?php echo esc_html( $db_time ); ?><br>
				<?php endif; ?>
				<?php echo esc_html( db_meeting_field( 'location' ) ); ?>
			</p>

			<ul class="rail-card__links">
				<li>
					<a href="<?php the_permalink(); ?>">
						<?php esc_html_e( 'Meeting details', 'drakes-branch' ); ?>
					</a>
				</li>
				<?php if ( db_meeting_field( 'agenda_url' ) ) : ?>
					<li>
						<a href="<?php echo esc_url( db_meeting_field( 'agenda_url' ) ); ?>">
							<?php esc_html_e( 'Read the agenda', 'drakes-branch' ); ?>
							<span class="screen-reader-text"><?php esc_html_e( '(PDF document)', 'drakes-branch' ); ?></span>
						</a>
					</li>
				<?php endif; ?>
				<li>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'meeting' ) ); ?>">
						<?php esc_html_e( 'All meetings and minutes', 'drakes-branch' ); ?>
					</a>
				</li>
			</ul>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>

	<?php else : ?>
		<p class="rail-card__meta">
			<?php esc_html_e( 'No meeting is scheduled yet. Check back soon, or call the town office.', 'drakes-branch' ); ?>
		</p>
		<ul class="rail-card__links">
			<li>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'meeting' ) ); ?>">
					<?php esc_html_e( 'Past meetings and minutes', 'drakes-branch' ); ?>
				</a>
			</li>
		</ul>
	<?php endif; ?>
</div>

<?php if ( $db_hours || $db_phone || $db_email ) : ?>
	<div class="rail-card">
		<h2 class="rail-card__title"><?php esc_html_e( 'Town office', 'drakes-branch' ); ?></h2>

		<dl class="fact-list">
			<?php if ( $db_hours ) : ?>
				<div class="fact-list__row">
					<dt class="fact-list__key"><?php esc_html_e( 'Open', 'drakes-branch' ); ?></dt>
					<dd class="fact-list__val"><?php echo esc_html( $db_hours ); ?></dd>
				</div>
			<?php endif; ?>

			<?php if ( $db_phone ) : ?>
				<div class="fact-list__row">
					<dt class="fact-list__key"><?php esc_html_e( 'Phone', 'drakes-branch' ); ?></dt>
					<dd class="fact-list__val">
						<a href="<?php echo esc_url( db_tel_href( $db_phone ) ); ?>">
							<?php echo esc_html( $db_phone ); ?>
						</a>
					</dd>
				</div>
			<?php endif; ?>

			<?php if ( $db_email ) : ?>
				<div class="fact-list__row">
					<dt class="fact-list__key"><?php esc_html_e( 'Email', 'drakes-branch' ); ?></dt>
					<dd class="fact-list__val">
						<a href="mailto:<?php echo esc_attr( $db_email ); ?>">
							<?php esc_html_e( 'Send a message', 'drakes-branch' ); ?>
						</a>
					</dd>
				</div>
			<?php endif; ?>

			<?php if ( $db_works ) : ?>
				<div class="fact-list__row">
					<dt class="fact-list__key"><?php esc_html_e( 'Trash', 'drakes-branch' ); ?></dt>
					<dd class="fact-list__val">
						<a href="<?php echo esc_url( db_tel_href( $db_works ) ); ?>">
							<?php echo esc_html( $db_works ); ?>
						</a>
					</dd>
				</div>
			<?php endif; ?>
		</dl>

		<?php if ( $db_note ) : ?>
			<p class="rail-card__meta mt-4 mb-0"><?php echo esc_html( $db_note ); ?></p>
		<?php endif; ?>

		<?php if ( $db_pay ) : ?>
			<p class="mt-4 mb-0">
				<a class="btn btn--secondary btn--wide" href="<?php echo esc_url( $db_pay ); ?>">
					<?php esc_html_e( 'Pay my bill', 'drakes-branch' ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ( $db_emergency ) : ?>
	<?php // Water and sewer emergencies do not wait for office hours. ?>
	<div class="rail-card rail-card--urgent">
		<h2 class="rail-card__title"><?php esc_html_e( 'After hours', 'drakes-branch' ); ?></h2>
		<p class="rail-card__meta mb-0">
			<?php echo esc_html( wp_strip_all_tags( $db_emergency ) ); ?>
		</p>
	</div>
<?php endif; ?>
