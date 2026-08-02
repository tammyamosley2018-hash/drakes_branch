# Standalone HTML forms

Five civic forms as plain HTML pages, styled to match the town website. They can
be hosted anywhere — GitHub Pages, the town server, or opened straight from a
folder — and they do not need WordPress to display.

| File | Form |
|---|---|
| `contact.html` | Contact the town office |
| `permit-request.html` | Request a permit |
| `report-a-problem.html` | Report a problem or share feedback |
| `foia-request.html` | Request public records (FOIA) |
| `job-application.html` | Apply for a position |

Fields, wording, and confirmation messages are identical to the versions built
into the WordPress theme, so a resident sees the same form either way.

## Connecting them

Edit **`assets/config.js`** — it is the only file you need to change:

```js
window.DRAKES_BRANCH_FORM = {
  endpoint: 'https://towndrakesbranch.com/wp-json/drakes-branch/v1/submit'
};
```

### Point them at the website, not at N8N

The endpoint above is the town's own WordPress site, which then forwards to
N8N. Please keep it that way. Sending straight to an N8N webhook would:

- **put the webhook address in the page source**, where anyone can read it and
  post junk straight into the town's spreadsheets, bypassing the site entirely;
- **skip server-side validation**, so nothing checks what arrives;
- **lose the email fallback** — the WordPress route emails the town office when
  N8N is unreachable, so a resident's request is never silently dropped.

Only post directly to N8N if these pages must run with no WordPress site behind
them at all, and understand that the webhook becomes public if you do.

### If they are hosted on a different domain

A form on `github.io` posting to `towndrakesbranch.com` is a cross-origin
request, and the browser will block it unless the site sends CORS headers. Two
options:

1. **Host them on the town's own domain** — simplest, nothing to configure.
2. **Allow the origin** in the theme, by adding the form's domain to the
   `Access-Control-Allow-Origin` header on the submit route.

Until one of those is done, submissions from another domain will fail with the
"We could not reach the town office" message, which tells the resident to call
instead.

## What is in the box

- `assets/form.css` — a self-contained subset of the site's design system.
  Token values are copied from the theme's `theme.css`; if a colour changes
  there, change it here too.
- `assets/form.js` — validation and submission.
- `assets/fonts/` — the same self-hosted webfonts as the site, so no visitor
  data goes to a font CDN.
- `assets/town-seal.png` — the town seal in the masthead.

## Accessibility

Built to the same standard as the site: real `<label>` elements, required
fields marked with a symbol *and* text rather than colour alone, radio groups
wrapped in a `<fieldset>` with a `<legend>`, errors announced with
`role="alert"`, and a summary at the top of a failed form that moves focus and
links to each problem field.

Verified on `foia-request.html`: submitting an empty form reports five problems
(radio groups counted once, not once per option), focus moves to the summary,
and every summary link resolves to a real field.

**These pages need JavaScript.** A plain HTML file has no server to post to, so
there is no no-JS fallback here — unlike the WordPress versions, which work
with JavaScript turned off. Every page carries the office phone number and
email beneath the form for anyone who cannot use it.
