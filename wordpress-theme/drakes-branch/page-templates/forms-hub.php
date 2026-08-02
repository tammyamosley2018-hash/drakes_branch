<?php
/**
 * Template Name: Forms hub
 * Template Post Type: page
 *
 * Lists every form the town offers. Each card links to a page; a card is
 * skipped when its page has not been created yet.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

$db_forms = array(
	array(
		'slug' => 'permits',
		'icon' => 'permit',
		'text' => __( 'Building, utility, sign and demolition permits. Attach your plans and we will be in touch about fees and inspections.', 'drakes-branch' ),
	),
	array(
		'slug' => 'report-a-problem',
		'icon' => 'report',
		'text' => __( 'A street light out, a water problem, a property concern, or a suggestion for the town.', 'drakes-branch' ),
	),
	array(
		'slug' => 'public-records',
		'icon' => 'records',
		'text' => __( 'Request records under the Virginia Freedom of Information Act. The town has five working days to respond.', 'drakes-branch' ),
	),
	array(
		'slug' => 'jobs',
		'icon' => 'jobs',
		'text' => __( 'Apply for an open position with the town. Attach your résumé and tell us why you are a good fit.', 'drakes-branch' ),
	),
	array(
		'slug' => 'contact',
		'icon' => 'contact',
		'text' => __( 'Anything else. Send the town clerk a message and we will reply within two business days.', 'drakes-branch' ),
	),
);

while ( have_posts() ) :
	the_post();

	db_page_banner( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>

	<div class="container section">
		<?php if ( trim( get_the_content() ) ) : ?>
			<div class="prose entry-content">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>

		<ul class="grid grid--3 card-list mt-5">
			<?php
			foreach ( $db_forms as $db_form ) :
				$db_page = get_page_by_path( $db_form['slug'] );

				if ( ! $db_page ) {
					continue;
				}
				?>
				<li>
					<div class="card card--linked">
						<?php db_icon( $db_form['icon'] ); ?>
						<h2 class="card__title">
							<a href="<?php echo esc_url( get_permalink( $db_page ) ); ?>">
								<?php echo esc_html( get_the_title( $db_page ) ); ?>
							</a>
						</h2>
						<p class="card__text"><?php echo esc_html( $db_form['text'] ); ?></p>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<?php
endwhile;

get_footer();
