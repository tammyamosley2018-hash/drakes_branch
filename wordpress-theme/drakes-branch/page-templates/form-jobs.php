<?php
/**
 * Template Name: Form: Job application
 * Template Post Type: page
 *
 * Apply for a position.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	db_page_banner( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );

	get_template_part( 'template-parts/form-page', null, array( 'type' => 'jobs' ) );

endwhile;

get_footer();
