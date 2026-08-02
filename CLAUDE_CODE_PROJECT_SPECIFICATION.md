# Town of Drakes Branch Website Rebuild
## Comprehensive Project Specification for Claude Code

**Project:** WordPress 7.0.2 Website Rebuild  
**Client:** Town of Drakes Branch, Virginia (Population ~350)  
**Budget Feel:** $10,000+ professional website  
**Delivery Date:** September 13, 2026 (or earlier)  
**Status:** Ready for Claude Code CLI Implementation  

---

## PROJECT OVERVIEW

### Goal
Build a new WordPress website that:
- ✅ Migrates all current content from towndrakesbranch.com
- ✅ Meets WCAG 2.1 Level AA accessibility standards
- ✅ Feels premium and professional ($10k+ quality)
- ✅ Is fully responsive and mobile-first
- ✅ Integrates forms with N8N workflows and Google Sheets
- ✅ Hosts meeting videos and minutes
- ✅ Uses AI-generated media (via Higgsfield) for high-end feel
- ✅ Maintains existing payment integration (RDA)

### Scope
- 9 migrated pages (Home, About, Services, Town Officials, Links, Contact, Pay My Bill, Meeting Minutes, Events)
- 5 new pages (Permits, Complaints, FOIA, Job Applications, Video Archive)
- 5 new forms (Contact, Permit, Complaint, FOIA, Job Application)
- Accessible, modern WordPress theme
- Professional media (generated via Higgsfield)
- Mobile-first, fully responsive design

### Tools & Resources Available
- **WordPress:** 7.0.2 on BlueHost
- **Theme:** Modern, accessible (to be selected)
- **Media Generation:** Higgsfield (for hero images, graphics, icon illustrations)
- **Form Automation:** N8N workflows + Google Sheets
- **Video Hosting:** YouTube (primary) or Bunny.net (optional)
- **Version Control:** GitHub (Drakes Branch account)
- **Data Backup:** Google Sheets + GitHub

---

## TECHNICAL REQUIREMENTS

### WordPress Configuration
- **Version:** 7.0.2
- **Hosting:** BlueHost (Unlimited Websites)
- **Database:** MySQL 5.7.23+
- **PHP:** 8.1.34+
- **URL:** staging.towndrakesbranch.com (during development)
- **Final URL:** towndrakesbranch.com (after approval)

### Theme Selection & Setup
**Requirements:**
- ✅ WCAG 2.1 AA compliant (built-in)
- ✅ Mobile-first responsive design
- ✅ Modern, professional appearance
- ✅ Accessible color contrast (4.5:1 minimum body text)
- ✅ Semantic HTML (proper heading hierarchy h1→h2→h3)
- ✅ Keyboard navigation support
- ✅ Screen reader compatible
- ✅ Touch-friendly (44px minimum touch targets)
- ✅ Fast loading (optimize images, lazy loading)

**Recommended Themes:**
- GeneratePress (highly accessible, professional)
- Neve (performance-focused, accessible)
- Kadence (design-forward, good accessibility)
- Astra (flexible, accessible)

**Theme Customization:**
- [ ] Customize header/logo area
- [ ] Set up primary navigation menu
- [ ] Configure footer with links
- [ ] Apply custom color scheme (professional palette)
- [ ] Set up sidebar widgets (if needed)
- [ ] Configure font choices (readable, professional)
- [ ] Add custom CSS for branded styling
- [ ] Test all pages at multiple breakpoints (mobile, tablet, desktop)

### Plugins to Install
**Essential:**
- WPForms Lite (or keep if already installed) — for form building
- Contact Form 7 (alternative if needed)
- Yoast SEO or AIOSEO (already installed)

**Recommended:**
- Wordfence (security)
- Akismet (spam protection)
- WP Rocket (performance optimization)
- Elementor (if page builder preferred)

**Do NOT install:**
- Heavy carousels/sliders (accessibility issues)
- Auto-play media plugins
- Unnecessary bloat

---

## CONTENT MIGRATION REQUIREMENTS

### Pages to Migrate
All pages from current site must be migrated to new WordPress with improved accessibility.

| Page | Current Status | Action | New Features |
|------|---|---|---|
| **Home** | Active | Migrate & redesign | Remove carousel; add hero image (Higgsfield); meeting info widget; upcoming events |
| **About Us** | Active | Migrate as-is | Keep content; improve formatting/accessibility |
| **Town Officials** | Active | Migrate & enhance | Add photos (Higgsfield); improve layout |
| **Services** | Active | Migrate as-is | Link to permit forms; improve navigation |
| **Links** | Active | Migrate as-is | Update any broken links |
| **Contact** | Active | Migrate & enhance | Replace old form with N8N workflow; add accessibility features |
| **Pay My Bill** | Active | Migrate & test | Verify RDA link works; add fallback payment methods |
| **Meeting Minutes** | Active | Migrate & enhance | Link to GitHub-hosted PDFs; embed videos; create archive |
| **Events/Announcements** | Active | Migrate & refresh | Remove outdated dates; add upcoming events |

### New Pages to Create

#### 1. Meeting Agenda
- **Purpose:** Display upcoming meeting agendas
- **Content:** Pulled from GitHub or WordPress post type
- **Layout:** Simple, readable table format
- **Accessibility:** Semantic table structure; proper headers
- **Mobile:** Responsive table or list view on mobile

#### 2. Online Ordinance
- **Purpose:** Searchable town ordinances
- **Content:** Linked to custom ChatGPT tool
- **Layout:** Index of ordinances with search
- **Accessibility:** Semantic list; proper links
- **Integration:** ChatGPT API link for AI-powered search

#### 3. Forms Hub
- **Purpose:** Central page linking to all forms
- **Content:** Brief descriptions of each form
- **Layout:** Card grid (3 columns desktop, 1 mobile)
- **Links:** Each card links to individual form page
- **Accessibility:** Proper heading hierarchy; semantic structure

#### 4. Permit Request
- **Purpose:** Online permit applications
- **Content:** HTML form + N8N integration
- **Fields:** Name, address, permit type, description, attachment
- **Submission:** N8N → Google Sheets + Email
- **Accessibility:** Explicit labels; error messages; required field indicators

#### 5. Complaint/Feedback
- **Purpose:** Public feedback mechanism
- **Content:** HTML form + N8N integration
- **Fields:** Name, email, category, description
- **Submission:** N8N → Google Sheets + Email
- **Accessibility:** Explicit labels; clear instructions

#### 6. FOIA Request
- **Purpose:** Freedom of Information requests
- **Content:** HTML form + N8N integration
- **Fields:** Requester name, request description, format, timeline
- **Submission:** N8N → Google Sheets + Email
- **Accessibility:** Proper form structure; required field labels

#### 7. Job Applications
- **Purpose:** Employment applications
- **Content:** HTML form + N8N integration
- **Fields:** Name, contact, position, resume upload, cover letter
- **Submission:** N8N → Google Sheets + Email + File storage
- **Accessibility:** Accessible file upload; clear instructions

#### 8. Video Archive
- **Purpose:** Meeting recordings repository
- **Content:** Embedded YouTube videos
- **Layout:** Grid of video thumbnails (3 columns desktop, 1 mobile)
- **Metadata:** Meeting date, topic, description
- **Accessibility:** Video captions; descriptive titles; transcript links

#### 9. Accessibility Statement
- **Purpose:** Communicate accessibility commitment
- **Content:** Town's accessibility statement; how to report issues
- **Links:** Alternative format request process
- **Accessibility:** Fully accessible; text-based (no images for content)

---

## DESIGN REQUIREMENTS ($10,000+ Professional Feel)

### Visual Design
- ✅ **Hero Images:** Use Higgsfield to generate professional photos of Drakes Branch landmarks, town scenes, community events
- ✅ **Color Palette:** Professional, government-appropriate (blues, grays, greens)
- ✅ **Typography:** Clean, readable fonts (e.g., Inter, Open Sans, Lato)
- ✅ **Icons:** Professional icon library (Font Awesome, Feather Icons, or generated via Higgsfield)
- ✅ **Spacing:** Generous white space; professional padding/margins
- ✅ **Shadows & Depth:** Subtle shadows for layered feel (not excessive)
- ✅ **Consistency:** Uniform styling across all pages

### Homepage Design (Priority 1)
**Hero Section:**
- [ ] Large hero image (Higgsfield: professional town scene or landscape)
- [ ] Hero text overlay: "Welcome to Drakes Branch, Virginia"
- [ ] Tagline: "Strong Community, Strong Future" (or similar)
- [ ] CTA button: "Learn More" or "Explore Services"
- [ ] Fully responsive; text readable on mobile

**Quick Links Section:**
- [ ] 4-6 prominent cards with icons (Higgsfield-generated icons)
- [ ] Cards: Services, Permits, Pay My Bill, Meetings, News, Contact
- [ ] Card hover effects (subtle animation)
- [ ] Responsive: 2 columns mobile, 3 columns tablet, 6 columns desktop

**Upcoming Meetings Widget:**
- [ ] Simple, readable meeting list
- [ ] Next 3 meetings displayed
- [ ] Date, time, location, link to full agenda
- [ ] "View All Meetings" link

**Recent News/Announcements:**
- [ ] Latest 3 posts/announcements
- [ ] Clean card layout
- [ ] Excerpt + "Read More" link
- [ ] Responsive grid layout

**About Drakes Branch (Brief):**
- [ ] Short welcome text (100-150 words)
- [ ] 2-3 Higgsfield-generated images of town
- [ ] Link to full "About Us" page

**Footer:**
- [ ] Contact info (address, phone, email)
- [ ] Quick links (Services, Contact, Accessibility)
- [ ] Social links (Facebook if applicable)
- [ ] Copyright notice
- [ ] "Built with accessibility in mind" badge

### Mobile-First Design Strategy
**CRITICAL:** Design is mobile-first, not "desktop shrunk to mobile"

**Mobile Requirements:**
- [ ] Single-column layouts on mobile (except grids)
- [ ] Touch targets minimum 44×44 CSS pixels
- [ ] Readable text size: 16px minimum body text on mobile
- [ ] Proper line spacing (1.5 minimum)
- [ ] Hamburger menu for navigation (with proper focus states)
- [ ] Form fields full width on mobile
- [ ] Images scale properly; no horizontal scrolling
- [ ] Tap-friendly spacing between interactive elements
- [ ] Modal dialogs/overlays properly sized for mobile

**Tablet Design (768px - 1024px):**
- [ ] 2-column layouts where appropriate
- [ ] Increased spacing compared to mobile
- [ ] Readable text size maintained

**Desktop Design (1024px+):**
- [ ] Full multi-column layouts
- [ ] Professional spacing and hierarchy
- [ ] Side-by-side content where appropriate

**Testing Checkpoints:**
- [ ] iPhone SE (375px width)
- [ ] iPhone 12/13 (390px width)
- [ ] iPad (768px width)
- [ ] Desktop (1920px width)
- [ ] Test at 200% zoom on all breakpoints
- [ ] Test with horizontal/vertical orientation on mobile

---

## MEDIA & BRANDING (Higgsfield Integration)

### Media to Generate via Higgsfield
Use Higgsfield to create professional, branded media for high-end feel.

#### Hero Images (Homepage & Key Pages)
- [ ] Professional town skyline/landscape (Homepage hero)
- [ ] Town hall/government building (About Us page)
- [ ] Community gathering scene (Services page)
- [ ] Meeting room/council chamber (Meeting Minutes page)
- [ ] Job opportunity graphic (Job Applications page)

**Specifications:**
- Resolution: 1920×1080px (desktop), 1080×720px (mobile)
- Format: WebP (optimized) + JPEG (fallback)
- Style: Professional, slightly inviting (not corporate sterile)
- Prompt Example: "Professional photo of small Virginia town main street, daytime, welcoming atmosphere, government building visible, USA flag"

#### Icons
- [ ] Services icon (buildings/tools)
- [ ] Permits icon (clipboard/document)
- [ ] Meetings icon (calendar/people)
- [ ] Forms icon (document/pencil)
- [ ] Payment icon (wallet/dollar)
- [ ] Contact icon (envelope/phone)
- [ ] Video icon (play button/camera)
- [ ] Accessibility icon (wheelchair/accessible symbol)

**Specifications:**
- Format: SVG (scalable) or PNG 256×256px
- Style: Flat, modern, consistent
- Color: Match brand palette (blues, grays, greens)

#### Graphics & Illustrations
- [ ] Divider graphics (horizontal lines, subtle patterns)
- [ ] Background patterns (subtle, not distracting)
- [ ] Callout graphics for key messages
- [ ] Section dividers (Higgsfield-generated geometric shapes)

**Specifications:**
- Format: SVG (preferred) for scalability
- Style: Professional, minimal
- Color: 2-3 colors max (brand palette)

### Brand Guidelines (AI-Generated Media)
**Tone:** Professional, welcoming, accessible  
**Color Palette:**
- Primary: Professional blue (#003366 or similar)
- Secondary: Civic green (#2d7a3e or similar)
- Accent: Warm gray (#999999 or similar)
- Background: Light gray (#f5f5f5 or similar)

**Photography Style:**
- Realistic, natural lighting
- People-inclusive (show community)
- Professional but not sterile
- Day-time preferred (outdoor scenes)

**Typography:**
- Headings: Sans-serif, bold (e.g., Inter Bold)
- Body: Sans-serif, regular (e.g., Inter Regular)
- Sizes: H1=32px, H2=24px, H3=20px, Body=16px (mobile scaled appropriately)

---

## ACCESSIBILITY REQUIREMENTS (WCAG 2.1 Level AA)

### Mandatory Compliance
Every page and form must meet these standards. This is non-negotiable.

#### 1. Perceivable
- [ ] **1.1.1 Non-text Content:** All images have meaningful alt text
  - Form labels visible
  - Decorative images have empty alt=""
  - Actionable images describe the action
  
- [ ] **1.3.1 Info & Relationships:** Content structure is semantic
  - Proper heading hierarchy (h1 → h2 → h3, no skipping)
  - Form labels associated with inputs (<label for="id">)
  - Lists use <ul>, <ol>, <li>
  - Tables use <thead>, <tbody>, <th> properly
  
- [ ] **1.4.3 Color Contrast:** Minimum 4.5:1 for normal text, 3:1 for large text
  - Test all text with WebAIM Contrast Checker
  - Document contrast ratios in spreadsheet
  
- [ ] **1.4.11 Non-text Contrast:** UI components have 3:1 contrast
  - Buttons, form borders, icons, graphics

#### 2. Operable
- [ ] **2.1.1 Keyboard:** All functionality available via keyboard
  - Tab through entire site; verify logical order
  - All buttons, links, form inputs keyboard-accessible
  - No keyboard traps (focus can move away)
  
- [ ] **2.4.3 Focus Order:** Logical, predictable tab order
  - Header nav → Main content → Sidebar (if any) → Footer
  - Forms: top to bottom
  - No jumps or strange order
  
- [ ] **2.4.7 Focus Visible:** Visible focus indicator on all interactive elements
  - Default browser outline is acceptable
  - Or custom outline (minimum 2px, high contrast)
  - Test with Tab key
  
- [ ] **2.5.5 Target Size:** All touch targets minimum 44×44 CSS pixels
  - Buttons, links, form inputs
  - Test on mobile viewport

#### 3. Understandable
- [ ] **3.2.1 On Focus:** No unexpected changes when element gets focus
  - Don't auto-submit forms on focus
  - Don't open modals on focus
  - Don't change page context
  
- [ ] **3.3.1 Error Identification:** Errors clearly labeled
  - "This field is required"
  - "Please enter a valid email"
  - Color + text (not just color)
  
- [ ] **3.3.2 Labels & Instructions:** Form inputs have explicit labels
  - <label for="form-id"> tags
  - Visible on page (not placeholder text only)
  - Required fields marked

#### 4. Robust
- [ ] **4.1.2 Name, Role, Value:** All UI components properly announced
  - Buttons have accessible names (text or aria-label)
  - Form inputs have labels
  - Icons have aria-labels if actionable
  - Complex widgets have ARIA roles

### Testing Checkpoints
- [ ] **Automated:** Run axe-core on all pages; 0 errors
- [ ] **Keyboard:** Tab through entire site; all interactive elements accessible
- [ ] **Screen Reader:** Test with VoiceOver (Mac) or NVDA (Windows); verify announcements
- [ ] **Mobile:** Test touch targets; swipe navigation works
- [ ] **Zoom:** Test at 200% zoom; layout doesn't break

---

## FORM & INTEGRATION REQUIREMENTS

### Form Architecture
**All forms follow this pattern:**

```
HTML Form (on WordPress page)
    ↓
Form Submission via JavaScript/Fetch API
    ↓
N8N Webhook (receives form data)
    ↓
N8N Workflow Processes Data:
    ├→ Add to Google Sheet
    └→ Send Email to Town Clerk
    ↓
User Confirmation Message
```

### Forms to Build

#### 1. Contact Form
- **Location:** /contact or Contact page
- **Fields:**
  - Name (text, required)
  - Email (email, required)
  - Subject (text, required)
  - Message (textarea, required)
  - Honeypot (anti-spam)
- **N8N Workflow:** contact-form-webhook
- **Google Sheet:** "Inquiries"
- **Email to:** town clerk email
- **Confirmation:** "Thank you! We'll respond within 2 business days."

#### 2. Permit Request Form
- **Location:** /permits/request or Permits page
- **Fields:**
  - Full Name (text, required)
  - Address (text, required)
  - Permit Type (dropdown: Building, Utility, Other, required)
  - Description (textarea, required)
  - Attachment (file upload, optional)
  - Honeypot (anti-spam)
- **N8N Workflow:** permit-request-webhook
- **Google Sheet:** "Permits"
- **Email to:** town clerk email + attachment link
- **Confirmation:** "Permit request submitted. Reference #: [auto-generated]"

#### 3. Complaint/Feedback Form
- **Location:** /feedback or Complaints page
- **Fields:**
  - Name (text, required)
  - Email (email, required)
  - Category (dropdown: Complaint, Suggestion, Other, required)
  - Description (textarea, required)
  - Honeypot (anti-spam)
- **N8N Workflow:** feedback-form-webhook
- **Google Sheet:** "Feedback"
- **Email to:** town clerk email
- **Confirmation:** "Thank you for your feedback!"

#### 4. FOIA Request Form
- **Location:** /foia or Records page
- **Fields:**
  - Requester Name (text, required)
  - Email (email, required)
  - Request Description (textarea, required)
  - Preferred Format (radio: Digital, Paper, CD, required)
  - Timeline Needed (radio: Standard 5-10 days, Expedited, required)
  - Honeypot (anti-spam)
- **N8N Workflow:** foia-request-webhook
- **Google Sheet:** "FOIA_Requests"
- **Email to:** town clerk email + compliance template
- **Confirmation:** "FOIA request received. Request #: [auto-generated]"

#### 5. Job Application Form
- **Location:** /jobs or Employment page
- **Fields:**
  - Full Name (text, required)
  - Email (email, required)
  - Phone (tel, required)
  - Position Applied (dropdown: [list positions], required)
  - Resume/CV (file upload, .pdf/.docx, required)
  - Cover Letter (textarea, required)
  - Honeypot (anti-spam)
- **N8N Workflow:** job-application-webhook
- **Google Sheet:** "JobApplications"
- **Email to:** town clerk email + file links
- **Confirmation:** "Application submitted! We'll review and contact you within 5-7 days."

### Form Accessibility
ALL forms must meet these requirements:

- [ ] Explicit labels: `<label for="field-id">Label</label>`
- [ ] Required indicators: red asterisk + aria-required="true"
- [ ] Error messages: appear on blur; clearly describe error
- [ ] Focus management: focus stays in form; logical tab order
- [ ] Keyboard navigation: Tab through all fields; Enter submits
- [ ] Touch targets: 44×44px minimum on mobile
- [ ] Clear instructions: placeholder is NOT a label
- [ ] Success message: displayed after successful submission
- [ ] Validation: client-side for UX; server-side for security (N8N)

### N8N Workflows
**Each workflow:**
1. Receives webhook data from form submission
2. Validates data
3. Adds timestamp
4. Appends to Google Sheet
5. Sends confirmation email to town clerk

**Workflow names:**
- contact-form-webhook
- permit-request-webhook
- feedback-form-webhook
- foia-request-webhook
- job-application-webhook

---

## CONTENT REQUIREMENTS

### Homepage Copy
- **Hero Headline:** Welcoming, clear (e.g., "Drakes Branch Government Portal")
- **Hero Subheading:** Community-focused tagline
- **Call-to-Action Buttons:** Clear, action-oriented text
- **Section Headings:** Descriptive, scannable
- **Body Copy:** Concise, readable (short paragraphs)

### Tone & Voice
- Professional but approachable
- Clear, jargon-free language
- Active voice preferred
- Helpful, service-oriented
- Inclusive language

### Content Structure
- H1 per page (only one main heading)
- H2 for major sections
- H3 for subsections
- Short paragraphs (2-3 sentences max)
- Bulleted lists for procedures
- Strong visual hierarchy

---

## PAYMENT INTEGRATION

### RDA Bill Pay Link
- **Current Status:** Verify existing link works
- **Requirement:** Maintain same functionality as current site
- **Testing Checkpoint:** Click link; verify payment portal loads
- **Fallback:** If link breaks, provide alternative payment methods:
  - Mail payment (with remittance slip)
  - In-person payment (address provided)
  - Phone payment (number provided)
  - Document all methods on "Pay My Bill" page

---

## VIDEO HOSTING

### YouTube Integration (Recommended)
- [ ] Create unlisted YouTube channel for town
- [ ] Upload meeting recordings to YouTube
- [ ] Add captions to all videos (for accessibility)
- [ ] Embed videos on WordPress pages using iframe
- [ ] Link to video archive from meeting minutes

**Embed Code Example:**
```html
<iframe width="560" height="315" src="https://www.youtube.com/embed/VIDEO_ID" 
  title="Meeting Title" frameborder="0" allow="accelerometer; autoplay; clipboard-write; 
  encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
```

### Optional: Bunny.net Migration (Future)
- If YouTube becomes limiting, migrate to Bunny.net CDN
- Similar embed approach; faster delivery
- More control over content

---

## GITHUB INTEGRATION

### Repository Structure
```
drakesbranch-town-website/
├── README.md
├── forms/
│   ├── contact-form.html
│   ├── permit-request.html
│   ├── complaint-form.html
│   ├── foia-request.html
│   └── job-application.html
├── meeting-minutes/
│   ├── 2024/
│   ├── 2025/
│   └── 2026/
├── templates/
│   ├── meeting-agenda-template.md
│   └── meeting-minutes-template.md
├── docs/
│   ├── n8n-workflows.md
│   ├── form-submission-guide.md
│   └── maintenance-procedures.md
└── assets/
    └── (images, icons for documentation)
```

### Use Cases
- Store HTML form files
- Version control meeting minutes PDFs
- Document N8N workflow configurations
- Backup/reference for website components
- Public accessibility (transparency for town)

---

## IMPLEMENTATION CHECKPOINTS

### Phase 1: Setup & Planning (Week 1)
- [ ] WordPress staging site created on BlueHost
- [ ] Theme selected & installed
- [ ] Plugins installed & configured
- [ ] GitHub repository created & structured
- [ ] Higgsfield media generation plan finalized

### Phase 2: Design & Media (Week 2)
- [ ] Hero images generated via Higgsfield (5-7 images)
- [ ] Icons created via Higgsfield (8-10 icons)
- [ ] Color palette finalized
- [ ] Typography selected & configured
- [ ] Homepage design completed
- [ ] All pages layout sketched/approved

### Phase 3: Content Migration (Week 2-3)
- [ ] All 9 current pages migrated
- [ ] Content edited for readability/accessibility
- [ ] Images added with proper alt text
- [ ] Links verified (internal & external)
- [ ] PDF documents uploaded & linked

### Phase 4: New Pages & Forms (Week 3-4)
- [ ] 9 new pages created (Permits, Complaints, FOIA, Jobs, Video Archive, etc.)
- [ ] 5 HTML forms built with accessibility features
- [ ] N8N webhooks configured & tested
- [ ] Google Sheets set up for each form
- [ ] Form submission testing completed

### Phase 5: Accessibility Audit (Week 4)
- [ ] Automated accessibility scan (axe-core)
- [ ] Keyboard navigation testing (Tab through all pages)
- [ ] Screen reader testing (VoiceOver/NVDA)
- [ ] Color contrast verification (all text)
- [ ] Mobile touch target verification
- [ ] 200% zoom testing
- [ ] Generate accessibility report

### Phase 6: Video Integration (Week 4-5)
- [ ] YouTube channel set up
- [ ] Sample video uploaded & embedded
- [ ] Video archive page created
- [ ] Video captions verified
- [ ] Embed codes documented

### Phase 7: Integration Testing (Week 5)
- [ ] Form submissions tested end-to-end
- [ ] Google Sheets integration verified
- [ ] Email notifications working
- [ ] GitHub links verified
- [ ] RDA payment link tested
- [ ] All external links working
- [ ] Mobile responsiveness tested (all breakpoints)

### Phase 8: Performance & Security (Week 5-6)
- [ ] Image optimization (WebP format, proper sizing)
- [ ] Lazy loading implemented
- [ ] Caching configured
- [ ] Security: SSL certificate verified
- [ ] Performance: Google PageSpeed Insights 90+
- [ ] Mobile performance: Lighthouse score 90+

### Phase 9: Final QA & Documentation (Week 6)
- [ ] All checkpoints verified
- [ ] Accessibility compliance document
- [ ] Content audit checklist
- [ ] Documentation for town clerk (maintenance guide)
- [ ] N8N workflow documentation
- [ ] GitHub repository documentation
- [ ] Video hosting guide
- [ ] Deployment instructions prepared

### Phase 10: Staging Review & Approval (Week 6-7)
- [ ] Website uploaded to staging.towndrakesbranch.com
- [ ] All functionality tested on staging
- [ ] Town Council review & approval
- [ ] Feedback incorporated
- [ ] Final sign-off obtained

### Phase 11: Deployment to Production (Week 7)
- [ ] Final backup of current site
- [ ] DNS/domain switch to new WordPress
- [ ] 301 redirects set up (if URL structure changed)
- [ ] Monitor for errors
- [ ] Production accessibility audit
- [ ] Deployment verification

---

## QUALITY STANDARDS

### Performance
- [ ] Page load time: <3 seconds on 4G
- [ ] Lighthouse score: 90+
- [ ] Core Web Vitals: All green
- [ ] Mobile performance: 90+ score

### Accessibility
- [ ] WCAG 2.1 AA compliance: 100%
- [ ] Zero axe-core errors
- [ ] Keyboard navigation: 100%
- [ ] Screen reader: All content announced correctly
- [ ] Color contrast: 4.5:1 minimum

### Design
- [ ] Professional appearance ($10k+ feel)
- [ ] Consistent branding (colors, typography)
- [ ] Responsive at all breakpoints
- [ ] Mobile-first (not shrunk)
- [ ] Touch-friendly
- [ ] High-quality media (Higgsfield)

### Functionality
- [ ] All forms working
- [ ] All links working
- [ ] All integrations working (N8N, GitHub, RDA)
- [ ] Video embeds working
- [ ] All plugins functioning
- [ ] Database properly configured

### Security
- [ ] SSL certificate active
- [ ] Wordfence security scan: clean
- [ ] Form security: CSRF tokens, validation
- [ ] No hardcoded credentials
- [ ] Backup strategy documented

---

## DELIVERABLES

### Website
- [ ] Live WordPress site on staging.towndrakesbranch.com
- [ ] All pages, forms, and integrations working
- [ ] Fully accessible and responsive
- [ ] Professional design with Higgsfield media
- [ ] Performance optimized

### Documentation
- [ ] Maintenance guide for Town Clerk
- [ ] Form submission guide
- [ ] N8N workflow documentation
- [ ] GitHub repository guide
- [ ] Accessibility compliance report
- [ ] Deployment & launch instructions

### Training
- [ ] Town Clerk trained on WordPress basics
- [ ] How to update pages
- [ ] How to add meeting minutes
- [ ] How to manage forms
- [ ] How to monitor form submissions

---

## FINAL REQUIREMENTS SUMMARY

✅ **Migrate all content** from current site  
✅ **Professional design** ($10k+ feel, Higgsfield media)  
✅ **Mobile-first** (responsive, touch-friendly)  
✅ **WCAG 2.1 AA** (fully accessible)  
✅ **Forms with N8N** (Permits, Complaints, FOIA, Jobs, Contact)  
✅ **Video hosting** (YouTube or Bunny.net)  
✅ **GitHub integration** (forms, meeting minutes)  
✅ **Payment link** (RDA maintained)  
✅ **Performance optimized** (fast, 90+ scores)  
✅ **Documented** (maintenance guide for Town Clerk)  
✅ **Production-ready** by September 13, 2026  

---

## NOTES FOR CLAUDE CODE

- This is a government website for a small town (350 people)
- Quality is critical; aim for $10,000+ professional feel
- Accessibility is non-negotiable (WCAG 2.1 AA required)
- Mobile-first means design FOR mobile, not shrink desktop design
- Use Higgsfield to generate professional media (hero images, icons, graphics)
- N8N workflows automate form submissions to Google Sheets + email
- Town Clerk (Tammy) will maintain this after launch
- Keep documentation clear for non-technical future staff
- Test thoroughly; each checkpoint is a quality gate

---

**Project Status:** Ready for Claude Code CLI Implementation  
**Start Date:** August 1, 2026  
**Target Completion:** September 13, 2026  
**Questions?** Contact: tammyamosley2018@gmail.com
