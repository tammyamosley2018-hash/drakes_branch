<?php
/**
 * Homepage.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

$db_hero_image = (int) db_get( 'db_hero_image' );
$db_pay_url    = db_get( 'db_pay_url' );
$db_contact    = get_page_by_path( 'contact' );
?>

<section class="hero">
	<div class="container">
		<div class="hero__grid">

			<div>
				<?php if ( db_get( 'db_hero_eyebrow' ) ) : ?>
					<p class="hero__eyebrow"><?php echo esc_html( db_get( 'db_hero_eyebrow' ) ); ?></p>
				<?php endif; ?>

				<h1 class="hero__title"><?php echo esc_html( db_get( 'db_hero_title' ) ); ?></h1>

				<?php if ( db_get( 'db_hero_lead' ) ) : ?>
					<p class="lead"><?php echo esc_html( wp_strip_all_tags( db_get( 'db_hero_lead' ) ) ); ?></p>
				<?php endif; ?>

				<div class="hero__actions">
					<?php if ( $db_pay_url ) : ?>
						<a class="btn" href="<?php echo esc_url( $db_pay_url ); ?>">
							<?php echo esc_html( db_get( 'db_hero_cta_label' ) ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $db_contact ) : ?>
						<a class="btn btn--secondary" href="<?php echo esc_url( get_permalink( $db_contact ) ); ?>">
							<?php echo esc_html( db_get( 'db_hero_cta2_label' ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $db_hero_image ) : ?>
				<figure class="hero__figure">
					<?php
					echo wp_get_attachment_image(
						$db_hero_image,
						'db-hero',
						false,
						array(
							// Above the fold, so it is fetched eagerly and early.
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'decoding'      => 'async',
						)
					);
					?>
					<?php if ( db_get( 'db_hero_caption' ) ) : ?>
						<figcaption class="hero__caption">
							<?php echo esc_html( db_get( 'db_hero_caption' ) ); ?>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>

		</div>
	</div>
</section>

<div class="container section">
	<div class="with-rail">

		<div class="with-rail__main stack">

			<section aria-labelledby="services-heading">
				<div class="section__head">
					<h2 id="services-heading"><?php esc_html_e( 'What do you need to do?', 'drakes-branch' ); ?></h2>
				</div>
				<?php get_template_part( 'template-parts/service-cards' ); ?>
			</section>

			<?php
			$db_news = new WP_Query(
				array(
					'post_type'      => 'post',
					'post_status'    => 'publish',
					'posts_per_page' => 4,
					'no_found_rows'  => true,
				)
			);
			?>

			<?php if ( $db_news->have_posts() ) : ?>
				<section aria-labelledby="news-heading">
					<div class="section__head">
						<h2 id="news-heading"><?php esc_html_e( 'Announcements', 'drakes-branch' ); ?></h2>
					</div>

					<?php
					while ( $db_news->have_posts() ) :
						$db_news->the_post();
						?>
						<article class="dated">
							<p class="dated__date">
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
									<?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
								</time>
							</p>

							<div>
								<h3 class="dated__title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>
								<p class="dated__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
							</div>
						</article>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>

					<?php if ( get_option( 'page_for_posts' ) ) : ?>
						<p class="mt-5">
							<a class="arrow-link" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>">
								<?php esc_html_e( 'All announcements', 'drakes-branch' ); ?>
							</a>
						</p>
					<?php endif; ?>
				</section>
			<?php endif; ?>

		</div>

		<aside class="with-rail__rail" aria-label="<?php esc_attr_e( 'Meetings and town office', 'drakes-branch' ); ?>">
			<?php get_template_part( 'template-parts/meeting-rail' ); ?>
		</aside>

	</div>
</div>

<?php
// The page's own editor content becomes the closing "about the town" section,
// so staff can rewrite it without touching a template.
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		if ( trim( get_the_content() ) ) :
			?>
			<section class="section section--sand">
				<div class="container">
					<div class="prose entry-content">
						<?php the_content(); ?>
					</div>
				</div>
			</section>
			<?php
		endif;
	endwhile;
endif;

get_footer();
