<?php
/**
 * The service card deck.
 *
 * Each card points at a page by slug. A card is skipped when its page does
 * not exist, so the homepage never shows a link to a missing page. To change
 * the wording, edit the array below; to change where a card points, rename
 * the page slug to match.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

$db_cards = array(
	array(
		'slug' => 'pay-my-bill',
		'icon' => 'bill',
		'text' => __( 'Pay a water or sewer bill online, by mail, or in person at the town office.', 'drakes-branch' ),
	),
	array(
		'slug' => 'permits',
		'icon' => 'permit',
		'text' => __( 'Apply for a building, utility, sign or demolition permit.', 'drakes-branch' ),
	),
	array(
		'slug' => 'report-a-problem',
		'icon' => 'report',
		'text' => __( 'Tell us about a street light, a water problem, or anything else that needs attention.', 'drakes-branch' ),
	),
	array(
		'slug' => 'meetings',
		'icon' => 'meetings',
		'text' => __( 'Agendas, minutes and recordings of town council meetings.', 'drakes-branch' ),
		'url'  => get_post_type_archive_link( 'meeting' ),
	),
	array(
		'slug' => 'public-records',
		'icon' => 'records',
		'text' => __( 'Request records under the Virginia Freedom of Information Act.', 'drakes-branch' ),
	),
	array(
		'slug' => 'contact',
		'icon' => 'contact',
		'text' => __( 'Reach the town clerk by phone, email, or the contact form.', 'drakes-branch' ),
	),
);
?>

<ul class="grid grid--3 card-list">
	<?php
	foreach ( $db_cards as $db_card ) :
		$db_page  = get_page_by_path( $db_card['slug'] );
		$db_url   = ! empty( $db_card['url'] ) ? $db_card['url'] : ( $db_page ? get_permalink( $db_page ) : '' );
		$db_title = $db_page ? get_the_title( $db_page ) : '';

		// A meetings archive has no page object, so fall back to a label.
		if ( ! $db_title && ! empty( $db_card['url'] ) ) {
			$db_title = __( 'Meetings and minutes', 'drakes-branch' );
		}

		if ( ! $db_url || ! $db_title ) {
			continue;
		}
		?>
		<li>
			<div class="card card--linked">
				<?php db_icon( $db_card['icon'] ); ?>
				<h3 class="card__title">
					<a href="<?php echo esc_url( $db_url ); ?>"><?php echo esc_html( $db_title ); ?></a>
				</h3>
				<p class="card__text"><?php echo esc_html( $db_card['text'] ); ?></p>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
