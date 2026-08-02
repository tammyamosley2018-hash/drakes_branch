<?php
/**
 * Shared layout for the five civic form pages.
 *
 * The page's editor content appears above the form, so staff can explain fees,
 * deadlines or open positions without touching a template.
 *
 * @package DrakesBranch
 *
 * @var array $args Passed from get_template_part(). Expects a 'type' key.
 */

defined( 'ABSPATH' ) || exit;

$db_type = isset( $args['type'] ) ? $args['type'] : 'contact';

$db_phone  = db_get( 'db_phone' );
$db_email  = db_get( 'db_email' );
$db_hours  = db_get( 'db_office_hours' );
$db_street = db_get( 'db_address_street' );
$db_city   = db_get( 'db_address_city' );
?>

<div class="container section">
	<div class="with-rail">

		<div class="with-rail__main">
			<?php if ( trim( get_the_content() ) ) : ?>
				<div class="prose entry-content">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>

			<div class="mt-5">
				<?php db_render_form( $db_type ); ?>
			</div>
		</div>

		<aside class="with-rail__rail" aria-label="<?php esc_attr_e( 'Other ways to reach us', 'drakes-branch' ); ?>">
			<div class="rail-card">
				<h2 class="rail-card__title"><?php esc_html_e( 'Rather not use this form?', 'drakes-branch' ); ?></h2>

				<p class="rail-card__meta">
					<?php esc_html_e( 'You can call, email, or come by the office. We will fill this in with you.', 'drakes-branch' ); ?>
				</p>

				<dl class="fact-list">
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

					<?php if ( $db_hours ) : ?>
						<div class="fact-list__row">
							<dt class="fact-list__key"><?php esc_html_e( 'Open', 'drakes-branch' ); ?></dt>
							<dd class="fact-list__val"><?php echo esc_html( $db_hours ); ?></dd>
						</div>
					<?php endif; ?>
				</dl>

				<?php if ( $db_street || $db_city ) : ?>
					<address class="rail-card__meta mt-4 mb-0">
						<?php if ( $db_street ) : ?>
							<?php echo esc_html( $db_street ); ?><br>
						<?php endif; ?>
						<?php echo esc_html( $db_city ); ?>
					</address>
				<?php endif; ?>
			</div>

			<div class="rail-card">
				<h2 class="rail-card__title"><?php esc_html_e( 'Your privacy', 'drakes-branch' ); ?></h2>
				<p class="rail-card__meta mb-0">
					<?php esc_html_e( 'What you send here goes to the town office. Requests to the town may be public records under Virginia law.', 'drakes-branch' ); ?>
				</p>
			</div>
		</aside>

	</div>
</div>
