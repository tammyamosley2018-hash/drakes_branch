# Content Migration & Architecture Plan
## Town of Drakes Branch Website Rebuild
**Date:** August 1, 2026  
**Prepared For:** Town Council Approval (August 3, 2026 Meeting)  
**Status:** Ready for Phase 2 Implementation

---

## Executive Summary

The new Town of Drakes Branch website will migrate all current content while implementing modern, accessible infrastructure. This plan outlines:

1. **Content Inventory** — what transfers from current site
2. **New Infrastructure** — forms, video hosting, workflows, GitHub integration
3. **Architecture Diagram** — how components connect
4. **Implementation Timeline** — week-by-week phases
5. **Technical Stack** — WordPress + N8N + Google Sheets + GitHub

---

## Part 1: Content Inventory

### Pages to Migrate (All Current Pages)
| Page | Status | Notes |
|------|--------|-------|
| Home | Migrate | Remove/replace inaccessible carousel; update meeting info |
| About Us | Migrate | Keep current content |
| Town Officials | Migrate | Keep as is; can be updated anytime |
| Services | Migrate | Keep current content; link to online ordinance |
| Links | Migrate | Keep all external links |
| Contact | Migrate → Transform | Keep page; redirect form to N8N workflow |
| Pay My Bill | Migrate | Preserve RDA payment link; test compatibility |
| Meeting Minutes | Migrate + Archive | Link to GitHub-hosted PDFs; embed meeting videos |
| Events/Announcements | Migrate | Keep current content; refresh outdated dates |

### New Pages to Create
| Page | Purpose | Content Source |
|------|---------|-----------------|
| Meeting Agenda | Published before each meeting | Town Clerk creates; stored in GitHub |
| Online Ordinance | Searchable town ordinances | ChatGPT-powered custom link |
| Forms & Applications | Centralized form hub | Links to individual N8N forms |
| Permit Request | Online permit applications | N8N workflow → Google Sheets |
| Complaint Form | Public complaints/feedback | N8N workflow → Google Sheets |
| FOIA Request | Freedom of Info requests | N8N workflow → Google Sheets |
| Job Applications | Employment opportunities | HTML form → Google Sheets |
| Video Archive | Meeting recordings | Embedded YouTube or Bunny.net |
| Accessibility | Accessibility statement & contact | Town policy; how to request alt formats |

### Content Not Migrating
| Item | Reason | Alternative |
|------|--------|-------------|
| Outdated meeting dates (May/June 2021) | Obsolete | Will be updated with current meetings |
| Broken calendar embed | Not accessible | Replace with HTML meeting list |
| Inaccessible image carousel | Not WCAG-compliant | Replace with static image gallery |

---

## Part 2: Documents & Assets

### PDFs to Archive
**Source:** Current website public_html folder  
**Action:** Keep all existing PDFs  
**Storage:** BlueHost + GitHub (for version control)

| Category | Count | Action |
|----------|-------|--------|
| Meeting Minutes | ~50+ | Link from "Meeting Minutes" page; host PDFs in GitHub repo |
| Budgets | ~5 | Keep current; update as needed |
| Ordinances | ~10+ | Keep current; also create searchable HTML index with ChatGPT link |
| Forms | ~8 | Keep as downloadable templates; also offer HTML versions |
| Other documents | ~15 | Archive; organize by year |

### Video Hosting Decision
**Options:**
1. **YouTube** — Free, unlimited storage, good accessibility, integrates easily with WordPress
2. **Bunny.net** — Cost-effective CDN, good for long-term storage, higher control

**Recommendation:** Start with YouTube (free); migrate to Bunny.net later if needed.

**Implementation:**
- Upload meeting recordings to YouTube (unlisted if sensitive)
- Embed videos on "Video Archive" page
- Link to each meeting in meeting minutes
- Add captions for accessibility

---

## Part 3: Forms & Data Flow

### Form Architecture
**Pattern:** HTML Form → N8N Workflow → Google Sheets + Email Notification

```
┌─────────────────────────────────────────────────────────┐
│                    WordPress Site                        │
│  (Homepage, About, Services, Pay My Bill, etc.)         │
└─────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────┐
│                   Forms Hub Page                         │
│  (Links to all N8N-powered forms)                        │
└─────────────────────────────────────────────────────────┘
    ↙        ↙          ↙          ↙         ↙
┌──────┐ ┌──────┐  ┌──────┐  ┌──────┐  ┌──────┐
│Contact│ │Permit│  │Complaint
│Form   │ │Form  │  │Form   │  │FOIA  │  │Job App│
└──────┘ └──────┘  └──────┘  └──────┘  └──────┘
    ↓        ↓          ↓          ↓         ↓
    └────────┴──────────┴──────────┴────────┘
                    ↓
         ┌──────────────────────┐
         │  N8N Workflows       │
         │  (Process & Route)   │
         └──────────────────────┘
              ↙              ↖
        ┌─────────┐      ┌──────────────┐
        │Google   │      │Email         │
        │Sheets   │      │Notification  │
        │(Data)   │      │(Town Clerk)  │
        └─────────┘      └──────────────┘
```

### Forms to Build

#### 1. Contact Form (Existing → Enhanced)
- **Purpose:** General inquiries
- **Fields:** Name, Email, Subject, Message
- **Workflow:** N8N catches submission → sends email to town clerk + stores in Google Sheets
- **HTML File:** `contact-form.html` (GitHub repo)

#### 2. Permit Request Form (New)
- **Purpose:** Online permit applications
- **Fields:** Applicant name, address, permit type, description, attachment upload
- **Workflow:** N8N → Google Sheets → Email town clerk
- **HTML File:** `permit-request.html` (GitHub repo)

#### 3. Complaint/Feedback Form (New)
- **Purpose:** Public complaints, suggestions, feedback
- **Fields:** Name, Email, Category (Complaint/Suggestion/Other), Description
- **Workflow:** N8N → Google Sheets → Email town clerk
- **HTML File:** `complaint-form.html` (GitHub repo)

#### 4. FOIA Request Form (New)
- **Purpose:** Freedom of Information Act requests
- **Fields:** Requester name, request description, preferred format, deadline needed
- **Workflow:** N8N → Google Sheets → Email town clerk
- **HTML File:** `foia-request.html` (GitHub repo)

#### 5. Job Application Form (New)
- **Purpose:** Employment applications
- **Fields:** Full name, contact info, position applied for, resume/CV upload, cover letter
- **Workflow:** N8N → Google Sheets → Email town clerk
- **HTML File:** `job-application.html` (GitHub repo)

### Google Sheets Destinations
Each form submits to a dedicated Google Sheet:

| Form | Google Sheet | Columns |
|------|--------------|---------|
| Contact | Inquiries | Timestamp, Name, Email, Subject, Message |
| Permit | Permits | Timestamp, Applicant, Address, Type, Description, File Link |
| Complaint | Feedback | Timestamp, Name, Email, Category, Description |
| FOIA | FOIA Requests | Timestamp, Requester, Request, Format, Deadline |
| Job | Applications | Timestamp, Name, Email, Position, Resume Link, Cover Letter |

---

## Part 4: GitHub Integration

### Repository Structure
**Location:** Drakes Branch GitHub account  
**Purpose:** Version control for meeting minutes, HTML forms, configuration

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
│       ├── 2026-01-06-council-meeting.pdf
│       ├── 2026-02-03-council-meeting.pdf
│       ├── 2026-03-03-council-meeting.pdf
│       ├── 2026-06-01-council-meeting.pdf (future)
│       └── 2026-08-03-council-meeting.pdf (upcoming)
├── templates/
│   ├── meeting-agenda-template.md
│   └── meeting-minutes-template.md
├── ordinances/
│   ├── ordinance-index.md
│   └── [individual ordinance files]
└── docs/
    ├── n8n-workflow-config.md
    └── form-submission-guide.md
```

### Workflow
1. **Before meeting:** Town Clerk creates agenda in GitHub
2. **After meeting:** Town Clerk uploads minutes PDF to GitHub
3. **Recording:** Town Clerk uploads video link to GitHub (or embeds in minutes)
4. **Website:** WordPress pulls links from GitHub; displays on site
5. **Archive:** All minutes automatically versioned in GitHub

---

## Part 5: Technical Architecture

### Stack Overview
```
┌─────────────────────────────────────────────┐
│        WordPress 7.0.2 (Front-end)          │
│  (Pages, Navigation, Content Display)       │
└──────────────────────┬──────────────────────┘
                       │
        ┌──────────────┼──────────────┐
        ↓              ↓              ↓
    ┌────────┐   ┌─────────┐   ┌──────────┐
    │GitHub  │   │N8N      │   │RDA Pay   │
    │(Forms, │   │(Form    │   │My Bill   │
    │Minutes)│   │Workflows)  │(Payment)│
    └────────┘   └─────────┘   └──────────┘
                       │
                       ↓
                ┌─────────────┐
                │Google       │
                │Sheets       │
                │(Data Store) │
                └─────────────┘
```

### Components

#### WordPress (BlueHost)
- **Role:** Main website, content management
- **Hosts:** All pages, navigation, images, embedded forms
- **Plugins:** Keep existing (WPForms for contact form if used; replace with N8N)
- **Theme:** Upgrade to accessible, modern theme (to be selected)

#### GitHub
- **Role:** Version control for forms, meeting minutes, documentation
- **Hosts:** HTML form files, meeting PDFs, ordinance files
- **Access:** Public (allows transparency); Tammy as admin
- **Branches:** main (production), dev (testing)

#### N8N
- **Role:** Workflow automation
- **Handles:** Form submissions → Google Sheets + Email notifications
- **Hosted:** Self-hosted on BlueHost (if available) or N8N Cloud
- **Workflows:** 1 per form type (5 workflows total)

#### Google Sheets
- **Role:** Form data storage & backup
- **Stores:** All form submissions organized by type
- **Access:** Town Clerk + Council (read-only link)
- **Backup:** Automatic daily backups

#### RDA Payment System
- **Role:** Bill payment processing
- **Status:** Keep existing integration
- **Test:** Verify compatibility after WordPress rebuild
- **Fallback:** If broken, provide alternative payment methods (mail, phone, in-person)

---

## Part 6: Implementation Timeline

### Phase 1: Planning & Approval (Week of Aug 1-3)
**By August 3, 2026 Council Meeting**
- [ ] Present audit report to Council
- [ ] Review content migration plan
- [ ] Decide on WordPress theme
- [ ] Decide on video hosting (YouTube vs Bunny.net)
- [ ] Get Council approval to proceed

### Phase 2: Infrastructure Setup (Week of Aug 5-9)
- [ ] Create new WordPress staging site on BlueHost subdomain
- [ ] Set up GitHub repository structure
- [ ] Create N8N workflows (test environment)
- [ ] Create Google Sheets for form data
- [ ] Test form submission flow end-to-end

### Phase 3: Content Migration (Week of Aug 12-16)
- [ ] Migrate all pages from old site to new WordPress
- [ ] Update images with proper alt text
- [ ] Create new pages (Permits, Complaints, FOIA, Job Apps, Video Archive)
- [ ] Upload all PDFs to GitHub
- [ ] Create meeting agenda template

### Phase 4: Accessibility Fixes (Week of Aug 19-23)
- [ ] Replace image carousel with accessible gallery
- [ ] Create HTML meeting list (replace calendar embed)
- [ ] Add accessibility statement page
- [ ] Fix heading hierarchy
- [ ] Add ARIA labels to interactive elements
- [ ] Verify color contrast

### Phase 5: Testing & Optimization (Week of Aug 26-30)
- [ ] Keyboard navigation audit
- [ ] Screen reader testing (VoiceOver/NVDA)
- [ ] Form testing (all 5 forms)
- [ ] Video embedding test
- [ ] RDA payment link test
- [ ] Mobile responsiveness test

### Phase 6: Council Review (Week of Sep 2-6)
- [ ] Upload new site to staging URL
- [ ] Present to Council for review
- [ ] Gather feedback
- [ ] Make revisions based on feedback

### Phase 7: Deployment & Training (Week of Sep 9-13)
- [ ] Final accessibility audit
- [ ] Deploy to production (towndrakesbranch.com)
- [ ] Archive old site as backup
- [ ] Train Town Clerk on WordPress maintenance
- [ ] Create maintenance documentation

**Total Timeline:** 6 weeks to production-ready site

---

## Part 7: Cost & Resource Analysis

### Hosting (Current)
- **BlueHost:** $251/quarter = **$1,004/year**
- **With new site:** Same hosting (unlimited sites included)
- **Cost change:** $0 (no additional hosting cost)

### Tools & Services
| Tool | Cost | Purpose |
|------|------|---------|
| WordPress | Free | CMS |
| BlueHost | $251/quarter | Hosting |
| GitHub | Free (public repo) | Version control |
| N8N | Free (self-hosted) or ~$30/month (cloud) | Workflow automation |
| Google Sheets | Free | Form data storage |
| YouTube | Free | Video hosting |
| Bunny.net | ~$0.01-0.03 per GB (optional) | CDN for videos |
| RDA | Existing | Payment processing |

**New costs:** Potentially $0-30/month for N8N (depending on hosting choice)

### Time Investment
- **Tammy (Town Clerk):** ~40 hours over 6 weeks
  - Content review & verification
  - Form testing
  - Meeting minute uploads (ongoing)
  - Staff training
  - Documentation

- **Claude (Assistant):** Included in this project

---

## Part 8: Payment Link Integration (RDA)

### Current Status
- **Provider:** RDA ("Pay My Bill" link)
- **Integration:** External link from website
- **Testing:** Will verify compatibility after WordPress rebuild

### Contingency Plan
If rebuild breaks RDA link:
1. **Option A:** Re-establish RDA link in new WordPress
2. **Option B:** Provide alternative payment methods:
   - Mail payment (with instructions)
   - In-person payment at Town Office
   - Phone payment (if supported by RDA)
3. **Communication:** Clearly display all payment options on "Pay My Bill" page

### Action Item
- [ ] Document RDA account login & configuration
- [ ] Test payment link immediately after WordPress migration
- [ ] Have backup payment instructions ready

---

## Part 9: Video Embedding

### YouTube Integration (Recommended for now)
- Free, unlimited storage
- Built-in accessibility (captions)
- Integrates with WordPress (embed via shortcode)
- Easy for public to find

**Implementation:**
```
1. Upload meeting recording to YouTube (unlisted channel)
2. Get embed code from YouTube
3. Add embed code to WordPress meeting page or minutes post
4. Link to video from "Video Archive" page
```

### Bunny.net Integration (Optional future)
- If switching to Bunny.net later:
  - Better CDN performance
  - Lower bandwidth costs
  - Still embeds in WordPress via iframe
  - Can migrate YouTube videos to Bunny later

---

## Part 10: Approval Checklist for Council

### Council Must Approve:
- [ ] Proceed with WordPress rebuild (vs static site)
- [ ] Migrate all current content
- [ ] Create new online forms (Permits, Complaints, FOIA, Jobs)
- [ ] Use N8N for form automation
- [ ] Use Google Sheets for data storage
- [ ] Use GitHub for meeting minutes & forms version control
- [ ] Use YouTube (or Bunny.net) for meeting videos
- [ ] Implement WCAG 2.1 AA accessibility standards
- [ ] Update "Pay My Bill" link integration
- [ ] Timeline: Production ready by September 13, 2026

### Questions for Council:
1. Who is authorized to approve new content types (forms, videos)?
2. Who has access to Google Sheets with form submissions?
3. What's the backup plan if payment link breaks?
4. Should old website be archived or deleted after migration?

---

## Next Steps

### For Town Clerk (Tammy)
1. ✅ Present audit report to Council (Monday, August 3)
2. ✅ Get Council approval for rebuild approach
3. **This week:** Start GitHub setup; create form templates
4. **Aug 5-9:** Build WordPress staging site with new theme

### For Claude (Assistant)
1. ✅ Complete audit and content plan
2. **This week:** Select & configure accessible WordPress theme
3. **Aug 5-9:** Set up staging WordPress site
4. **Aug 12:** Begin content migration
5. **Aug 19:** Implement accessibility fixes
6. **Aug 26:** Run full accessibility test suite

### For Council
1. **August 3 Meeting:** Review plan; vote to approve rebuild
2. **August 30 (Approx):** Review staging site
3. **September 3 (Approx):** Approve deployment to production

---

## Questions & Support

**Contact:** tammyamosley2018@gmail.com  
**GitHub:** (Drakes Branch account)  
**Timeline:** Ready to start August 5, 2026

---

**Document Version:** 1.0  
**Date Prepared:** August 1, 2026  
**Status:** Ready for Council Review
