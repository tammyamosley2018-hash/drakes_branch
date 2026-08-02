# Drakes Branch theme — setup guide

The custom WordPress theme for the Town of Drakes Branch. Everything the town
changes routinely lives in **Appearance → Customize → Town settings**, so no
one needs to edit a file to update a phone number or a webhook.

---

## 1. Install the theme

1. Zip the `drakes-branch` folder (the one containing `style.css`).
2. In WordPress: **Appearance → Themes → Add New → Upload Theme**.
3. Upload the zip, then click **Activate**.

Uploading through cPanel or FTP instead? Put the `drakes-branch` folder in
`wp-content/themes/`, then activate it under **Appearance → Themes**.

Activating the theme creates a private folder for form attachments at
`wp-content/uploads/town-forms/`, protected so résumés and permit drawings
cannot be downloaded by anyone guessing a web address.

---

## 2. Check the town's details

**Appearance → Customize → Town settings**. These are already filled in from
the current towndrakesbranch.com, so check them rather than typing them fresh:

| Setting | Value carried over |
|---|---|
| Address | P.O. Box 191, 4801 Drakes Branch Main St., Drakes Branch, VA 23937 |
| Phone | 434-568-3091 |
| Email | drakesbr@hovac.com |
| Office hours | Monday to Friday, 8:30am to 2:30pm — *unless posted otherwise* |
| Public Works | Every day 7:00am–3:30pm, 434-568-3600 (missed trash collection) |
| After hours | Waste Water Supervisor 607-644-4981, Mayor 434-568-3028 |
| Pay my bill | `https://drakesbranch-revmgt.secure.openrda.net/portal/` |

Still blank, because the current site does not list them: **fax number**,
**Facebook page URL**, **ordinance search URL**, and the five **N8N webhooks**.

Anything left empty is hidden rather than shown as a placeholder, so a blank
Facebook field simply means no Facebook link appears.

The **after-hours numbers appear in two places** — a card in the homepage
sidebar and a highlighted band in the footer — because a sewer backup at 9pm is
exactly what a town website should answer immediately.

### The town seal

The circular seal was taken from the current site's logo banner and cropped to
`assets/images/town-seal.png`. If you have a higher-resolution original, drop it
in at the same path; the disc must touch all four edges of a square image, since
the stylesheet rounds the corners off rather than relying on transparency.

---

## 3. Create the pages

Create each page under **Pages → Add New**, set its **slug** exactly as listed,
and pick the **Template** from the Page Attributes box on the right.

| Page title | Slug (must match) | Template |
|---|---|---|
| Pay My Bill | `pay-my-bill` | Pay my bill |
| Permits | `permits` | Form: Permit request |
| Report a Problem | `report-a-problem` | Form: Report a problem |
| Public Records | `public-records` | Form: Public records |
| Jobs | `jobs` | Form: Job application |
| Contact | `contact` | Form: Contact |
| Forms | `forms` | Forms hub |
| Video Archive | `video-archive` | Video archive |
| About | `about` | *(default)* |
| Services | `services` | *(default)* |
| Accessibility | `accessibility` | *(default)* |

The slugs matter: the homepage cards and the forms hub look pages up by slug and
**skip any card whose page does not exist yet**. That means no broken links
while you are still building — but it also means a card stays missing until its
page is published with the right slug.

Anything you type into the page's own editor appears **above** the form, which
is where to put fees, deadlines, or the list of open positions.

### Set the homepage

**Settings → Reading → Your homepage displays → A static page.** Choose your
home page, and set "Posts page" to a page called Announcements or News.

### Menus

**Appearance → Menus.** Four locations are available: Primary navigation,
Utility bar, Footer links, and Footer legal links. Until you build a primary
menu the theme shows a basic fallback so the site is never unnavigable.

---

## 4. Meetings

Meetings are their own section in the admin sidebar. **Add one record per
meeting** — the homepage panel, the meetings archive, and the video archive all
read from it, so you never enter the same meeting twice.

Fields, in the "Meeting details" box:

- **Meeting date** — required. Everything is ordered and filed by this date.
- **Start time**, **Location** — location falls back to the Customizer default.
- **Agenda / Minutes document URL** — upload the PDF to the Media Library, copy
  its link, paste it here.
- **Recording URL** — the YouTube link. Turn captions on before publishing.
- **Transcript URL** — optional, but the most accessible option of all.
- **Cancelled** — keeps the meeting listed and clearly marks it as cancelled,
  rather than deleting it.

A meeting moves from "upcoming" to "past" on its own, based on the date. There
is nothing to switch over.

---

## 5. Connecting the forms to N8N

Each form posts to **this website first**, and the site then forwards it to
N8N. This matters:

- The webhook address is never visible in the page source, so it cannot be
  spammed directly.
- Validation cannot be skipped by posting straight to the webhook.
- **If N8N is down or the webhook is blank, the submission is emailed to the
  notification address instead.** A resident's request is never silently lost.

In each N8N workflow, add a **Webhook** node set to **POST**, copy its
production URL, and paste it into the matching field under **Customize → Town
settings → Form delivery**.

The site sends JSON shaped like this:

```json
{
  "form": "permit",
  "reference": "PR-20260801-K4T9",
  "submitted": "2026-08-01T14:22:05-04:00",
  "site": "https://towndrakesbranch.com",
  "fields": {
    "name": "Sam Whitlow",
    "email": "sam@example.com",
    "property_address": "12 Sycamore Street",
    "permit_type": "building",
    "description": "Replacing the back porch."
  }
}
```

Point the workflow at a Google Sheet and an email node from there. Permit, FOIA
and job submissions include a `reference` number that is also shown to the
person who submitted the form.

**Attachments** (résumés, permit drawings) are emailed to the notification
address as real attachments and stored privately on the server. They are not
sent to N8N as files, so nothing confidential passes through a third party.

### Testing a form

Submit one yourself. You should get the confirmation message on screen, a row
in the Google Sheet, and an email. If the email arrives but the sheet row does
not, the webhook is wrong or N8N is down — the fallback did its job.

---

## 6. Accessibility

The theme was built to WCAG 2.1 Level AA. Verified so far, on the rendered page:

- **Colour contrast** — 131 text elements checked, zero failures, lowest ratio
  4.75:1 against a 4.5:1 requirement.
- **Non-text contrast (1.4.11)** — form field and pagination borders sit at
  3.68:1 against the page, above the 3:1 minimum. This was a real failure at
  first pass (1.73:1) and was fixed by darkening the control border.
- **Keyboard** — the mobile menu and submenus open and close with the keyboard,
  Escape closes the innermost one first, and focus returns to the button that
  opened it. No keyboard trap.
- **Structure** — no skipped heading levels, every image has alt text, every
  form field has a real `<label>`, and the page has banner / main / contentinfo
  landmarks.
- **Targets** — service cards are clickable across their whole area (327×170
  pixels), well above the 44×44 minimum.

Still to verify once the site is on a real server: screen reader testing with
VoiceOver or NVDA, and a full axe-core run against live pages.

**When you add content, the two things that matter most:**

1. Give every image alt text that says what it shows. If it is purely
   decorative, leave the alt text empty rather than describing it.
2. Use the heading levels in order — Heading 2, then Heading 3 beneath it.
   Don't skip a level to get a smaller size.

---

## 7. Previewing the design without WordPress

`_preview.html` renders the theme's stylesheet with sample content, so you can
check design changes without a WordPress install:

```bash
node /private/tmp/claude-501/-Users-tammy-Desktop-Claude-Code-Drakes-Branch/bba5acd3-802e-4363-a208-baec6a572f65/scratchpad/server.js
```

Then open `http://localhost:8765`. This file is outside the `drakes-branch`
folder, so it is never part of the theme you upload.

---

---

## 8. Town officials

**Officials** in the admin menu holds one record per person. **Bodies** groups
them — Town Council, Town Office, Public Works, Planning Commission, Board of
Zoning Appeals. Somebody who serves on two bodies is entered once and ticked
into both, so Janice Wells appears under both commissions from a single record.

Create a page, set its template to **Town officials**, and everyone appears
grouped and in order.

- **Order within a body** — the **Order** box under Page Attributes. Mayor 1,
  Vice-Mayor 2, and so on.
- **Order of the bodies themselves** — the **Display order** field on each body
  under Officials → Bodies.
- **Role** — leave blank for an ordinary member; fill it in for Mayor, Clerk,
  Secretary.
- **Photograph** — the Featured image box. Without one, the person's initials
  appear in a circle.

### The photographs

The eight portraits you supplied are cropped square, centred on the face, and
bundled at `assets/images/officials/`, named after each person's slug. The page
uses them automatically — **there is nothing to upload**.

A Featured image always wins over the bundled file, so replacing a photo later
is an ordinary WordPress edit. Delete the file and the person falls back to
their initials.

Sixteen people have no photograph yet and show initials.

### Don't generate portraits of real people

The original brief suggested AI-generated photos for this page. Please don't.
A synthetic portrait beside a real council member's name reads as a photograph
of that person, and a resident has no way to tell it isn't. That is misleading
in a way the rest of the site works hard not to be — and if it were noticed,
it would undermine trust in everything else on the page.

Use real photographs taken with each person's knowledge, or leave the initials.
The initials are a deliberate design, not a gap: a page of them looks
consistent and intentional. Generated imagery is fine for a hero photograph of
a streetscape, where nobody is being depicted.

### Loading the 23 people already on the site

`import/drakes-branch-officials.xml` has everyone from the current site, with
roles, groupings and ranking. **Tools → Import → WordPress**, upload it, done.
Install the WordPress Importer plugin first if prompted.

Two things to check afterwards, both inherited from the current site:

- The clerk is listed as **"Vickie Cliborne"** in one place and **"Vicki"** in
  another. The import uses Vickie. Correct it if that is wrong.
- Roles and membership are as published, which may be out of date. Worth one
  pass against the current council before this page goes live.

---

## 9. Migrated page content

`import/page-content.md` holds About, Services, Links and Pay My Bill, rewritten
for structure but not for meaning, ready to paste in. Every change I made is
flagged in the file so you can undo any of them.

While migrating I checked all nineteen external links. One is dead
(`vagenweb.org` is now a parked domain), two point at pages that have moved —
including Charlotte County, which is now on a `.gov` address — and four were
plain `http://`. Details are in the same file.

The two PDFs on the About page still point at the old site. Re-upload them to
the new media library before launch or they break when the old site goes away.

---

## 10. Colours and type

The primary is the town's own logo navy, deepened slightly so it holds a
full-width header without glare.

| Token | Value | Used for |
|---|---|---|
| `--branch` | `#16265E` | Header, buttons, links |
| `--ink` | `#131A26` | Body text, footer background |
| `--pine` | `#2F5D3F` | The meeting card's top rule |
| `--clay` | `#A9553A` | Dated rules, current page marker, focus ring |
| `--paper` | `#F7F5F2` | Page background |

Clay is deliberately rare. It marks the date on an announcement, the page you
are currently on, and the keyboard focus ring — nothing else. Used on every
link it stops reading as an accent.

Type is **Zilla Slab** for headings over **Source Sans 3** for text, both
self-hosted in `assets/fonts/` so no visitor data goes to a font CDN.

---

## What is not built yet

- **Photography** — the homepage hero has no image yet. The current site's
  carousel has usable subjects (Main Street, the warehouse, Twitty's Creek, the
  fire department and town offices, the museum) if you would rather use real
  photos of the town than generated ones.
