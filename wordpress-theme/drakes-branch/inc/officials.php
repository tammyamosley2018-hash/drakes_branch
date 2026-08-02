<?php
/**
 * Town officials.
 *
 * One record per person, grouped into bodies (council, commissions, staff).
 * A person can sit on more than one body and is entered once.
 *
 * These records have no public single view. Nobody needs a URL for one council
 * member, and a page per person would be mostly empty. They are a directory,
 * rendered together by the officials page template.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the official post type and the body taxonomy.
 */
function db_register_officials() {
	register_post_type(
		'db_official',
		array(
			'labels'             => array(
				'name'               => __( 'Officials', 'drakes-branch' ),
				'singular_name'      => __( 'Official', 'drakes-branch' ),
				'add_new'            => __( 'Add person', 'drakes-branch' ),
				'add_new_item'       => __( 'Add person', 'drakes-branch' ),
				'edit_item'          => __( 'Edit person', 'drakes-branch' ),
				'new_item'           => __( 'New person', 'drakes-branch' ),
				'search_items'       => __( 'Search officials', 'drakes-branch' ),
				'not_found'          => __( 'No officials yet.', 'drakes-branch' ),
				'not_found_in_trash' => __( 'No officials in the trash.', 'drakes-branch' ),
				'all_items'          => __( 'All officials', 'drakes-branch' ),
			),
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'publicly_queryable' => false,
			'has_archive'        => false,
			'exclude_from_search' => true,
			'menu_icon'          => 'dashicons-groups',
			'menu_position'      => 22,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'show_in_rest'       => true,
			'capability_type'    => 'post',
		)
	);

	register_taxonomy(
		'db_body',
		'db_official',
		array(
			'labels'            => array(
				'name'          => __( 'Bodies', 'drakes-branch' ),
				'singular_name' => __( 'Body', 'drakes-branch' ),
				'add_new_item'  => __( 'Add body', 'drakes-branch' ),
				'menu_name'     => __( 'Bodies', 'drakes-branch' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'db_register_officials' );

/**
 * Registers the per-person fields.
 */
function db_register_official_meta() {
	$fields = array(
		'role'      => 'sanitize_text_field',
		'phone'     => 'sanitize_text_field',
		'term_ends' => 'sanitize_text_field',
		'email'     => 'sanitize_email',
	);

	foreach ( $fields as $field => $sanitize ) {
		register_post_meta(
			'db_official',
			'_db_official_' . $field,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => $sanitize,
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'db_register_official_meta' );

/**
 * Adds the person details meta box.
 */
function db_official_meta_box() {
	add_meta_box(
		'db-official-details',
		__( 'Details', 'drakes-branch' ),
		'db_render_official_meta_box',
		'db_official',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'db_official_meta_box' );

/**
 * Renders the person details meta box.
 *
 * @param WP_Post $post Current post.
 */
function db_render_official_meta_box( $post ) {
	wp_nonce_field( 'db_save_official', 'db_official_nonce' );

	$value = function ( $field ) use ( $post ) {
		return get_post_meta( $post->ID, '_db_official_' . $field, true );
	};
	?>
	<style>
		.db-meta-grid { display: grid; gap: 16px 24px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); margin-top: 8px; }
		.db-meta-field label { display: block; font-weight: 600; margin-bottom: 4px; }
		.db-meta-field input[type="text"], .db-meta-field input[type="email"] { width: 100%; }
		.db-meta-field p { margin: 4px 0 0; color: #646970; font-size: 12px; }
		.db-meta-full { grid-column: 1 / -1; }
	</style>

	<div class="db-meta-grid">
		<div class="db-meta-field db-meta-full">
			<label for="db_official_role"><?php esc_html_e( 'Role', 'drakes-branch' ); ?></label>
			<input type="text" id="db_official_role" name="db_official_role"
				value="<?php echo esc_attr( $value( 'role' ) ); ?>"
				placeholder="<?php esc_attr_e( 'Mayor', 'drakes-branch' ); ?>">
			<p><?php esc_html_e( 'Leave blank for an ordinary member. Use it for Mayor, Clerk, Secretary, and similar.', 'drakes-branch' ); ?></p>
		</div>

		<div class="db-meta-field">
			<label for="db_official_email"><?php esc_html_e( 'Public email', 'drakes-branch' ); ?></label>
			<input type="email" id="db_official_email" name="db_official_email"
				value="<?php echo esc_attr( $value( 'email' ) ); ?>">
			<p><?php esc_html_e( 'Only if this person takes enquiries directly. Otherwise leave blank and residents use the town office.', 'drakes-branch' ); ?></p>
		</div>

		<div class="db-meta-field">
			<label for="db_official_phone"><?php esc_html_e( 'Public phone', 'drakes-branch' ); ?></label>
			<input type="text" id="db_official_phone" name="db_official_phone"
				value="<?php echo esc_attr( $value( 'phone' ) ); ?>">
		</div>

		<div class="db-meta-field db-meta-full">
			<label for="db_official_term_ends"><?php esc_html_e( 'Term ends', 'drakes-branch' ); ?></label>
			<input type="text" id="db_official_term_ends" name="db_official_term_ends"
				value="<?php echo esc_attr( $value( 'term_ends' ) ); ?>"
				placeholder="<?php esc_attr_e( 'December 2028', 'drakes-branch' ); ?>">
			<p><?php esc_html_e( 'Optional. Shown so residents can see when a seat is next up.', 'drakes-branch' ); ?></p>
		</div>
	</div>

	<p>
		<strong><?php esc_html_e( 'Photograph:', 'drakes-branch' ); ?></strong>
		<?php esc_html_e( 'use the Featured image box. Without one, the person\'s initials are shown instead. Only use a real photograph, taken with that person\'s knowledge.', 'drakes-branch' ); ?>
	</p>
	<?php
}

/**
 * Saves the person details.
 *
 * @param int $post_id Post being saved.
 */
function db_save_official_meta( $post_id ) {
	if ( ! isset( $_POST['db_official_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['db_official_nonce'] ) ), 'db_save_official' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array( 'role', 'phone', 'term_ends' ) as $field ) {
		$key   = 'db_official_' . $field;
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		update_post_meta( $post_id, '_db_official_' . $field, $value );
	}

	$email = isset( $_POST['db_official_email'] ) ? sanitize_email( wp_unslash( $_POST['db_official_email'] ) ) : '';
	update_post_meta( $post_id, '_db_official_email', $email );
}
add_action( 'save_post_db_official', 'db_save_official_meta' );

/**
 * Reads a person's field.
 *
 * @param string $field   Field name without the prefix.
 * @param int    $post_id Person ID.
 * @return string
 */
function db_official_field( $field, $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	return (string) get_post_meta( $post_id, '_db_official_' . $field, true );
}

/**
 * Initials for the fallback avatar.
 *
 * Uses the first and last word of the name, skipping suffixes like Jr. so
 * "Roscoe Eubanks, Jr." reads RE rather than RJ.
 *
 * @param string $name Full name.
 * @return string One or two letters.
 */
function db_official_initials( $name ) {
	$name  = str_replace( ',', ' ', $name );
	$parts = preg_split( '/\s+/', trim( $name ), -1, PREG_SPLIT_NO_EMPTY );

	if ( ! $parts ) {
		return '';
	}

	$skip = array( 'jr', 'jr.', 'sr', 'sr.', 'ii', 'iii', 'iv' );

	$parts = array_values(
		array_filter(
			$parts,
			function ( $part ) use ( $skip ) {
				return ! in_array( strtolower( $part ), $skip, true );
			}
		)
	);

	if ( ! $parts ) {
		return '';
	}

	$first = mb_substr( $parts[0], 0, 1 );

	if ( count( $parts ) < 2 ) {
		return mb_strtoupper( $first );
	}

	return mb_strtoupper( $first . mb_substr( end( $parts ), 0, 1 ) );
}

/**
 * URL of a portrait bundled with the theme, matched to the person by slug.
 *
 * The council's photographs shipped with the theme so the page works the
 * moment it is switched on. A Featured image always wins, so replacing one is
 * a normal WordPress edit rather than a file change.
 *
 * @param int $post_id Person ID.
 * @return string Empty string when no bundled portrait exists.
 */
function db_official_bundled_photo( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$slug    = get_post_field( 'post_name', $post_id );

	if ( ! $slug ) {
		return '';
	}

	$relative = '/assets/images/officials/' . $slug . '.jpg';

	if ( ! file_exists( DB_THEME_DIR . $relative ) ) {
		return '';
	}

	return DB_THEME_URI . $relative;
}

/**
 * The bodies, in the order they should appear.
 *
 * @return WP_Term[]
 */
function db_get_bodies() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'db_body',
			'hide_empty' => true,
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return array();
	}

	usort(
		$terms,
		function ( $a, $b ) {
			$a_order = (int) get_term_meta( $a->term_id, 'db_body_order', true );
			$b_order = (int) get_term_meta( $b->term_id, 'db_body_order', true );

			// Bodies without an order sink below those that have one.
			$a_order = $a_order ? $a_order : 999;
			$b_order = $b_order ? $b_order : 999;

			if ( $a_order === $b_order ) {
				return strcmp( $a->name, $b->name );
			}

			return $a_order <=> $b_order;
		}
	);

	return $terms;
}

/**
 * The people in one body, ranked.
 *
 * @param int $term_id Body term ID.
 * @return WP_Query
 */
function db_get_officials( $term_id ) {
	return new WP_Query(
		array(
			'post_type'      => 'db_official',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'db_body',
					'field'    => 'term_id',
					'terms'    => $term_id,
				),
			),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Adds a display order field when a body is created.
 */
function db_body_add_order_field() {
	?>
	<div class="form-field">
		<label for="db_body_order"><?php esc_html_e( 'Display order', 'drakes-branch' ); ?></label>
		<input type="number" id="db_body_order" name="db_body_order" value="" min="0" step="1">
		<p><?php esc_html_e( 'Lower numbers appear first on the Town Officials page. The council is usually 1.', 'drakes-branch' ); ?></p>
	</div>
	<?php
}
add_action( 'db_body_add_form_fields', 'db_body_add_order_field' );

/**
 * Adds the display order field when a body is edited.
 *
 * @param WP_Term $term Term being edited.
 */
function db_body_edit_order_field( $term ) {
	$order = get_term_meta( $term->term_id, 'db_body_order', true );
	?>
	<tr class="form-field">
		<th scope="row">
			<label for="db_body_order"><?php esc_html_e( 'Display order', 'drakes-branch' ); ?></label>
		</th>
		<td>
			<input type="number" id="db_body_order" name="db_body_order"
				value="<?php echo esc_attr( $order ); ?>" min="0" step="1">
			<p class="description"><?php esc_html_e( 'Lower numbers appear first on the Town Officials page.', 'drakes-branch' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'db_body_edit_form_fields', 'db_body_edit_order_field' );

/**
 * Saves the body display order.
 *
 * @param int $term_id Term ID.
 */
function db_save_body_order( $term_id ) {
	if ( ! isset( $_POST['db_body_order'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	// The nonce is checked by core before these hooks run.
	update_term_meta( $term_id, 'db_body_order', absint( wp_unslash( $_POST['db_body_order'] ) ) );
}
add_action( 'created_db_body', 'db_save_body_order' );
add_action( 'edited_db_body', 'db_save_body_order' );

/**
 * Shows the role in the officials admin list, so the list is scannable.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function db_official_admin_columns( $columns ) {
	$reordered = array();

	foreach ( $columns as $key => $label ) {
		$reordered[ $key ] = $label;

		if ( 'title' === $key ) {
			$reordered['role'] = __( 'Role', 'drakes-branch' );
		}
	}

	return $reordered;
}
add_filter( 'manage_db_official_posts_columns', 'db_official_admin_columns' );

/**
 * Fills the role column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function db_official_admin_column_content( $column, $post_id ) {
	if ( 'role' === $column ) {
		$role = db_official_field( 'role', $post_id );
		echo $role ? esc_html( $role ) : '<span aria-hidden="true">&mdash;</span>';
	}
}
add_action( 'manage_db_official_posts_custom_column', 'db_official_admin_column_content', 10, 2 );
