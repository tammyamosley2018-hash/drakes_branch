<?php
/**
 * Template Name: Town officials
 *
 * Lists everyone who serves the town, grouped by body.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

get_header();

$db_bodies = db_get_bodies();

the_post();
db_page_banner( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
?>

<div class="container section">
	<?php if ( trim( get_the_content() ) ) : ?>
		<div class="prose measure mb-8"><?php the_content(); ?></div>
	<?php endif; ?>

	<?php if ( ! $db_bodies ) : ?>
		<p class="prose measure">
			<?php esc_html_e( 'The list of officials has not been added yet.', 'drakes-branch' ); ?>
		</p>
	<?php else : ?>

		<?php // A short index, because this page is long on a phone. ?>
		<nav class="jump-nav" aria-label="<?php esc_attr_e( 'On this page', 'drakes-branch' ); ?>">
			<h2 class="jump-nav__title"><?php esc_html_e( 'On this page', 'drakes-branch' ); ?></h2>
			<ul class="jump-nav__list">
				<?php foreach ( $db_bodies as $db_body ) : ?>
					<li>
						<a href="#body-<?php echo esc_attr( $db_body->slug ); ?>">
							<?php echo esc_html( $db_body->name ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="stack">
			<?php foreach ( $db_bodies as $db_body ) : ?>
				<?php $db_people = db_get_officials( $db_body->term_id ); ?>

				<?php if ( $db_people->have_posts() ) : ?>
					<section aria-labelledby="body-<?php echo esc_attr( $db_body->slug ); ?>">
						<div class="section__head">
							<h2 id="body-<?php echo esc_attr( $db_body->slug ); ?>">
								<?php echo esc_html( $db_body->name ); ?>
							</h2>
						</div>

						<?php if ( $db_body->description ) : ?>
							<p class="prose measure mb-6"><?php echo esc_html( $db_body->description ); ?></p>
						<?php endif; ?>

						<ul class="grid grid--3 card-list">
							<?php
							while ( $db_people->have_posts() ) :
								$db_people->the_post();

								$db_role   = db_official_field( 'role' );
								$db_email  = db_official_field( 'email' );
								$db_phone  = db_official_field( 'phone' );
								$db_term   = db_official_field( 'term_ends' );
								$db_photo  = db_official_bundled_photo();
								?>
								<li>
									<div class="person">
										<?php if ( has_post_thumbnail() ) : ?>
											<?php
											the_post_thumbnail(
												'db-portrait',
												array(
													'class'   => 'person__photo',
													'alt'     => '',
													'loading' => 'lazy',
												)
											);
											?>
										<?php elseif ( $db_photo ) : ?>
											<?php // Alt is empty: the name sits beside it as real text. ?>
											<img class="person__photo" src="<?php echo esc_url( $db_photo ); ?>"
												width="400" height="400" alt="" loading="lazy" decoding="async">
										<?php else : ?>
											<?php // Initials rather than a stock silhouette, which reads as a missing image. ?>
											<span class="person__initials" aria-hidden="true">
												<?php echo esc_html( db_official_initials( get_the_title() ) ); ?>
											</span>
										<?php endif; ?>

										<div class="person__body">
											<h3 class="person__name"><?php the_title(); ?></h3>

											<?php if ( $db_role ) : ?>
												<p class="person__role"><?php echo esc_html( $db_role ); ?></p>
											<?php endif; ?>

											<?php if ( $db_term ) : ?>
												<p class="person__meta">
													<?php
													printf(
														/* translators: %s: when the term of office ends */
														esc_html__( 'Term ends %s', 'drakes-branch' ),
														esc_html( $db_term )
													);
													?>
												</p>
											<?php endif; ?>

											<?php if ( $db_email || $db_phone ) : ?>
												<ul class="person__contact">
													<?php if ( $db_phone ) : ?>
														<li>
															<a href="<?php echo esc_url( db_tel_href( $db_phone ) ); ?>">
																<?php echo esc_html( $db_phone ); ?>
															</a>
														</li>
													<?php endif; ?>
													<?php if ( $db_email ) : ?>
														<li>
															<a href="mailto:<?php echo esc_attr( $db_email ); ?>">
																<?php echo esc_html( $db_email ); ?>
															</a>
														</li>
													<?php endif; ?>
												</ul>
											<?php endif; ?>

											<?php if ( get_the_content() ) : ?>
												<div class="person__note"><?php the_content(); ?></div>
											<?php endif; ?>
										</div>
									</div>
								</li>
								<?php
							endwhile;
							wp_reset_postdata();
							?>
						</ul>
					</section>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<?php // Most residents want the office, not an individual. ?>
		<div class="callout mt-8">
			<h2 class="callout__title"><?php esc_html_e( 'Getting in touch', 'drakes-branch' ); ?></h2>
			<p class="mb-0">
				<?php
				printf(
					/* translators: 1: phone link, 2: contact page link */
					esc_html__( 'For most matters the town office is the fastest route: %1$s, or %2$s.', 'drakes-branch' ),
					'<a href="' . esc_url( db_tel_href( db_get( 'db_phone' ) ) ) . '">' . esc_html( db_get( 'db_phone' ) ) . '</a>',
					'<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'send a message', 'drakes-branch' ) . '</a>'
				);
				?>
			</p>
		</div>
	<?php endif; ?>
</div>

<?php
get_footer();
