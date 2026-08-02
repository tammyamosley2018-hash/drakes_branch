<?php
/**
 * Accessible navigation walker.
 *
 * Implements the WAI disclosure navigation pattern: a submenu is opened by a
 * real button with aria-expanded, and the parent link keeps working as a link.
 * Hover alone never reveals a submenu, so the menu is usable by keyboard, by
 * touch, and by switch devices.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Primary navigation walker.
 */
class DB_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * ID of the item currently being walked, used to link the toggle button
	 * to the submenu it controls.
	 *
	 * @var int
	 */
	protected $current_item_id = 0;

	/**
	 * Opens a submenu list.
	 *
	 * Also closes the .nav-row wrapper opened in start_el, so the link and its
	 * toggle button sit on one row above the submenu.
	 *
	 * @param string   $output Menu markup, passed by reference.
	 * @param int      $depth  Current depth.
	 * @param stdClass $args   Menu arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent = str_repeat( "\t", $depth );

		$output .= "\n" . $indent . '</div>' . "\n";
		$output .= sprintf(
			'%s<ul class="sub-menu" id="submenu-%d" hidden>' . "\n",
			$indent,
			$this->current_item_id
		);
	}

	/**
	 * Closes a submenu list.
	 *
	 * @param string   $output Menu markup, passed by reference.
	 * @param int      $depth  Current depth.
	 * @param stdClass $args   Menu arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= str_repeat( "\t", $depth ) . "</ul>\n";
	}

	/**
	 * Outputs a menu item.
	 *
	 * @param string   $output Menu markup, passed by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Current depth.
	 * @param stdClass $args   Menu arguments.
	 * @param int      $id     Menu item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$this->current_item_id = (int) $item->ID;

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$has_children = in_array( 'menu-item-has-children', $classes, true );

		$class_names = implode(
			' ',
			array_filter( apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) )
		);

		$indent  = str_repeat( "\t", $depth );
		$output .= $indent . '<li class="' . esc_attr( $class_names ) . '">';

		if ( $has_children ) {
			$output .= '<div class="nav-row">';
		}

		$atts = array(
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
			'href'   => ! empty( $item->url ) ? $item->url : '',
		);

		// Mark the current page for assistive technology. The stylesheet also
		// shows it with a rule, so the state is not conveyed by colour alone.
		if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current_page_item', $classes, true ) ) {
			$atts['aria-current'] = 'page';
		}

		// Warn before leaving the site, and never open a new tab silently.
		if ( '_blank' === $atts['target'] ) {
			$atts['rel'] = trim( $atts['rel'] . ' noopener' );
		}

		$attributes = '';
		foreach ( apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth ) as $attr => $value ) {
			if ( '' === $value || false === $value ) {
				continue;
			}
			$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$output .= '<a' . $attributes . '>' . esc_html( $title ) . '</a>';

		if ( $has_children ) {
			$output .= sprintf(
				'<button type="button" class="submenu-toggle" aria-expanded="false" aria-controls="submenu-%1$d">'
					. '<span class="screen-reader-text">%2$s</span>'
					. '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false">'
					. '<path d="M2 5l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
					. '</svg>'
					. '</button>',
				(int) $item->ID,
				/* translators: %s: name of the parent menu item */
				esc_html( sprintf( __( 'Show submenu for %s', 'drakes-branch' ), $title ) )
			);
		}
	}

	/**
	 * Closes a menu item.
	 *
	 * @param string   $output Menu markup, passed by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Current depth.
	 * @param stdClass $args   Menu arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		// Items without children never opened a .nav-row, so only close the
		// list item here. Parents had their wrapper closed in start_lvl.
		if ( ! in_array( 'menu-item-has-children', $classes, true ) ) {
			$output .= "</li>\n";
			return;
		}

		$output .= "</li>\n";
	}
}

/**
 * Shown when no menu has been assigned to the primary location yet.
 *
 * Keeps the site navigable on a fresh install instead of printing the core
 * "list of pages" fallback, which does not match the theme markup.
 */
function db_primary_menu_fallback() {
	$links = array(
		home_url( '/' )                    => __( 'Home', 'drakes-branch' ),
		home_url( '/about/' )              => __( 'About', 'drakes-branch' ),
		home_url( '/services/' )           => __( 'Services', 'drakes-branch' ),
		home_url( '/meetings/' )           => __( 'Meetings', 'drakes-branch' ),
		home_url( '/forms/' )              => __( 'Forms', 'drakes-branch' ),
		home_url( '/contact/' )            => __( 'Contact', 'drakes-branch' ),
	);

	echo '<ul class="primary-nav__list">';

	foreach ( $links as $url => $label ) {
		printf(
			'<li class="menu-item"><a href="%1$s"%2$s>%3$s</a></li>',
			esc_url( $url ),
			untrailingslashit( $url ) === untrailingslashit( home_url( add_query_arg( array() ) ) ) ? ' aria-current="page"' : '',
			esc_html( $label )
		);
	}

	echo '</ul>';
}
