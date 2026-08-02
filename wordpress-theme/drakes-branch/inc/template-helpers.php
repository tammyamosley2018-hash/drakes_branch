<?php
/**
 * Shared template helpers.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Page templates that render a civic form.
 *
 * @return array Map of template file to form type.
 */
function db_form_templates() {
	return array(
		'page-templates/form-contact.php'   => 'contact',
		'page-templates/form-permit.php'    => 'permit',
		'page-templates/form-complaint.php' => 'feedback',
		'page-templates/form-foia.php'      => 'foia',
		'page-templates/form-jobs.php'      => 'jobs',
	);
}

/**
 * Whether the current request renders a form, so form assets load only where
 * they are needed.
 *
 * @return bool
 */
function db_page_has_form() {
	if ( ! is_singular() ) {
		return false;
	}

	$template = get_page_template_slug();

	if ( $template && array_key_exists( $template, db_form_templates() ) ) {
		return true;
	}

	$post = get_post();

	return $post instanceof WP_Post && has_shortcode( $post->post_content, 'town_form' );
}

/**
 * The town seal, used in the header and footer.
 *
 * The disc is inscribed exactly in the square image, so a 50% border radius
 * in the stylesheet trims the corners without any transparency in the file.
 *
 * The seal is decorative here: it always sits beside the town's name in text,
 * so describing it again would make screen readers announce the town twice.
 *
 * @param string $class Extra CSS class.
 */
function db_the_mark( $class = '' ) {
	printf(
		'<img class="%1$s" src="%2$s" width="268" height="268" alt="" decoding="async">',
		esc_attr( $class ),
		esc_url( DB_THEME_URI . '/assets/images/town-seal.png' )
	);
}

/**
 * Line icons used on the service cards.
 *
 * Drawn on a 24 unit grid in currentColor so they inherit the surrounding
 * text colour and stay legible in high contrast mode. All are decorative:
 * every card already has a text label, so the icon is hidden from assistive
 * technology.
 *
 * @param string $name  Icon name.
 * @param string $class Extra CSS class.
 */
function db_icon( $name, $class = 'card__icon' ) {
	$paths = array(
		'bill'     => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/>',
		'permit'   => '<path d="M9 3h6a1 1 0 0 1 1 1v1H8V4a1 1 0 0 1 1-1z"/><path d="M16 5h2a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h2"/><path d="M9 12h6"/><path d="M9 16h4"/>',
		'report'   => '<path d="M12 3v13"/><path d="M12 20h.01"/><path d="M4 8h16"/><path d="M4 8a8 8 0 0 1 16 0"/>',
		'records'  => '<path d="M4 6a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M9 13h6"/>',
		'meetings' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18"/><path d="M8 3v4"/><path d="M16 3v4"/><path d="M8 15h3"/>',
		'jobs'     => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 12h18"/>',
		'contact'  => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="m2.5 6.5 9.5 6.5 9.5-6.5"/>',
		'video'    => '<rect x="2" y="5" width="14" height="14" rx="2"/><path d="m16 10 6-3v10l-6-3z"/>',
		'phone'    => '<path d="M5 3h4l2 5-2.5 1.5a12 12 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 5a2 2 0 0 1 2-2z"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return;
	}

	printf(
		'<svg class="%s" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none"'
			. ' stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">%s</svg>',
		esc_attr( $class ),
		$paths[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fixed set of literal path data.
	);
}

/**
 * Document icon for links that open a PDF.
 */
function db_doc_icon() {
	?>
	<svg viewBox="0 0 20 20" aria-hidden="true" focusable="false" fill="none"
		stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
		<path d="M11.5 2H5a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V6.5z"/>
		<path d="M11.5 2v4.5H16"/>
	</svg>
	<?php
}

/**
 * Breadcrumb trail for interior pages.
 *
 * Uses an ordered list because the trail is a sequence, and marks the last
 * item with aria-current rather than linking to the current page.
 */
function db_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$trail = array(
		array(
			'label' => __( 'Home', 'drakes-branch' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( is_singular( 'meeting' ) ) {
		$trail[] = array(
			'label' => __( 'Meetings', 'drakes-branch' ),
			'url'   => get_post_type_archive_link( 'meeting' ),
		);
		$trail[] = array( 'label' => get_the_title() );
	} elseif ( is_post_type_archive( 'meeting' ) ) {
		$trail[] = array( 'label' => __( 'Meetings', 'drakes-branch' ) );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$trail[] = array(
				'label' => get_the_title( $ancestor ),
				'url'   => get_permalink( $ancestor ),
			);
		}
		$trail[] = array( 'label' => get_the_title() );
	} elseif ( is_singular( 'post' ) ) {
		$trail[] = array(
			'label' => __( 'News', 'drakes-branch' ),
			'url'   => get_permalink( get_option( 'page_for_posts' ) ),
		);
		$trail[] = array( 'label' => get_the_title() );
	} elseif ( is_search() ) {
		$trail[] = array( 'label' => __( 'Search results', 'drakes-branch' ) );
	} elseif ( is_404() ) {
		$trail[] = array( 'label' => __( 'Page not found', 'drakes-branch' ) );
	} elseif ( is_archive() ) {
		$trail[] = array( 'label' => wp_strip_all_tags( get_the_archive_title() ) );
	}

	if ( count( $trail ) < 2 ) {
		return;
	}
	?>
	<nav class="breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'drakes-branch' ); ?>">
		<ol>
			<?php foreach ( $trail as $i => $crumb ) : ?>
				<li>
					<?php if ( ! empty( $crumb['url'] ) && $i < count( $trail ) - 1 ) : ?>
						<a href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['label'] ); ?></a>
					<?php else : ?>
						<span aria-current="page"><?php echo esc_html( $crumb['label'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/**
 * Renders the compact page banner used on interior pages.
 *
 * @param string $title Page title.
 * @param string $intro Optional standfirst.
 */
function db_page_banner( $title, $intro = '' ) {
	?>
	<div class="page-banner is-dark">
		<div class="container">
			<?php db_breadcrumbs(); ?>
			<h1 class="page-banner__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $intro ) : ?>
				<p class="page-banner__intro"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Formats a phone number as a tel: URI.
 *
 * @param string $phone Human-readable number.
 * @return string
 */
function db_tel_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * Prints a link that opens a document, with the file type in the accessible
 * name so it is announced before the link is followed.
 *
 * @param string $url   Document URL.
 * @param string $label Link text.
 */
function db_document_link( $url, $label ) {
	if ( ! $url ) {
		return;
	}

	$extension = strtoupper( pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	$extension = $extension ? $extension : 'PDF';
	?>
	<a class="doc-link" href="<?php echo esc_url( $url ); ?>">
		<?php db_doc_icon(); ?>
		<span>
			<?php echo esc_html( $label ); ?>
			<span class="screen-reader-text">
				<?php
				/* translators: %s: file type, for example PDF */
				echo esc_html( sprintf( __( '(%s document)', 'drakes-branch' ), $extension ) );
				?>
			</span>
		</span>
	</a>
	<?php
}
