<?php
/**
 * Civic forms.
 *
 * One schema per form drives both the rendered markup and the server-side
 * validation, so the two can never drift apart.
 *
 * Submissions post to this site, not straight to N8N. The browser never sees
 * the webhook URL, validation cannot be bypassed by posting directly to the
 * webhook, and there is no cross-origin request to configure. If the webhook
 * is unset or unreachable the submission is emailed to the town instead, so a
 * resident's request is never silently lost.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Directory that holds uploaded attachments, outside the media library.
 */
function db_upload_dir() {
	$uploads = wp_upload_dir();

	return trailingslashit( $uploads['basedir'] ) . 'town-forms';
}

/**
 * Creates the attachment directory and blocks direct web access to it.
 *
 * Résumés and permit attachments contain personal information, so they must
 * not be readable by anyone who guesses a URL.
 */
function db_protect_upload_dir() {
	$dir = db_upload_dir();

	if ( ! file_exists( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	$htaccess = $dir . '/.htaccess';

	if ( ! file_exists( $htaccess ) ) {
		file_put_contents( // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			$htaccess,
			"# Attachments submitted through town forms are private.\n"
			. "Require all denied\n"
			. "<IfModule !mod_authz_core.c>\n\tDeny from all\n</IfModule>\n"
		);
	}

	$index = $dir . '/index.php';

	if ( ! file_exists( $index ) ) {
		file_put_contents( $index, "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}
}
add_action( 'after_switch_theme', 'db_protect_upload_dir' );

/**
 * Field definitions for every form.
 *
 * @param string $type Form type.
 * @return array Empty array for an unknown type.
 */
function db_form_schema( $type ) {
	$name = array(
		'name'      => 'name',
		'label'     => __( 'Your name', 'drakes-branch' ),
		'type'      => 'text',
		'required'  => true,
		'autocomplete' => 'name',
		'maxlength' => 120,
	);

	$email = array(
		'name'      => 'email',
		'label'     => __( 'Email address', 'drakes-branch' ),
		'type'      => 'email',
		'required'  => true,
		'autocomplete' => 'email',
		'hint'      => __( 'We use this to reply to you.', 'drakes-branch' ),
		'maxlength' => 190,
	);

	$phone = array(
		'name'      => 'phone',
		'label'     => __( 'Phone number', 'drakes-branch' ),
		'type'      => 'tel',
		'required'  => false,
		'autocomplete' => 'tel',
		'maxlength' => 40,
	);

	$forms = array(
		'contact' => array(
			'title'    => __( 'Contact the town office', 'drakes-branch' ),
			'confirm'  => __( 'Thank you. We received your message and will reply within two business days.', 'drakes-branch' ),
			'subject'  => __( 'Website contact form', 'drakes-branch' ),
			'webhook'  => 'db_webhook_contact',
			'fields'   => array(
				$name,
				$email,
				$phone,
				array(
					'name'      => 'subject',
					'label'     => __( 'Subject', 'drakes-branch' ),
					'type'      => 'text',
					'required'  => true,
					'maxlength' => 160,
				),
				array(
					'name'      => 'message',
					'label'     => __( 'Message', 'drakes-branch' ),
					'type'      => 'textarea',
					'required'  => true,
					'maxlength' => 4000,
				),
			),
		),

		'permit' => array(
			'title'    => __( 'Request a permit', 'drakes-branch' ),
			'confirm'  => __( 'Your permit request has been submitted. The town office will contact you about next steps and any fee due.', 'drakes-branch' ),
			'subject'  => __( 'Permit request', 'drakes-branch' ),
			'webhook'  => 'db_webhook_permit',
			'reference' => 'PR',
			'fields'   => array(
				$name,
				$email,
				$phone,
				array(
					'name'      => 'property_address',
					'label'     => __( 'Address of the property', 'drakes-branch' ),
					'type'      => 'text',
					'required'  => true,
					'autocomplete' => 'street-address',
					'maxlength' => 200,
				),
				array(
					'name'     => 'permit_type',
					'label'    => __( 'Type of permit', 'drakes-branch' ),
					'type'     => 'select',
					'required' => true,
					'options'  => array(
						'building' => __( 'Building', 'drakes-branch' ),
						'utility'  => __( 'Utility connection', 'drakes-branch' ),
						'sign'     => __( 'Sign', 'drakes-branch' ),
						'demolition' => __( 'Demolition', 'drakes-branch' ),
						'other'    => __( 'Something else', 'drakes-branch' ),
					),
				),
				array(
					'name'      => 'description',
					'label'     => __( 'Describe the work', 'drakes-branch' ),
					'type'      => 'textarea',
					'required'  => true,
					'hint'      => __( 'Include what you plan to do, roughly when you want to start, and who will do the work.', 'drakes-branch' ),
					'maxlength' => 4000,
				),
				array(
					'name'     => 'attachment',
					'label'    => __( 'Plans or drawings', 'drakes-branch' ),
					'type'     => 'file',
					'required' => false,
					'hint'     => __( 'Optional. PDF, JPG or PNG, up to 8 MB.', 'drakes-branch' ),
					'accept'   => array( 'pdf', 'jpg', 'jpeg', 'png' ),
				),
			),
		),

		'feedback' => array(
			'title'    => __( 'Report a problem or share feedback', 'drakes-branch' ),
			'confirm'  => __( 'Thank you. Your message has been recorded and passed to the town office.', 'drakes-branch' ),
			'subject'  => __( 'Complaint or feedback', 'drakes-branch' ),
			'webhook'  => 'db_webhook_feedback',
			'fields'   => array(
				$name,
				$email,
				$phone,
				array(
					'name'     => 'category',
					'label'    => __( 'What is this about?', 'drakes-branch' ),
					'type'     => 'select',
					'required' => true,
					'options'  => array(
						'street'    => __( 'Streets, signs or lighting', 'drakes-branch' ),
						'water'     => __( 'Water or sewer', 'drakes-branch' ),
						'trash'     => __( 'Trash collection', 'drakes-branch' ),
						'property'  => __( 'Property or nuisance concern', 'drakes-branch' ),
						'suggestion' => __( 'A suggestion for the town', 'drakes-branch' ),
						'other'     => __( 'Something else', 'drakes-branch' ),
					),
				),
				array(
					'name'      => 'location',
					'label'     => __( 'Where is it?', 'drakes-branch' ),
					'type'      => 'text',
					'required'  => false,
					'hint'      => __( 'A street address or nearby landmark helps us find it.', 'drakes-branch' ),
					'maxlength' => 200,
				),
				array(
					'name'      => 'description',
					'label'     => __( 'Tell us what happened', 'drakes-branch' ),
					'type'      => 'textarea',
					'required'  => true,
					'maxlength' => 4000,
				),
			),
		),

		'foia' => array(
			'title'    => __( 'Request public records', 'drakes-branch' ),
			'confirm'  => __( 'Your records request has been received. Virginia law gives the town five working days to respond.', 'drakes-branch' ),
			'subject'  => __( 'FOIA request', 'drakes-branch' ),
			'webhook'  => 'db_webhook_foia',
			'reference' => 'FOIA',
			'fields'   => array(
				$name,
				$email,
				$phone,
				array(
					'name'      => 'mailing_address',
					'label'     => __( 'Mailing address', 'drakes-branch' ),
					'type'      => 'textarea',
					'required'  => false,
					'hint'      => __( 'Needed only if you want the records mailed to you.', 'drakes-branch' ),
					'maxlength' => 400,
				),
				array(
					'name'      => 'records',
					'label'     => __( 'Which records do you want?', 'drakes-branch' ),
					'type'      => 'textarea',
					'required'  => true,
					'hint'      => __( 'Describe the records as specifically as you can, including dates if you know them. A narrower request is usually answered faster and costs less.', 'drakes-branch' ),
					'maxlength' => 4000,
				),
				array(
					'name'     => 'format',
					'label'    => __( 'How would you like to receive them?', 'drakes-branch' ),
					'type'     => 'radio',
					'required' => true,
					'options'  => array(
						'email'  => __( 'By email', 'drakes-branch' ),
						'paper'  => __( 'Paper copies', 'drakes-branch' ),
						'inspect' => __( 'I will read them at the town office', 'drakes-branch' ),
					),
				),
				array(
					'name'     => 'cost_limit',
					'label'    => __( 'Charges', 'drakes-branch' ),
					'type'     => 'radio',
					'required' => true,
					'hint'     => __( 'The town may charge for staff time and copying. We will contact you before doing any work that costs more than you agree to here.', 'drakes-branch' ),
					'options'  => array(
						'contact' => __( 'Contact me with an estimate before starting', 'drakes-branch' ),
						'25'      => __( 'Proceed if the cost is under $25', 'drakes-branch' ),
						'50'      => __( 'Proceed if the cost is under $50', 'drakes-branch' ),
					),
				),
			),
		),

		'jobs' => array(
			'title'    => __( 'Apply for a position', 'drakes-branch' ),
			'confirm'  => __( 'Your application has been submitted. We review applications as they arrive and will be in touch within five to seven business days.', 'drakes-branch' ),
			'subject'  => __( 'Job application', 'drakes-branch' ),
			'webhook'  => 'db_webhook_jobs',
			'reference' => 'JOB',
			'fields'   => array(
				array_merge( $name, array( 'label' => __( 'Full name', 'drakes-branch' ) ) ),
				$email,
				array_merge( $phone, array( 'required' => true ) ),
				array(
					'name'      => 'position',
					'label'     => __( 'Position you are applying for', 'drakes-branch' ),
					'type'      => 'text',
					'required'  => true,
					'hint'      => __( 'Open positions are listed above this form.', 'drakes-branch' ),
					'maxlength' => 160,
				),
				array(
					'name'     => 'resume',
					'label'    => __( 'Résumé', 'drakes-branch' ),
					'type'     => 'file',
					'required' => true,
					'hint'     => __( 'PDF or Word document, up to 8 MB.', 'drakes-branch' ),
					'accept'   => array( 'pdf', 'doc', 'docx' ),
				),
				array(
					'name'      => 'cover_letter',
					'label'     => __( 'Why are you a good fit?', 'drakes-branch' ),
					'type'      => 'textarea',
					'required'  => true,
					'hint'      => __( 'A few sentences is plenty.', 'drakes-branch' ),
					'maxlength' => 4000,
				),
			),
		),
	);

	return isset( $forms[ $type ] ) ? $forms[ $type ] : array();
}

/**
 * Maximum attachment size in bytes.
 *
 * @return int
 */
function db_max_upload_bytes() {
	return (int) apply_filters( 'db_max_upload_bytes', 8 * MB_IN_BYTES );
}

/* ==========================================================================
   Rendering
   ========================================================================== */

/**
 * Renders one form field.
 *
 * @param array  $field   Field definition.
 * @param string $form_id Unique form id, used to namespace element ids.
 * @param string $error   Error message from a submission made without
 *                        JavaScript, so the field renders already invalid.
 * @param string $value   Previously entered value, so nothing is retyped.
 */
function db_render_field( $field, $form_id, $error = '', $value = '' ) {
	$id       = $form_id . '-' . $field['name'];
	$required = ! empty( $field['required'] );
	$hint_id  = ! empty( $field['hint'] ) ? $id . '-hint' : '';
	$error_id = $id . '-error';

	// The error node is always present and always referenced, so the
	// relationship exists before an error is written into it.
	$described = trim( $hint_id . ' ' . $error_id );

	$required_mark = $required
		? '<span class="field__required" aria-hidden="true">*</span>'
		: ' <span class="text-meta">(' . esc_html__( 'optional', 'drakes-branch' ) . ')</span>';

	$common = sprintf(
		'id="%1$s" name="%2$s" class="field__control" aria-describedby="%3$s"%4$s%5$s',
		esc_attr( $id ),
		esc_attr( $field['name'] ),
		esc_attr( $described ),
		$required ? ' required aria-required="true"' : '',
		$error ? ' aria-invalid="true"' : ''
	);

	if ( ! empty( $field['autocomplete'] ) ) {
		$common .= ' autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
	}

	if ( ! empty( $field['maxlength'] ) ) {
		$common .= ' maxlength="' . (int) $field['maxlength'] . '"';
	}

	// Radio groups need a fieldset so the legend names the whole group.
	if ( 'radio' === $field['type'] ) {
		?>
		<fieldset class="fieldset">
			<legend class="fieldset__legend">
				<?php echo esc_html( $field['label'] ); ?>
				<?php echo wp_kses_post( $required_mark ); ?>
			</legend>
			<?php if ( ! empty( $field['hint'] ) ) : ?>
				<span class="field__hint" id="<?php echo esc_attr( $hint_id ); ?>">
					<?php echo esc_html( $field['hint'] ); ?>
				</span>
			<?php endif; ?>

			<?php foreach ( $field['options'] as $option_value => $label ) : ?>
				<label class="choice" for="<?php echo esc_attr( $id . '-' . $option_value ); ?>">
					<input type="radio"
						id="<?php echo esc_attr( $id . '-' . $option_value ); ?>"
						name="<?php echo esc_attr( $field['name'] ); ?>"
						value="<?php echo esc_attr( $option_value ); ?>"
						aria-describedby="<?php echo esc_attr( $described ); ?>"
						<?php checked( $value, $option_value ); ?>
						<?php echo $required ? 'required aria-required="true"' : ''; ?>>
					<span class="choice__label"><?php echo esc_html( $label ); ?></span>
				</label>
			<?php endforeach; ?>

			<p class="field__error" id="<?php echo esc_attr( $error_id ); ?>" role="alert">
				<?php echo esc_html( $error ); ?>
			</p>
		</fieldset>
		<?php
		return;
	}
	?>

	<div class="field">
		<label class="field__label" for="<?php echo esc_attr( $id ); ?>">
			<?php echo esc_html( $field['label'] ); ?>
			<?php echo wp_kses_post( $required_mark ); ?>
		</label>

		<?php if ( ! empty( $field['hint'] ) ) : ?>
			<span class="field__hint" id="<?php echo esc_attr( $hint_id ); ?>">
				<?php echo esc_html( $field['hint'] ); ?>
			</span>
		<?php endif; ?>

		<?php if ( 'textarea' === $field['type'] ) : ?>
			<textarea <?php echo $common; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> rows="6"><?php echo esc_textarea( $value ); ?></textarea>

		<?php elseif ( 'select' === $field['type'] ) : ?>
			<select <?php echo $common; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<option value=""><?php esc_html_e( 'Choose one', 'drakes-branch' ); ?></option>
				<?php foreach ( $field['options'] as $option_value => $label ) : ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>

		<?php elseif ( 'file' === $field['type'] ) : ?>
			<input type="file"
				<?php echo $common; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				accept="<?php echo esc_attr( '.' . implode( ',.', $field['accept'] ) ); ?>"
				data-max-bytes="<?php echo esc_attr( db_max_upload_bytes() ); ?>"
				data-accept="<?php echo esc_attr( implode( ', ', $field['accept'] ) ); ?>">

		<?php else : ?>
			<input type="<?php echo esc_attr( $field['type'] ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				<?php echo $common; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php endif; ?>

		<p class="field__error" id="<?php echo esc_attr( $error_id ); ?>" role="alert">
			<?php echo esc_html( $error ); ?>
		</p>
	</div>
	<?php
}

/**
 * Renders a complete form.
 *
 * @param string $type Form type.
 */
function db_render_form( $type ) {
	$schema = db_form_schema( $type );

	if ( ! $schema ) {
		return;
	}

	$form_id  = 'form-' . $type;
	$issued   = time();
	$token    = hash_hmac( 'sha256', $type . '|' . $issued, wp_salt( 'nonce' ) );
	$has_file = false;

	foreach ( $schema['fields'] as $field ) {
		if ( 'file' === $field['type'] ) {
			$has_file = true;
			break;
		}
	}

	// Result of a submission made without JavaScript, if there was one.
	$result = db_stored_result( $type );
	$errors = ! empty( $result['errors'] ) ? $result['errors'] : array();
	$values = ! empty( $result['values'] ) ? $result['values'] : array();

	/*
	 * The form posts to its own page, so it works with JavaScript switched
	 * off or blocked. When the script does load it intercepts submit and
	 * posts to the REST route instead, which avoids a full page reload.
	 */
	?>
	<form class="form" id="<?php echo esc_attr( $form_id ); ?>" method="post"
		action="<?php echo esc_url( get_permalink() ); ?>#<?php echo esc_attr( $form_id ); ?>"
		data-town-form="<?php echo esc_attr( $type ); ?>"
		<?php echo $has_file ? 'enctype="multipart/form-data"' : ''; ?>
		novalidate>

		<?php // Announced when submission succeeds or fails as a whole. ?>
		<div class="form-message <?php echo esc_attr( db_message_class( $result ) ); ?>"
			id="<?php echo esc_attr( $form_id ); ?>-message"
			role="status" aria-live="polite" tabindex="-1">
			<?php if ( ! empty( $result['message'] ) ) : ?>
				<p><?php echo esc_html( $result['message'] ); ?></p>
			<?php endif; ?>
		</div>

		<input type="hidden" name="form_type" value="<?php echo esc_attr( $type ); ?>">
		<input type="hidden" name="issued" value="<?php echo esc_attr( $issued ); ?>">
		<input type="hidden" name="token" value="<?php echo esc_attr( $token ); ?>">

		<?php
		/*
		 * Honeypot. Positioned off-screen rather than hidden with display:none,
		 * because many bots skip fields that are display:none. Real people
		 * never reach it: it is removed from the tab order and from the
		 * accessibility tree.
		 */
		?>
		<div class="honeypot" aria-hidden="true">
			<label for="<?php echo esc_attr( $form_id ); ?>-website">
				<?php esc_html_e( 'Leave this field empty', 'drakes-branch' ); ?>
			</label>
			<input type="text" id="<?php echo esc_attr( $form_id ); ?>-website"
				name="website" tabindex="-1" autocomplete="off">
		</div>

		<?php foreach ( $schema['fields'] as $field ) : ?>
			<?php
			db_render_field(
				$field,
				$form_id,
				isset( $errors[ $field['name'] ] ) ? $errors[ $field['name'] ] : '',
				isset( $values[ $field['name'] ] ) ? $values[ $field['name'] ] : ''
			);
			?>
		<?php endforeach; ?>

		<div class="form__actions">
			<button type="submit" class="btn">
				<?php esc_html_e( 'Send', 'drakes-branch' ); ?>
			</button>
			<p class="form__note">
				<span class="field__required" aria-hidden="true">*</span>
				<?php esc_html_e( 'Fields marked with an asterisk are required.', 'drakes-branch' ); ?>
			</p>
		</div>
	</form>
	<?php
}

/**
 * Shortcode so a form can be placed in any page: [town_form type="permit"].
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function db_form_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'contact' ), $atts, 'town_form' );

	ob_start();
	db_render_form( sanitize_key( $atts['type'] ) );

	return ob_get_clean();
}
add_shortcode( 'town_form', 'db_form_shortcode' );

/* ==========================================================================
   Submission
   ========================================================================== */

/**
 * Registers the submission route.
 *
 * The route is public. Requiring a nonce would break the form on any cached
 * page once the nonce expired, and a nonce protects against cross-site
 * requests, which is not the threat here: the endpoint performs no privileged
 * action. Spam is the real risk, and it is handled by the honeypot, the
 * signed timestamp and the per-address rate limit.
 */
function db_register_form_route() {
	register_rest_route(
		'drakes-branch/v1',
		'/submit',
		array(
			'methods'             => 'POST',
			'callback'            => 'db_handle_submission',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'db_register_form_route' );

/**
 * Rejects repeated submissions from the same address.
 *
 * @return bool True when the caller is over the limit.
 */
function db_is_rate_limited() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	if ( ! $ip ) {
		return false;
	}

	// The address is hashed so no raw IP is written to the database.
	$key   = 'db_rate_' . hash_hmac( 'sha256', $ip, wp_salt() );
	$count = (int) get_transient( $key );

	if ( $count >= 5 ) {
		return true;
	}

	set_transient( $key, $count + 1, 15 * MINUTE_IN_SECONDS );

	return false;
}

/**
 * Validates and delivers a submission.
 *
 * Shared by the REST route and the no-JavaScript POST fallback so both paths
 * apply exactly the same rules.
 *
 * @param array $params Submitted values.
 * @param array $files  Uploaded files, in $_FILES shape.
 * @return array {
 *     @type int    $code    HTTP status.
 *     @type string $message Message for the visitor.
 *     @type array  $errors  Field name to error message.
 *     @type array  $values  Text values, for repopulating the form.
 * }
 */
function db_process_submission( $params, $files = array() ) {
	$param = function ( $key ) use ( $params ) {
		return isset( $params[ $key ] ) ? (string) $params[ $key ] : '';
	};

	$type   = sanitize_key( $param( 'form_type' ) );
	$schema = db_form_schema( $type );

	if ( ! $schema ) {
		return array(
			'code'    => 400,
			'message' => __( 'That form is not recognised.', 'drakes-branch' ),
		);
	}

	// Honeypot. Answer as though it succeeded so a bot learns nothing.
	if ( '' !== trim( $param( 'website' ) ) ) {
		return array(
			'code'    => 200,
			'message' => $schema['confirm'],
		);
	}

	// Signed timestamp: proves the form came from this site and that a human
	// spent at least a few seconds on it.
	$issued   = (int) $param( 'issued' );
	$token    = $param( 'token' );
	$expected = hash_hmac( 'sha256', $type . '|' . $issued, wp_salt( 'nonce' ) );
	$age      = time() - $issued;

	if ( ! hash_equals( $expected, $token ) || $age < 3 || $age > DAY_IN_SECONDS ) {
		return array(
			'code'    => 400,
			'message' => __( 'This form expired before it was sent. Please reload the page and try again.', 'drakes-branch' ),
		);
	}

	if ( db_is_rate_limited() ) {
		return array(
			'code'    => 429,
			'message' => __( 'Several forms have already been sent from this connection. Please wait a few minutes, or call the town office.', 'drakes-branch' ),
		);
	}

	$values = array();
	$errors = array();

	foreach ( $schema['fields'] as $field ) {
		$key   = $field['name'];
		$label = $field['label'];

		if ( 'file' === $field['type'] ) {
			$upload = isset( $files[ $key ] ) ? $files[ $key ] : null;
			$empty  = ! $upload || UPLOAD_ERR_NO_FILE === $upload['error'];

			if ( $empty ) {
				if ( ! empty( $field['required'] ) ) {
					$errors[ $key ] = sprintf(
						/* translators: %s: field label */
						__( '%s is required.', 'drakes-branch' ),
						$label
					);
				}
				continue;
			}

			$stored = db_store_upload( $upload, $field );

			if ( is_wp_error( $stored ) ) {
				$errors[ $key ] = $stored->get_error_message();
				continue;
			}

			$values[ $key ] = $stored;
			continue;
		}

		$raw = $param( $key );

		if ( 'textarea' === $field['type'] ) {
			$value = sanitize_textarea_field( wp_unslash( $raw ) );
		} elseif ( 'email' === $field['type'] ) {
			$value = sanitize_email( wp_unslash( $raw ) );
		} else {
			$value = sanitize_text_field( wp_unslash( $raw ) );
		}

		if ( '' === trim( $value ) ) {
			if ( ! empty( $field['required'] ) ) {
				$errors[ $key ] = sprintf(
					/* translators: %s: field label */
					__( '%s is required.', 'drakes-branch' ),
					$label
				);
			}
			$values[ $key ] = '';
			continue;
		}

		if ( 'email' === $field['type'] && ! is_email( $value ) ) {
			$errors[ $key ] = __( 'Enter an email address in the format name@example.com.', 'drakes-branch' );
			continue;
		}

		// Choice fields must match an option we actually offered.
		if ( in_array( $field['type'], array( 'select', 'radio' ), true )
			&& ! array_key_exists( $value, $field['options'] ) ) {
			$errors[ $key ] = sprintf(
				/* translators: %s: field label */
				__( 'Choose one of the listed options for %s.', 'drakes-branch' ),
				$label
			);
			continue;
		}

		if ( ! empty( $field['maxlength'] ) && mb_strlen( $value ) > (int) $field['maxlength'] ) {
			$value = mb_substr( $value, 0, (int) $field['maxlength'] );
		}

		$values[ $key ] = $value;
	}

	if ( $errors ) {
		// Attachments are never echoed back; a file input cannot be
		// repopulated for security reasons, so the visitor reselects it.
		$text_values = array_filter(
			$values,
			function ( $value ) {
				return ! is_array( $value );
			}
		);

		return array(
			'code'    => 422,
			'message' => __( 'Please check the highlighted fields and send the form again.', 'drakes-branch' ),
			'errors'  => $errors,
			'values'  => $text_values,
		);
	}

	$reference = ! empty( $schema['reference'] )
		? $schema['reference'] . '-' . gmdate( 'Ymd' ) . '-' . strtoupper( wp_generate_password( 4, false, false ) )
		: '';

	$payload = array(
		'form'      => $type,
		'reference' => $reference,
		'submitted' => current_time( 'c' ),
		'site'      => home_url(),
		'fields'    => $values,
	);

	$delivered = db_deliver_submission( $payload, $schema );

	if ( ! $delivered ) {
		return array(
			'code'    => 500,
			'message' => __( 'We could not deliver your form. Please call the town office so your request is not lost.', 'drakes-branch' ),
		);
	}

	$message = $schema['confirm'];

	if ( $reference ) {
		$message .= ' ' . sprintf(
			/* translators: %s: reference number */
			__( 'Your reference number is %s. Please keep it for your records.', 'drakes-branch' ),
			$reference
		);
	}

	return array(
		'code'      => 200,
		'message'   => $message,
		'reference' => $reference,
	);
}

/**
 * REST wrapper around the submission handler.
 *
 * @param WP_REST_Request $request Incoming request.
 * @return WP_REST_Response
 */
function db_handle_submission( WP_REST_Request $request ) {
	$result = db_process_submission(
		(array) $request->get_body_params() + (array) $request->get_params(),
		$request->get_file_params()
	);

	$body = array( 'message' => $result['message'] );

	if ( ! empty( $result['errors'] ) ) {
		$body['errors'] = $result['errors'];
	}

	if ( ! empty( $result['reference'] ) ) {
		$body['reference'] = $result['reference'];
	}

	return new WP_REST_Response( $body, $result['code'] );
}

/**
 * Handles a form posted without JavaScript.
 *
 * Processes the submission, stores the outcome briefly, then redirects back
 * to the page. Redirecting means a browser refresh cannot send the form twice.
 */
function db_handle_post_fallback() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Public
	// form; spam is handled by the honeypot, signed timestamp and rate limit.
	if ( empty( $_POST['form_type'] ) || is_admin() || ! is_singular() ) {
		return;
	}

	$type = sanitize_key( wp_unslash( $_POST['form_type'] ) );

	if ( ! db_form_schema( $type ) ) {
		return;
	}

	$result = db_process_submission( wp_unslash( $_POST ), $_FILES );
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	$key = wp_generate_password( 16, false, false );
	set_transient( 'db_form_result_' . $key, $result, 5 * MINUTE_IN_SECONDS );

	$redirect = add_query_arg( 'form_result', $key, get_permalink() );

	wp_safe_redirect( $redirect . '#form-' . $type, 303 );
	exit;
}
add_action( 'template_redirect', 'db_handle_post_fallback' );

/**
 * Reads the stored outcome of a no-JavaScript submission.
 *
 * @param string $type Form type.
 * @return array Empty when this request is not showing a result.
 */
function db_stored_result( $type ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$key = isset( $_GET['form_result'] ) ? sanitize_key( wp_unslash( $_GET['form_result'] ) ) : '';

	if ( ! $key ) {
		return array();
	}

	$result = get_transient( 'db_form_result_' . $key );

	if ( ! is_array( $result ) ) {
		return array();
	}

	// One read only, so a shared or bookmarked link does not replay it.
	delete_transient( 'db_form_result_' . $key );

	return $result;
}

/**
 * Maps a submission result to its message style.
 *
 * @param array $result Result array.
 * @return string
 */
function db_message_class( $result ) {
	if ( empty( $result['message'] ) ) {
		return '';
	}

	return 200 === (int) $result['code'] ? 'form-message--success' : 'form-message--error';
}

/**
 * Validates and stores an uploaded attachment.
 *
 * @param array $upload PHP file array.
 * @param array $field  Field definition.
 * @return array|WP_Error Stored file details, or an error to show the visitor.
 */
function db_store_upload( $upload, $field ) {
	if ( UPLOAD_ERR_OK !== $upload['error'] ) {
		return new WP_Error( 'upload', __( 'That file did not upload correctly. Please try again.', 'drakes-branch' ) );
	}

	if ( $upload['size'] > db_max_upload_bytes() ) {
		return new WP_Error(
			'too_large',
			sprintf(
				/* translators: %s: maximum file size, for example 8 MB */
				__( 'That file is larger than %s. Please choose a smaller file.', 'drakes-branch' ),
				size_format( db_max_upload_bytes() )
			)
		);
	}

	// Trust the file's real contents, not the name or the browser's guess.
	$check = wp_check_filetype_and_ext( $upload['tmp_name'], $upload['name'] );
	$ext   = $check['ext'] ? $check['ext'] : '';

	if ( ! $ext || ! in_array( strtolower( $ext ), $field['accept'], true ) ) {
		return new WP_Error(
			'wrong_type',
			sprintf(
				/* translators: %s: list of accepted file types */
				__( 'That file type is not accepted. Please upload one of: %s.', 'drakes-branch' ),
				implode( ', ', $field['accept'] )
			)
		);
	}

	db_protect_upload_dir();

	// An unguessable name, so a stored résumé cannot be found by trying URLs.
	$filename = gmdate( 'Y-m-d' ) . '-' . wp_generate_password( 20, false, false ) . '.' . strtolower( $ext );
	$path     = trailingslashit( db_upload_dir() ) . $filename;

	if ( ! move_uploaded_file( $upload['tmp_name'], $path ) ) {
		return new WP_Error( 'store', __( 'We could not save that file. Please try again.', 'drakes-branch' ) );
	}

	return array(
		'original' => sanitize_file_name( $upload['name'] ),
		'stored'   => $filename,
		'path'     => $path,
		'size'     => size_format( $upload['size'] ),
	);
}

/**
 * Sends a submission to N8N, and emails the town if that fails.
 *
 * @param array $payload Submission data.
 * @param array $schema  Form definition.
 * @return bool True when the submission reached at least one destination.
 */
function db_deliver_submission( $payload, $schema ) {
	$webhook   = db_get( $schema['webhook'] );
	$delivered = false;

	if ( $webhook ) {
		// Files are sent as a path reference rather than as bytes; the email
		// carries the actual attachment so the clerk always has a copy.
		$response = wp_remote_post(
			$webhook,
			array(
				'timeout'  => 15,
				'headers'  => array( 'Content-Type' => 'application/json' ),
				'body'     => wp_json_encode( $payload ),
				'blocking' => true,
			)
		);

		$code = is_wp_error( $response ) ? 0 : (int) wp_remote_retrieve_response_code( $response );

		if ( $code >= 200 && $code < 300 ) {
			$delivered = true;
		} else {
			// Recorded so a silent webhook outage is discoverable later.
			error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				sprintf(
					'[drakes-branch] %s webhook failed (%s). Falling back to email.',
					$payload['form'],
					is_wp_error( $response ) ? $response->get_error_message() : $code
				)
			);
		}
	}

	if ( db_email_submission( $payload, $schema ) ) {
		$delivered = true;
	}

	return $delivered;
}

/**
 * Emails a submission to the town office.
 *
 * @param array $payload Submission data.
 * @param array $schema  Form definition.
 * @return bool
 */
function db_email_submission( $payload, $schema ) {
	$to = db_get( 'db_notify_email' );
	$to = $to ? $to : get_option( 'admin_email' );

	if ( ! $to ) {
		return false;
	}

	$lines       = array();
	$attachments = array();
	$reply_to    = '';

	foreach ( $schema['fields'] as $field ) {
		$key   = $field['name'];
		$value = isset( $payload['fields'][ $key ] ) ? $payload['fields'][ $key ] : '';

		if ( is_array( $value ) ) {
			$attachments[] = $value['path'];
			$lines[]       = $field['label'] . ': ' . $value['original'] . ' (' . $value['size'] . ', attached)';
			continue;
		}

		if ( '' === $value ) {
			continue;
		}

		// Show the option's label, not its stored key.
		if ( in_array( $field['type'], array( 'select', 'radio' ), true )
			&& isset( $field['options'][ $value ] ) ) {
			$value = $field['options'][ $value ];
		}

		if ( 'email' === $field['type'] && ! $reply_to ) {
			$reply_to = $value;
		}

		$lines[] = $field['label'] . ': ' . $value;
	}

	$subject = $schema['subject'];

	if ( ! empty( $payload['reference'] ) ) {
		$subject .= ' [' . $payload['reference'] . ']';
	}

	$body = implode( "\n", $lines ) . "\n\n"
		. str_repeat( '-', 40 ) . "\n"
		. __( 'Submitted', 'drakes-branch' ) . ': ' . $payload['submitted'] . "\n"
		. __( 'Sent from', 'drakes-branch' ) . ': ' . $payload['site'] . "\n";

	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

	if ( $reply_to ) {
		$headers[] = 'Reply-To: ' . $reply_to;
	}

	return wp_mail( $to, $subject, $body, $headers, $attachments );
}
