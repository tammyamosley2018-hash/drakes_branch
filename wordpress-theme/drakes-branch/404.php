<?php
/**
 * Page not found.
 *
 * An empty screen is an invitation to act, so this offers the routes people
 * were most likely looking for rather than apologising at length.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

db_page_banner( __( 'We could not find that page', 'drakes-branch' ) );

$db_phone = db_get( 'db_phone' );
?>

<div class="container section">
	<div class="prose">
		<p class="lead">
			<?php esc_html_e( 'The page may have moved, or the link may be out of date. Here is where most people are heading:', 'drakes-branch' ); ?>
		</p>
	</div>

	<div class="mt-5">
		<?php get_template_part( 'template-parts/service-cards' ); ?>
	</div>

	<div class="prose mt-5">
		<?php get_search_form(); ?>

		<?php if ( $db_phone ) : ?>
			<p class="mt-5">
				<?php esc_html_e( 'Still stuck? Call the town office at', 'drakes-branch' ); ?>
				<a href="<?php echo esc_url( db_tel_href( $db_phone ) ); ?>"><?php echo esc_html( $db_phone ); ?></a>.
			</p>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
