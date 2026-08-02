<?php
/**
 * Search form.
 *
 * The label is visible rather than a placeholder, so the field keeps its name
 * once someone starts typing.
 *
 * @package DrakesBranch
 */

defined( 'ABSPATH' ) || exit;

$db_search_id = 'search-' . wp_unique_id();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="field__label" for="<?php echo esc_attr( $db_search_id ); ?>">
		<?php esc_html_e( 'Search this site', 'drakes-branch' ); ?>
	</label>

	<div class="search-form__row">
		<input type="search" class="field__control" id="<?php echo esc_attr( $db_search_id ); ?>"
			name="s" value="<?php echo esc_attr( get_search_query() ); ?>">
		<button type="submit" class="btn"><?php esc_html_e( 'Search', 'drakes-branch' ); ?></button>
	</div>
</form>
