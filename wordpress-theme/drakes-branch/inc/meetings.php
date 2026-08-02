<?php
/**
 * Meetings.
 *
 * One record per meeting holds the date, the agenda, the minutes and the
 * recording. The homepage rail, the agenda page, the minutes archive and the
 * video archive all read from here, so staff enter a meeting once.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the meeting post type and its type taxonomy.
 */
function db_register_meetings() {
	register_post_type(
		'meeting',
		array(
			'labels'        => array(
				'name'               => __( 'Meetings', 'drakes-branch' ),
				'singular_name'      => __( 'Meeting', 'drakes-branch' ),
				'add_new_item'       => __( 'Add meeting', 'drakes-branch' ),
				'edit_item'          => __( 'Edit meeting', 'drakes-branch' ),
				'new_item'           => __( 'New meeting', 'drakes-branch' ),
				'view_item'          => __( 'View meeting', 'drakes-branch' ),
				'search_items'       => __( 'Search meetings', 'drakes-branch' ),
				'not_found'          => __( 'No meetings yet.', 'drakes-branch' ),
				'not_found_in_trash' => __( 'No meetings in the trash.', 'drakes-branch' ),
				'all_items'          => __( 'All meetings', 'drakes-branch' ),
			),
			'public'        => true,
			'has_archive'   => true,
			'menu_icon'     => 'dashicons-calendar-alt',
			'menu_position' => 21,
			'supports'      => array( 'title', 'editor', 'excerpt', 'revisions' ),
			'rewrite'       => array( 'slug' => 'meetings', 'with_front' => false ),
			'show_in_rest'  => true,
			'capability_type' => 'post',
		)
	);

	register_taxonomy(
		'meeting_type',
		'meeting',
		array(
			'labels'            => array(
				'name'          => __( 'Meeting types', 'drakes-branch' ),
				'singular_name' => __( 'Meeting type', 'drakes-branch' ),
				'add_new_item'  => __( 'Add meeting type', 'drakes-branch' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'meeting-type' ),
		)
	);
}
add_action( 'init', 'db_register_meetings' );

/**
 * Registers meta so the fields are available over REST and are sanitized on
 * every write, not only when the meta box posts.
 */
function db_register_meeting_meta() {
	$url_fields  = array( 'agenda_url', 'minutes_url', 'video_url', 'transcript_url' );
	$text_fields = array( 'date', 'time', 'location' );

	foreach ( $text_fields as $field ) {
		register_post_meta(
			'meeting',
			'_db_meeting_' . $field,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	foreach ( $url_fields as $field ) {
		register_post_meta(
			'meeting',
			'_db_meeting_' . $field,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'esc_url_raw',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	register_post_meta(
		'meeting',
		'_db_meeting_cancelled',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'db_register_meeting_meta' );

/**
 * Adds the meeting details meta box.
 */
function db_meeting_meta_box() {
	add_meta_box(
		'db-meeting-details',
		__( 'Meeting details', 'drakes-branch' ),
		'db_render_meeting_meta_box',
		'meeting',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'db_meeting_meta_box' );

/**
 * Renders the meeting details meta box.
 *
 * @param WP_Post $post Current post.
 */
function db_render_meeting_meta_box( $post ) {
	wp_nonce_field( 'db_save_meeting', 'db_meeting_nonce' );

	$value = function ( $field ) use ( $post ) {
		return get_post_meta( $post->ID, '_db_meeting_' . $field, true );
	};
	?>
	<style>
		.db-meta-grid { display: grid; gap: 16px 24px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); margin-top: 8px; }
		.db-meta-field label { display: block; font-weight: 600; margin-bottom: 4px; }
		.db-meta-field input[type="text"], .db-meta-field input[type="url"], .db-meta-field input[type="date"], .db-meta-field input[type="time"] { width: 100%; }
		.db-meta-field p { margin: 4px 0 0; color: #646970; font-size: 12px; }
		.db-meta-full { grid-column: 1 / -1; }
	</style>

	<div class="db-meta-grid">
		<div class="db-meta-field">
			<label for="db_meeting_date"><?php esc_html_e( 'Meeting date', 'drakes-branch' ); ?></label>
			<input type="date" id="db_meeting_date" name="db_meeting_date"
				value="<?php echo esc_attr( $value( 'date' ) ); ?>">
			<p><?php esc_html_e( 'Required. Meetings are ordered and filed by this date.', 'drakes-branch' ); ?></p>
		</div>

		<div class="db-meta-field">
			<label for="db_meeting_time"><?php esc_html_e( 'Start time', 'drakes-branch' ); ?></label>
			<input type="time" id="db_meeting_time" name="db_meeting_time"
				value="<?php echo esc_attr( $value( 'time' ) ); ?>">
		</div>

		<div class="db-meta-field db-meta-full">
			<label for="db_meeting_location"><?php esc_html_e( 'Location', 'drakes-branch' ); ?></label>
			<input type="text" id="db_meeting_location" name="db_meeting_location"
				value="<?php echo esc_attr( $value( 'location' ) ); ?>">
			<p>
				<?php
				printf(
					/* translators: %s: default location from the Customizer */
					esc_html__( 'Leave blank to use the usual location: %s', 'drakes-branch' ),
					esc_html( db_get( 'db_meeting_location' ) )
				);
				?>
			</p>
		</div>

		<div class="db-meta-field">
			<label for="db_meeting_agenda_url"><?php esc_html_e( 'Agenda document URL', 'drakes-branch' ); ?></label>
			<input type="url" id="db_meeting_agenda_url" name="db_meeting_agenda_url"
				value="<?php echo esc_attr( $value( 'agenda_url' ) ); ?>">
			<p><?php esc_html_e( 'Upload the PDF to the media library, then paste its link here.', 'drakes-branch' ); ?></p>
		</div>

		<div class="db-meta-field">
			<label for="db_meeting_minutes_url"><?php esc_html_e( 'Minutes document URL', 'drakes-branch' ); ?></label>
			<input type="url" id="db_meeting_minutes_url" name="db_meeting_minutes_url"
				value="<?php echo esc_attr( $value( 'minutes_url' ) ); ?>">
			<p><?php esc_html_e( 'Added after the meeting, once the minutes are approved.', 'drakes-branch' ); ?></p>
		</div>

		<div class="db-meta-field">
			<label for="db_meeting_video_url"><?php esc_html_e( 'Recording URL', 'drakes-branch' ); ?></label>
			<input type="url" id="db_meeting_video_url" name="db_meeting_video_url"
				value="<?php echo esc_attr( $value( 'video_url' ) ); ?>"
				placeholder="https://www.youtube.com/watch?v=...">
			<p><?php esc_html_e( 'Paste the YouTube link. Turn on captions before publishing.', 'drakes-branch' ); ?></p>
		</div>

		<div class="db-meta-field">
			<label for="db_meeting_transcript_url"><?php esc_html_e( 'Transcript URL', 'drakes-branch' ); ?></label>
			<input type="url" id="db_meeting_transcript_url" name="db_meeting_transcript_url"
				value="<?php echo esc_attr( $value( 'transcript_url' ) ); ?>">
		</div>

		<div class="db-meta-field db-meta-full">
			<label for="db_meeting_cancelled">
				<input type="checkbox" id="db_meeting_cancelled" name="db_meeting_cancelled" value="1"
					<?php checked( $value( 'cancelled' ), '1' ); ?>>
				<?php esc_html_e( 'This meeting was cancelled', 'drakes-branch' ); ?>
			</label>
			<p><?php esc_html_e( 'The meeting stays listed and is clearly marked as cancelled.', 'drakes-branch' ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Saves the meeting details meta box.
 *
 * @param int $post_id Post being saved.
 */
function db_save_meeting_meta( $post_id ) {
	if ( ! isset( $_POST['db_meeting_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['db_meeting_nonce'] ) ), 'db_save_meeting' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = array( 'date', 'time', 'location' );
	$url_fields  = array( 'agenda_url', 'minutes_url', 'video_url', 'transcript_url' );

	foreach ( $text_fields as $field ) {
		$key   = 'db_meeting_' . $field;
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		update_post_meta( $post_id, '_db_meeting_' . $field, $value );
	}

	foreach ( $url_fields as $field ) {
		$key   = 'db_meeting_' . $field;
		$value = isset( $_POST[ $key ] ) ? esc_url_raw( wp_unslash( $_POST[ $key ] ) ) : '';
		update_post_meta( $post_id, '_db_meeting_' . $field, $value );
	}

	update_post_meta( $post_id, '_db_meeting_cancelled', isset( $_POST['db_meeting_cancelled'] ) ? '1' : '' );
}
add_action( 'save_post_meeting', 'db_save_meeting_meta' );

/**
 * Queries meetings by date.
 *
 * @param string $when     'upcoming' or 'past'.
 * @param int    $limit    Number of meetings.
 * @param array  $requires Meta keys that must have a value, for example
 *                         'video_url' to list only recorded meetings.
 * @return WP_Query
 */
function db_get_meetings( $when = 'upcoming', $limit = 3, $requires = array() ) {
	$today = current_time( 'Y-m-d' );

	$meta_query = array(
		array(
			'key'     => '_db_meeting_date',
			'value'   => $today,
			'compare' => 'upcoming' === $when ? '>=' : '<',
			'type'    => 'DATE',
		),
	);

	foreach ( $requires as $field ) {
		$meta_query[] = array(
			'key'     => '_db_meeting_' . $field,
			'value'   => '',
			'compare' => '!=',
		);
	}

	if ( count( $meta_query ) > 1 ) {
		$meta_query['relation'] = 'AND';
	}

	return new WP_Query(
		array(
			'post_type'      => 'meeting',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'meta_key'       => '_db_meeting_date',
			'orderby'        => 'meta_value',
			'order'          => 'upcoming' === $when ? 'ASC' : 'DESC',
			'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'no_found_rows'  => true,
		)
	);
}

/**
 * Reads a meeting field, falling back to the Customizer default for location.
 *
 * @param string $field   Field name without the prefix.
 * @param int    $post_id Meeting ID.
 * @return string
 */
function db_meeting_field( $field, $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$value   = get_post_meta( $post_id, '_db_meeting_' . $field, true );

	if ( '' === $value && 'location' === $field ) {
		return db_get( 'db_meeting_location' );
	}

	return $value;
}

/**
 * Formats a meeting's date and time for display.
 *
 * @param int    $post_id Meeting ID.
 * @param string $format  Date format. Defaults to the site setting.
 * @return string
 */
function db_meeting_date( $post_id = 0, $format = '' ) {
	$date = db_meeting_field( 'date', $post_id );

	if ( ! $date ) {
		return '';
	}

	$format = $format ? $format : 'l, F j, Y';
	$stamp  = strtotime( $date );

	return $stamp ? wp_date( $format, $stamp ) : '';
}

/**
 * Machine-readable datetime for a <time> element.
 *
 * @param int $post_id Meeting ID.
 * @return string
 */
function db_meeting_datetime_attr( $post_id = 0 ) {
	$date = db_meeting_field( 'date', $post_id );
	$time = db_meeting_field( 'time', $post_id );

	if ( ! $date ) {
		return '';
	}

	return $time ? $date . 'T' . $time : $date;
}

/**
 * Formats a meeting's start time.
 *
 * @param int $post_id Meeting ID.
 * @return string
 */
function db_meeting_time( $post_id = 0 ) {
	$time = db_meeting_field( 'time', $post_id );

	if ( ! $time ) {
		return '';
	}

	$stamp = strtotime( '1970-01-01 ' . $time );

	return $stamp ? wp_date( 'g:i a', $stamp ) : $time;
}

/**
 * Extracts a YouTube video ID from any of its URL formats.
 *
 * @param string $url Video URL.
 * @return string Empty string when the URL is not recognised.
 */
function db_youtube_id( $url ) {
	if ( ! $url ) {
		return '';
	}

	$patterns = array(
		'#youtube\.com/watch\?(?:.*&)?v=([A-Za-z0-9_-]{11})#',
		'#youtu\.be/([A-Za-z0-9_-]{11})#',
		'#youtube\.com/embed/([A-Za-z0-9_-]{11})#',
		'#youtube\.com/live/([A-Za-z0-9_-]{11})#',
	);

	foreach ( $patterns as $pattern ) {
		if ( preg_match( $pattern, $url, $matches ) ) {
			return $matches[1];
		}
	}

	return '';
}

/**
 * Orders the meeting archive by meeting date rather than publish date.
 *
 * @param WP_Query $query Current query.
 */
function db_meeting_archive_order( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'meeting' ) || $query->is_tax( 'meeting_type' ) ) {
		$query->set( 'meta_key', '_db_meeting_date' );
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'order', 'DESC' );

		// The archive template lists upcoming meetings from its own query, so
		// the main query covers past meetings only and nothing appears twice.
		$query->set(
			'meta_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				array(
					'key'     => '_db_meeting_date',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '<',
					'type'    => 'DATE',
				),
			)
		);
	}
}
add_action( 'pre_get_posts', 'db_meeting_archive_order' );

/**
 * Adds a meeting date column to the admin list, and makes it sortable.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function db_meeting_admin_columns( $columns ) {
	$reordered = array();

	foreach ( $columns as $key => $label ) {
		$reordered[ $key ] = $label;

		if ( 'title' === $key ) {
			$reordered['meeting_date'] = __( 'Meeting date', 'drakes-branch' );
			$reordered['documents']    = __( 'Documents', 'drakes-branch' );
		}
	}

	return $reordered;
}
add_filter( 'manage_meeting_posts_columns', 'db_meeting_admin_columns' );

/**
 * Fills the custom admin columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function db_meeting_admin_column_content( $column, $post_id ) {
	if ( 'meeting_date' === $column ) {
		$date = db_meeting_date( $post_id, 'M j, Y' );
		echo $date ? esc_html( $date ) : '<span aria-hidden="true">&mdash;</span>';

		if ( db_meeting_field( 'cancelled', $post_id ) ) {
			echo ' <strong>' . esc_html__( '(cancelled)', 'drakes-branch' ) . '</strong>';
		}
	}

	if ( 'documents' === $column ) {
		$have = array();

		if ( db_meeting_field( 'agenda_url', $post_id ) ) {
			$have[] = __( 'Agenda', 'drakes-branch' );
		}
		if ( db_meeting_field( 'minutes_url', $post_id ) ) {
			$have[] = __( 'Minutes', 'drakes-branch' );
		}
		if ( db_meeting_field( 'video_url', $post_id ) ) {
			$have[] = __( 'Recording', 'drakes-branch' );
		}

		echo $have ? esc_html( implode( ', ', $have ) ) : '<span aria-hidden="true">&mdash;</span>';
	}
}
add_action( 'manage_meeting_posts_custom_column', 'db_meeting_admin_column_content', 10, 2 );

/**
 * Makes the meeting date column sortable.
 *
 * @param array $columns Sortable columns.
 * @return array
 */
function db_meeting_sortable_columns( $columns ) {
	$columns['meeting_date'] = 'meeting_date';

	return $columns;
}
add_filter( 'manage_edit-meeting_sortable_columns', 'db_meeting_sortable_columns' );

/**
 * Sorts the admin list by meeting date, newest first by default.
 *
 * @param WP_Query $query Current query.
 */
function db_meeting_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'meeting' !== $query->get( 'post_type' ) ) {
		return;
	}

	if ( ! $query->get( 'orderby' ) || 'meeting_date' === $query->get( 'orderby' ) ) {
		$query->set( 'meta_key', '_db_meeting_date' );
		$query->set( 'orderby', 'meta_value' );

		if ( ! $query->get( 'order' ) ) {
			$query->set( 'order', 'DESC' );
		}
	}
}
add_action( 'pre_get_posts', 'db_meeting_admin_order' );
