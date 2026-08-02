<?php
/**
 * Site header.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

$db_pay_url = db_get( 'db_pay_url' );
$db_phone   = db_get( 'db_phone' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to main content', 'drakes-branch' ); ?></a>

<?php if ( $db_pay_url || $db_phone ) : ?>
	<div class="utility-bar is-dark">
		<div class="container utility-bar__inner">
			<?php if ( $db_phone ) : ?>
				<a href="<?php echo esc_url( db_tel_href( $db_phone ) ); ?>">
					<span class="screen-reader-text"><?php esc_html_e( 'Call the town office at', 'drakes-branch' ); ?></span>
					<?php echo esc_html( $db_phone ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $db_pay_url ) : ?>
				<a href="<?php echo esc_url( $db_pay_url ); ?>">
					<?php esc_html_e( 'Pay my bill', 'drakes-branch' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<header class="site-header is-dark" role="banner">
	<div class="container">
		<div class="site-header__bar">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php db_the_mark( 'site-brand__mark' ); ?>
					<span>
						<span class="site-brand__name"><?php bloginfo( 'name' ); ?></span>
						<span class="site-brand__place"><?php esc_html_e( 'Charlotte County, Virginia', 'drakes-branch' ); ?></span>
					</span>
				</a>
			<?php endif; ?>

			<button type="button" class="nav-toggle" aria-expanded="false"
				aria-controls="primary-navigation">
				<span class="nav-toggle__bars" aria-hidden="true"></span>
				<?php esc_html_e( 'Menu', 'drakes-branch' ); ?>
			</button>

			<nav class="primary-nav" id="primary-navigation"
				aria-label="<?php esc_attr_e( 'Main', 'drakes-branch' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'primary-nav__list',
						'depth'          => 2,
						'walker'         => new DB_Nav_Walker(),
						'fallback_cb'    => 'db_primary_menu_fallback',
					)
				);
				?>
			</nav>
		</div>
	</div>
</header>

<main id="main" tabindex="-1">
