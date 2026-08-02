<?php
/**
 * Customizer settings.
 *
 * Everything town staff need to change routinely lives here, so updating a
 * phone number or a webhook URL never requires editing a file.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults for every theme setting, in one place.
 *
 * @return array
 */
function db_setting_defaults() {
	return array(
		// Contact block, taken from the town's current website.
		'db_address_street'  => 'P.O. Box 191, 4801 Drakes Branch Main St.',
		'db_address_city'    => 'Drakes Branch, VA 23937',
		'db_phone'           => '434-568-3091',
		'db_fax'             => '',
		'db_email'           => 'drakesbr@hovac.com',
		'db_office_hours'    => 'Monday to Friday, 8:30am to 2:30pm',
		'db_hours_note'      => 'Unless posted otherwise.',

		// Public Works runs its own hours and number; trash questions go here.
		'db_works_phone'     => '434-568-3600',
		'db_works_hours'     => 'Every day, 7:00am to 3:30pm',

		// Shown prominently so an after-hours emergency reaches a person.
		'db_emergency_note'  => 'In an emergency, call the Waste Water Supervisor at 607-644-4981 or the Mayor at 434-568-3028.',

		// Links.
		'db_pay_url'         => 'https://drakesbranch-revmgt.secure.openrda.net/portal/',
		'db_facebook_url'    => 'https://www.facebook.com/profile.php?id=61583808658304',
		'db_ordinance_url'   => '',
		'db_github_url'      => '',

		// Homepage hero.
		'db_hero_eyebrow'    => 'Charlotte County, Virginia',
		'db_hero_title'      => 'Everything you need from the town, in one place.',
		'db_hero_lead'       => 'Pay a bill, request a permit, read the minutes, or reach the clerk directly. If you cannot find what you need here, call the office — someone will answer.',
		'db_hero_image'      => 0,
		'db_hero_caption'    => '',
		'db_hero_cta_label'  => 'Pay my bill',
		'db_hero_cta2_label' => 'Contact the town office',

		// Meeting defaults, used when a meeting record leaves them blank.
		'db_meeting_location' => 'Drakes Branch Town Hall',
		'db_meeting_note'     => 'Council meetings are open to the public.',

		// Footer.
		'db_footer_note'     => '',

		// N8N webhook URLs. Stored server-side and never printed to the page.
		'db_webhook_contact' => '',
		'db_webhook_permit'  => '',
		'db_webhook_feedback' => '',
		'db_webhook_foia'    => '',
		'db_webhook_jobs'    => '',

		// Where form notifications are sent if the webhook is unset or fails.
		'db_notify_email'    => '',
	);
}

/**
 * Reads a theme setting, falling back to its default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function db_get( $key ) {
	$defaults = db_setting_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * Registers Customizer panels, sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function db_customize_register( $wp_customize ) {
	$defaults = db_setting_defaults();

	$wp_customize->add_panel(
		'db_town',
		array(
			'title'       => __( 'Town settings', 'drakes-branch' ),
			'description' => __( 'Contact details, links and form delivery for the town website.', 'drakes-branch' ),
			'priority'    => 20,
		)
	);

	/**
	 * Adds a text-like control in one call.
	 *
	 * @param string $id      Setting id.
	 * @param string $section Section id.
	 * @param string $label   Control label.
	 * @param string $type    Control type.
	 * @param string $help    Description shown under the control.
	 */
	$add = function ( $id, $section, $label, $type = 'text', $help = '' ) use ( $wp_customize, $defaults ) {
		$sanitize = 'sanitize_text_field';

		if ( 'url' === $type ) {
			$sanitize = 'esc_url_raw';
		} elseif ( 'email' === $type ) {
			$sanitize = 'sanitize_email';
		} elseif ( 'textarea' === $type ) {
			$sanitize = 'wp_kses_post';
		}

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => isset( $defaults[ $id ] ) ? $defaults[ $id ] : '',
				'sanitize_callback' => $sanitize,
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$id,
			array(
				'label'       => $label,
				'section'     => $section,
				'type'        => $type,
				'description' => $help,
			)
		);
	};

	/* ----------------------------------------------------------------
	   Contact details
	   ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'db_contact',
		array(
			'title' => __( 'Contact details', 'drakes-branch' ),
			'panel' => 'db_town',
		)
	);

	$add( 'db_address_street', 'db_contact', __( 'Street or PO Box', 'drakes-branch' ) );
	$add( 'db_address_city', 'db_contact', __( 'City, state and ZIP', 'drakes-branch' ) );
	$add( 'db_phone', 'db_contact', __( 'Phone', 'drakes-branch' ), 'text', __( 'Shown in the footer and on the contact page.', 'drakes-branch' ) );
	$add( 'db_fax', 'db_contact', __( 'Fax', 'drakes-branch' ) );
	$add( 'db_email', 'db_contact', __( 'Public email address', 'drakes-branch' ), 'email' );
	$add( 'db_office_hours', 'db_contact', __( 'Office hours', 'drakes-branch' ), 'text', __( 'For example: Monday to Friday, 8:30am to 2:30pm.', 'drakes-branch' ) );
	$add( 'db_hours_note', 'db_contact', __( 'Note about hours', 'drakes-branch' ), 'text', __( 'Optional. For example: Unless posted otherwise.', 'drakes-branch' ) );
	$add( 'db_works_phone', 'db_contact', __( 'Public Works phone', 'drakes-branch' ), 'text', __( 'The number residents call about trash collection.', 'drakes-branch' ) );
	$add( 'db_works_hours', 'db_contact', __( 'Public Works hours', 'drakes-branch' ), 'text' );
	$add( 'db_emergency_note', 'db_contact', __( 'After-hours emergency note', 'drakes-branch' ), 'textarea', __( 'Shown on the contact page and in the footer. Include the numbers to call outside office hours.', 'drakes-branch' ) );

	/* ----------------------------------------------------------------
	   Links
	   ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'db_links',
		array(
			'title' => __( 'Important links', 'drakes-branch' ),
			'panel' => 'db_town',
		)
	);

	$add( 'db_pay_url', 'db_links', __( 'Pay my bill URL', 'drakes-branch' ), 'url', __( 'The RDA payment portal. Leave blank to hide the payment button.', 'drakes-branch' ) );
	$add( 'db_facebook_url', 'db_links', __( 'Facebook page URL', 'drakes-branch' ), 'url' );
	$add( 'db_ordinance_url', 'db_links', __( 'Ordinance search URL', 'drakes-branch' ), 'url' );
	$add( 'db_github_url', 'db_links', __( 'Document archive URL', 'drakes-branch' ), 'url', __( 'The GitHub repository holding minutes and agendas.', 'drakes-branch' ) );

	/* ----------------------------------------------------------------
	   Homepage
	   ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'db_home',
		array(
			'title'       => __( 'Homepage', 'drakes-branch' ),
			'panel'       => 'db_town',
			'description' => __( 'The opening section of the front page.', 'drakes-branch' ),
		)
	);

	$add( 'db_hero_eyebrow', 'db_home', __( 'Small label above the headline', 'drakes-branch' ) );
	$add( 'db_hero_title', 'db_home', __( 'Headline', 'drakes-branch' ) );
	$add( 'db_hero_lead', 'db_home', __( 'Opening paragraph', 'drakes-branch' ), 'textarea' );
	$add( 'db_hero_caption', 'db_home', __( 'Photo caption', 'drakes-branch' ), 'text', __( 'Describes the photo for sighted readers. Set the alt text in the media library.', 'drakes-branch' ) );
	$add( 'db_hero_cta_label', 'db_home', __( 'First button label', 'drakes-branch' ) );
	$add( 'db_hero_cta2_label', 'db_home', __( 'Second button label', 'drakes-branch' ) );

	$wp_customize->add_setting(
		'db_hero_image',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'db_hero_image',
			array(
				'label'       => __( 'Homepage photo', 'drakes-branch' ),
				'section'     => 'db_home',
				'mime_type'   => 'image',
				'description' => __( 'Add alt text in the media library so screen reader users get a description.', 'drakes-branch' ),
			)
		)
	);

	/* ----------------------------------------------------------------
	   Meetings
	   ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'db_meetings',
		array(
			'title'       => __( 'Meetings', 'drakes-branch' ),
			'panel'       => 'db_town',
			'description' => __( 'Defaults used when a meeting record leaves a field blank.', 'drakes-branch' ),
		)
	);

	$add( 'db_meeting_location', 'db_meetings', __( 'Usual meeting location', 'drakes-branch' ) );
	$add( 'db_meeting_note', 'db_meetings', __( 'Note shown with meeting listings', 'drakes-branch' ) );

	/* ----------------------------------------------------------------
	   Footer
	   ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'db_footer',
		array(
			'title' => __( 'Footer', 'drakes-branch' ),
			'panel' => 'db_town',
		)
	);

	$add( 'db_footer_note', 'db_footer', __( 'Extra footer text', 'drakes-branch' ), 'textarea' );

	/* ----------------------------------------------------------------
	   Form delivery
	   ---------------------------------------------------------------- */

	$wp_customize->add_section(
		'db_forms',
		array(
			'title'       => __( 'Form delivery', 'drakes-branch' ),
			'panel'       => 'db_town',
			'description' => __( 'Each form is sent to its N8N webhook from the server, so these addresses are never visible to visitors. If a webhook is blank or unreachable, the submission is emailed to the notification address instead and nothing is lost.', 'drakes-branch' ),
		)
	);

	$add( 'db_notify_email', 'db_forms', __( 'Notification email', 'drakes-branch' ), 'email', __( 'Where form submissions go if a webhook is not set or fails.', 'drakes-branch' ) );
	$add( 'db_webhook_contact', 'db_forms', __( 'Contact form webhook', 'drakes-branch' ), 'url' );
	$add( 'db_webhook_permit', 'db_forms', __( 'Permit request webhook', 'drakes-branch' ), 'url' );
	$add( 'db_webhook_feedback', 'db_forms', __( 'Complaint and feedback webhook', 'drakes-branch' ), 'url' );
	$add( 'db_webhook_foia', 'db_forms', __( 'FOIA request webhook', 'drakes-branch' ), 'url' );
	$add( 'db_webhook_jobs', 'db_forms', __( 'Job application webhook', 'drakes-branch' ), 'url' );

	// Live preview for the hero text.
	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->get_setting( 'db_hero_title' )->transport = 'postMessage';
		$wp_customize->selective_refresh->add_partial(
			'db_hero_title',
			array(
				'selector'        => '.hero__title',
				'render_callback' => function () {
					return esc_html( db_get( 'db_hero_title' ) );
				},
			)
		);
	}
}
add_action( 'customize_register', 'db_customize_register' );
