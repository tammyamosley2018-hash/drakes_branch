/**
 * Where the standalone forms send their submissions.
 *
 * This is the only file you need to edit to connect them.
 *
 * Point `endpoint` at the WORDPRESS SITE, not directly at N8N. The site
 * validates the submission, keeps the webhook address out of the page source
 * where it cannot be scraped and spammed, and emails the town office if N8N is
 * unreachable so nothing is silently lost.
 *
 * Posting straight to an N8N webhook would put that URL in front of every
 * visitor and skip all of the above. Only do it if these forms must run with
 * no WordPress site behind them at all.
 */
window.DRAKES_BRANCH_FORM = {
	endpoint: 'https://towndrakesbranch.com/wp-json/drakes-branch/v1/submit',

	// Keep in step with the theme's limit.
	maxUploadBytes: 8 * 1024 * 1024
};
