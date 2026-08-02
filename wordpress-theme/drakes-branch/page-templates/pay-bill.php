<?php
/**
 * Template Name: Pay my bill
 * Template Post Type: page
 *
 * Leads with the online payment button, then lists the other ways to pay, so
 * residents without a card or reliable internet are not left without a route.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

$db_pay    = db_get( 'db_pay_url' );
$db_phone  = db_get( 'db_phone' );
$db_hours  = db_get( 'db_office_hours' );
$db_street = db_get( 'db_address_street' );
$db_city   = db_get( 'db_address_city' );

while ( have_posts() ) :
	the_post();

	db_page_banner( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>

	<div class="container section">
		<div class="with-rail">

			<div class="with-rail__main">
				<?php if ( $db_pay ) : ?>
					<div class="rail-card">
						<h2 class="rail-card__title"><?php esc_html_e( 'Pay online', 'drakes-branch' ); ?></h2>
						<p class="rail-card__meta">
							<?php esc_html_e( 'Payments are handled by the town\'s payment provider on their own secure site. Have your account number ready.', 'drakes-branch' ); ?>
						</p>
						<p class="mb-0">
							<a class="btn" href="<?php echo esc_url( $db_pay ); ?>">
								<?php esc_html_e( 'Go to the payment site', 'drakes-branch' ); ?>
							</a>
						</p>
					</div>
				<?php endif; ?>

				<?php if ( trim( get_the_content() ) ) : ?>
					<div class="prose entry-content mt-5">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			</div>

			<aside class="with-rail__rail" aria-label="<?php esc_attr_e( 'Other ways to pay', 'drakes-branch' ); ?>">
				<div class="rail-card">
					<h2 class="rail-card__title"><?php esc_html_e( 'Other ways to pay', 'drakes-branch' ); ?></h2>

					<?php if ( $db_street || $db_city ) : ?>
						<p class="rail-card__meta">
							<strong><?php esc_html_e( 'By mail', 'drakes-branch' ); ?></strong><br>
							<?php esc_html_e( 'Send a check with the stub from your bill to:', 'drakes-branch' ); ?>
						</p>
						<address class="rail-card__meta">
							<?php if ( $db_street ) : ?>
								<?php echo esc_html( $db_street ); ?><br>
							<?php endif; ?>
							<?php echo esc_html( $db_city ); ?>
						</address>
					<?php endif; ?>

					<p class="rail-card__meta">
						<strong><?php esc_html_e( 'In person', 'drakes-branch' ); ?></strong><br>
						<?php if ( $db_hours ) : ?>
							<?php echo esc_html( $db_hours ); ?>
						<?php else : ?>
							<?php esc_html_e( 'During town office hours.', 'drakes-branch' ); ?>
						<?php endif; ?>
					</p>

					<?php if ( $db_phone ) : ?>
						<p class="rail-card__meta mb-0">
							<strong><?php esc_html_e( 'By phone', 'drakes-branch' ); ?></strong><br>
							<a href="<?php echo esc_url( db_tel_href( $db_phone ) ); ?>">
								<?php echo esc_html( $db_phone ); ?>
							</a>
						</p>
					<?php endif; ?>
				</div>

				<div class="rail-card">
					<h2 class="rail-card__title"><?php esc_html_e( 'Trouble paying?', 'drakes-branch' ); ?></h2>
					<p class="rail-card__meta mb-0">
						<?php esc_html_e( 'Call the town office before your due date. We would rather work something out with you than send a late notice.', 'drakes-branch' ); ?>
					</p>
				</div>
			</aside>

		</div>
	</div>

	<?php
endwhile;

get_footer();
