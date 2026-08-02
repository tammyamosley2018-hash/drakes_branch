<?php
/**
 * Template Name: Form: Permit request
 * Template Post Type: page
 *
 * Request a permit.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	db_page_banner( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );

	get_template_part( 'template-parts/form-page', null, array( 'type' => 'permit' ) );

endwhile;

get_footer();
