<?php
/**
 * Site footer.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

$db_street   = db_get( 'db_address_street' );
$db_city     = db_get( 'db_address_city' );
$db_phone    = db_get( 'db_phone' );
$db_fax      = db_get( 'db_fax' );
$db_email    = db_get( 'db_email' );
$db_hours    = db_get( 'db_office_hours' );
$db_note     = db_get( 'db_hours_note' );
$db_facebook = db_get( 'db_facebook_url' );
$db_footer   = db_get( 'db_footer_note' );
$db_works    = db_get( 'db_works_phone' );
$db_works_hr = db_get( 'db_works_hours' );
$db_emerg    = db_get( 'db_emergency_note' );
?>
</main>

<footer class="site-footer is-dark" role="contentinfo">
	<div class="container">
		<div class="footer-grid">

			<div>
				<h2><?php esc_html_e( 'Town office', 'drakes-branch' ); ?></h2>

				<?php if ( $db_street || $db_city ) : ?>
					<address class="footer-address">
						<?php if ( $db_street ) : ?>
							<?php echo esc_html( $db_street ); ?><br>
						<?php endif; ?>
						<?php echo esc_html( $db_city ); ?>
					</address>
				<?php endif; ?>

				<ul class="footer-list">
					<?php if ( $db_phone ) : ?>
						<li>
							<a href="<?php echo esc_url( db_tel_href( $db_phone ) ); ?>">
								<?php
								/* translators: %s: phone number */
								printf( esc_html__( 'Phone: %s', 'drakes-branch' ), esc_html( $db_phone ) );
								?>
							</a>
						</li>
					<?php endif; ?>

					<?php if ( $db_email ) : ?>
						<li>
							<a href="mailto:<?php echo esc_attr( $db_email ); ?>">
								<?php echo esc_html( $db_email ); ?>
							</a>
						</li>
					<?php endif; ?>

					<?php if ( $db_fax ) : ?>
						<li>
							<span class="footer-list__static">
								<?php
								/* translators: %s: fax number */
								printf( esc_html__( 'Fax: %s', 'drakes-branch' ), esc_html( $db_fax ) );
								?>
							</span>
						</li>
					<?php endif; ?>
				</ul>
			</div>

			<div>
				<h2><?php esc_html_e( 'Office hours', 'drakes-branch' ); ?></h2>
				<?php if ( $db_hours ) : ?>
					<p class="footer-address"><?php echo esc_html( $db_hours ); ?></p>
				<?php endif; ?>
				<?php if ( $db_note ) : ?>
					<p class="footer-address"><?php echo esc_html( $db_note ); ?></p>
				<?php endif; ?>

				<?php if ( $db_works ) : ?>
					<h2 class="mt-5"><?php esc_html_e( 'Public Works', 'drakes-branch' ); ?></h2>
					<p class="footer-address">
						<?php if ( $db_works_hr ) : ?>
							<?php echo esc_html( $db_works_hr ); ?><br>
						<?php endif; ?>
						<a href="<?php echo esc_url( db_tel_href( $db_works ) ); ?>">
							<?php echo esc_html( $db_works ); ?>
						</a><br>
						<?php esc_html_e( 'Call about missed trash collection.', 'drakes-branch' ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $db_facebook ) : ?>
					<ul class="footer-list">
						<li>
							<a href="<?php echo esc_url( $db_facebook ); ?>">
								<?php esc_html_e( 'Town Facebook page', 'drakes-branch' ); ?>
							</a>
						</li>
					</ul>
				<?php endif; ?>
			</div>

			<nav aria-label="<?php esc_attr_e( 'Footer', 'drakes-branch' ); ?>">
				<h2><?php esc_html_e( 'Town services', 'drakes-branch' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>

			<nav aria-label="<?php esc_attr_e( 'Policies', 'drakes-branch' ); ?>">
				<h2><?php esc_html_e( 'About this site', 'drakes-branch' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'menu_class'     => 'footer-list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>

		</div>

		<?php if ( $db_emerg ) : ?>
			<p class="footer-emergency">
				<strong><?php esc_html_e( 'After hours:', 'drakes-branch' ); ?></strong>
				<?php echo esc_html( wp_strip_all_tags( $db_emerg ) ); ?>
			</p>
		<?php endif; ?>

		<div class="footer-bottom">
			<p>
				<?php
				/* translators: %1$s: current year, %2$s: site name */
				printf(
					esc_html__( '© %1$s %2$s', 'drakes-branch' ),
					esc_html( wp_date( 'Y' ) ),
					esc_html( get_bloginfo( 'name' ) )
				);
				?>
			</p>

			<?php if ( $db_footer ) : ?>
				<p><?php echo esc_html( wp_strip_all_tags( $db_footer ) ); ?></p>
			<?php endif; ?>

			<p>
				<?php esc_html_e( 'Built to meet WCAG 2.1 Level AA. Tell us if something on this site is hard to use.', 'drakes-branch' ); ?>
			</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
