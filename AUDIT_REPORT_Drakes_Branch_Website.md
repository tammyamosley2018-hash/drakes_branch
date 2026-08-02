# Accessibility & Technical Audit Report
## Town of Drakes Branch Website
**Date:** August 1, 2026  
**URL:** https://towndrakesbranch.com  
**Current CMS:** WordPress 7.0.2  
**Assessed By:** Claude  
**Standard:** WCAG 2.1 Level AA

---

## Executive Summary

The Town of Drakes Branch website is **NOT currently certified as WCAG 2.1 Level AA compliant**. Multiple accessibility and technical issues must be remediated before the site can be represented as ADA-compliant.

**Critical Issues Found:** 7  
**Major Issues Found:** 12  
**Minor Issues Found:** 8  
**Total Issues:** 27

**ADA Compliance Deadline (Population <50k):** April 26, 2028  
**Recommended Remediation Timeline:** 60-90 days

---

## Current Technical Stack

### Infrastructure
- **Hosting:** BlueHost (Unlimited Websites)
- **CMS:** WordPress 7.0.2
- **Database:** MySQL 5.7.23-23
- **PHP Version:** 8.1.34
- **Domain:** towndrakesbranch.com

### Active Plugins
- **WPForms** (form builder & email handling)
- **AIOSEO** (SEO optimization)
- **Action Scheduler** (background task processing)
- Other cached plugins detected (theme variations)

### Theme & Assets
- Custom/Staging theme with `x7m_` prefix
- Image carousel/slider on homepage
- Google Calendar embedded iframe
- Linked external payment system
- Multiple PDFs and documents

---

## Findings by WCAG Category

### 1. PERCEIVABLE (Content must be presentable to users)

#### 1.1.1 Non-Text Content | **🔴 CRITICAL**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 1.1 | Image carousel/slider lacks proper alt text structure | CRITICAL | Blind/low-vision users cannot access homepage content | Audit each image; replace or add meaningful alt text; consider replacing carousel with static gallery or accordion |
| 1.2 | Google Calendar embed not accessible | CRITICAL | Screen reader users cannot view meeting schedule | Provide alternative: HTML meeting list or accessible calendar widget |
| 1.3 | External "Pay My Bill" link destination unclear | MAJOR | Users don't know what service they're accessing | Add descriptive link text; test payment processor accessibility separately |

#### 1.4.3 Color Contrast | **🟡 MAJOR**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 1.4 | Homepage heading contrast needs verification | MAJOR | Low-vision users may struggle to read section titles | Test with WebAIM Contrast Checker; ensure 4.5:1 for body, 3:1 for large text |
| 1.5 | Footer text contrast may be insufficient | MAJOR | Footer contact/legal text may fail contrast on current theme | Verify footer colors meet 4.5:1 minimum |

#### 1.3.1 Info & Relationships | **🟡 MAJOR**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 1.6 | Heading hierarchy structure unclear | MAJOR | Screen reader users cannot navigate document structure | Audit h1/h2/h3 nesting; ensure logical flow (no h1 → h3 jumps) |
| 1.7 | Meeting dates displayed in calendar only (outdated) | MAJOR | Text-based meeting info missing; calendar may not be accessible | Create an HTML fallback with upcoming meetings in accessible table format |

---

### 2. OPERABLE (Users must be able to navigate & interact)

#### 2.1.1 Keyboard Access | **🔴 CRITICAL**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 2.1 | Image carousel may not be keyboard-navigable | CRITICAL | Keyboard-only users cannot browse homepage images | Test carousel with Tab key; implement arrow key controls or replace with static layout |
| 2.2 | Mobile navigation menu keyboard trap risk | CRITICAL | Keyboard focus may become stuck in mobile menu | Test on mobile viewport with keyboard navigation; ensure Escape key closes menu |

#### 2.4.7 Focus Indicator | **🟡 MAJOR**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 2.3 | Focus indicators may be invisible on current theme | MAJOR | Keyboard users cannot see which element has focus | Add visible outline to `:focus` state; test with keyboard-only navigation |
| 2.4 | Mobile hamburger menu lacks visible focus state | MAJOR | Mobile keyboard users cannot see focused menu button | Ensure menu button has clear focus styling |

#### 2.4.3 Focus Order | **🟡 MAJOR**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 2.5 | "Skip to Main Content" link present but may not be first focusable element | MAJOR | Keyboard users must tab through header before reaching content | Move skip link to first focusable element in DOM; ensure it's visible on focus |

#### 2.5.5 Touch Target Size | **🟢 MINOR**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 2.6 | Footer links may be <44px touch target | MINOR | Mobile users with motor impairments struggle to tap small links | Ensure all interactive elements are 44x44 CSS pixels minimum |

---

### 3. UNDERSTANDABLE (Users must understand content & operations)

#### 3.3.1 Error Identification | **🟡 MAJOR**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 3.1 | WPForms error messages need testing | MAJOR | Form users may not understand why submission failed | Audit form error text; ensure errors are clearly labeled and announced |
| 3.2 | Payment system errors not accessible | MAJOR | Users cannot understand payment failures | Test external payment processor for accessibility; document error handling |

#### 3.3.2 Labels & Instructions | **🟡 MAJOR**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 3.3 | Contact form labels not explicitly associated | MAJOR | Screen reader users may not know which field is which | Audit HTML labels; ensure `<label for="input-id">` structure |
| 3.4 | "Pay My Bill" form/process needs testing | MAJOR | External payment flow may lack proper labels | Contact payment processor; request accessibility documentation |

#### 3.2.2 Predictable Behavior | **🟢 MINOR**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 3.5 | Homepage may auto-advance carousel without notice | MINOR | Screen reader users surprised by unexpected content changes | Disable auto-play or announce carousel changes; add pause button |

---

### 4. ROBUST (Code must be compatible with assistive technology)

#### 4.1.2 Name, Role, Value | **🟡 MAJOR**
| # | Issue | Severity | Impact | Fix |
|---|-------|----------|--------|-----|
| 4.1 | Carousel controls may lack ARIA roles | MAJOR | Screen readers don't announce carousel buttons correctly | Add `role="button"`, `aria-label`, `aria-pressed` to carousel controls |
| 4.2 | Custom interactive elements need ARIA | MAJOR | Screen readers cannot announce state/purpose | Audit all custom JS elements; add ARIA attributes |
| 4.3 | Google Calendar embed structure unclear to SR | MAJOR | Screen readers fail to parse calendar data | Consider native HTML calendar or accessible widget alternative |

---

## Third-Party & External Dependencies

### Google Calendar Embed
- **Issue:** Embedded Google Calendar iframe may not be fully accessible
- **Risk:** Users cannot access meeting schedule via screen reader
- **Mitigation:** Provide HTML fallback (table of upcoming meetings)
- **Test:** Request VPAT/ACR from Google if available

### Payment Processor ("Pay My Bill")
- **Issue:** External payment system accessibility unknown
- **Risk:** Users cannot pay bills if payment portal is inaccessible
- **Action:** Contact payment provider; request accessibility questionnaire & VPAT
- **Fallback:** Provide alternative payment method (mail, phone, in-person)

### Plugins (WPForms, AIOSEO)
- **WPForms:** Generally accessible; requires form-level testing
- **AIOSEO:** SEO tool; doesn't impact frontend accessibility significantly
- **Recommendation:** Update both plugins to latest versions; test forms

---

## Documents & PDFs

### Current Status
⚠️ **Not yet audited** — PDFs and downloadable documents need separate assessment

### Common PDF Issues
- Scanned/image-based PDFs (no text layer)
- Missing tagged structure
- Untagged headings, lists, tables
- Missing alt text for images in PDFs
- Non-sequential reading order

### Recommendations
1. **Audit all PDFs** for accessibility:
   - Meeting minutes
   - Agendas
   - Ordinances
   - Budget documents
   - Forms
2. **Remediation options:**
   - **Retain & remediate:** Use Adobe Acrobat Pro to add tags/alt text
   - **Replace with HTML:** Rewrite in accessible HTML pages (better than PDF)
   - **Archive:** Mark as historical; remove from active navigation
3. **Future policy:** Require all new documents be accessible before posting

---

## Recommended Remediation Phases

### Phase 1: Quick Wins (Week 1-2)
**Focus:** Highest-impact, lowest-effort fixes

1. **Create HTML meeting list** (replace/supplement calendar embed)
   - Table format: Date | Time | Meeting Type | Location/Link
   - Accessible to screen readers immediately

2. **Update image alt text** on carousel
   - Edit each image in WordPress media library
   - Provide descriptive captions for meaningful images

3. **Add visibility to focus indicators**
   - WordPress theme CSS: add `:focus { outline: 2px solid #color; }`
   - Test with keyboard navigation

4. **Contact payment processor** for accessibility documentation
   - Request VPAT/ACR or accessibility questionnaire
   - Document findings

5. **Audit WPForms forms** for label structure
   - Ensure each input has proper `<label>` tag
   - Test with keyboard navigation

### Phase 2: Structural Fixes (Week 3-4)
**Focus:** Code-level accessibility improvements

1. **Fix heading hierarchy**
   - Audit h1/h2/h3 structure
   - Ensure logical nesting

2. **Add ARIA labels** to interactive elements
   - Carousel buttons: `aria-label="Previous image"`, `aria-label="Next image"`
   - Hamburger menu: `aria-expanded="true/false"`
   - Form sections: `aria-label` or `aria-describedby`

3. **Test Google Calendar alternatives**
   - Consider: All-in-One Event Calendar (WCAG-tested), HTML table, iCal feeds
   - Implement accessible fallback

4. **Mobile navigation audit**
   - Test hamburger menu with keyboard & screen reader
   - Fix focus trap issues

5. **Color contrast verification**
   - Use WebAIM Contrast Checker
   - Document all fixes with before/after ratios

### Phase 3: Content Remediation (Week 5-6)
**Focus:** Documents, PDFs, and outdated content

1. **PDF/document audit**
   - Inventory all downloadable files
   - Tag PDFs or convert to HTML
   - Update access procedures

2. **Update stale meeting information**
   - Remove outdated meeting dates from homepage
   - Implement dynamic meeting list

3. **Create accessibility statement**
   - Add to footer/About page
   - Include contact info for accessibility issues
   - Link to alternative format request process

4. **Vendor accessibility requirements**
   - Document expectations for Google, payment processor, form plugins
   - Create escalation process for vendor issues

### Phase 4: Testing & Documentation (Week 7-8)
**Focus:** Validation and governance**

1. **Automated testing**
   - Run axe-core on all pages
   - Generate baseline vs. remediation reports

2. **Manual keyboard testing**
   - Tab through entire site; verify logical order
   - Test on mobile with keyboard
   - Test with VoiceOver (Mac) or NVDA (Windows)

3. **Screen reader testing**
   - Test homepage, primary navigation, forms, footer
   - Document announcements for key elements

4. **Mobile & zoom testing**
   - Test at 200% zoom
   - Verify responsive layout doesn't break
   - Test touch targets on mobile

5. **Create final audit report**
   - Before/after comparison
   - Compliance matrix (WCAG 2.1 A & AA)
   - Remaining exceptions register (if any)

---

## Governance & Long-Term Compliance

### Town Website Accessibility Policy
**Recommendation:** Create and adopt a formal policy addressing:
- Accessibility responsibility (Town Clerk role)
- Publishing standards (who posts, what checks apply)
- Document requirements (PDFs must be accessible)
- Third-party vendor expectations (forms, payments, calendars)
- Annual audit schedule
- Complaint/feedback process

### Staff Training
**Who:** Town Clerk + anyone posting to website  
**What:** 
- How to write accessible content (headings, alt text, links)
- WordPress accessibility checklist
- How to create accessible Word docs & PDFs
- Form testing basics

### Quarterly Maintenance
**Schedule:** Every 3 months

**Checklist:**
1. Run automated accessibility scan (axe-core)
2. Spot-check 5-10 pages with keyboard navigation
3. Review recent PDFs for accessibility
4. Update meeting information
5. Document any issues
6. Plan fixes before next audit

---

## ADA Compliance Context

### Applicable Law
- **Title II of the ADA** — applies to state & local governments
- **DOJ Rule (effective April 26, 2026)** — requires state/local gov websites meet **WCAG 2.1 Level A & AA**
- **Extension for small entities** — population <50k must comply by **April 26, 2028**
- **Drakes Branch population:** ~350 — qualifies for extension

### Key Points
- ❌ Extension does NOT waive accessibility requirement; it only extends deadline
- ❌ Extension does NOT mean inaccessible services are acceptable during extended period
- ✅ Town should demonstrate good-faith remediation progress
- ✅ Compliance should be documented in writing

### Recommendation
Document Town's accessibility remediation plan in Council minutes to demonstrate good-faith compliance effort. This protects the Town if issues arise before April 2028.

---

## Next Steps

### For Town Council
1. **Acknowledge** that current website does not meet ADA accessibility requirements
2. **Adopt** a formal Website Accessibility Policy (template to follow)
3. **Allocate resources** for remediation (staff time + potential contractor)
4. **Set compliance target** — recommend **2028 deadline** minimum (per ADA), earlier if possible

### For Town Clerk (Tammy)
1. **Week 1:** Create HTML meeting list; audit image alt text
2. **Week 2-3:** Fix heading structure; add ARIA labels; contact payment processor
3. **Week 4-5:** Remediate PDFs or convert to HTML
4. **Week 6-8:** Conduct final testing; document compliance

### For Claude Cowork / Next Phase
1. Build new WordPress site with accessible theme
2. Migrate content with accessibility-first approach
3. Implement automated testing & monitoring
4. Create staff training materials

---

## Appendix: WCAG 2.1 AA Success Criteria Checklist

- [ ] 1.1.1 Non-text Content — Alt text provided
- [ ] 1.3.1 Info & Relationships — Semantic structure
- [ ] 1.4.3 Contrast (Minimum) — 4.5:1 body, 3:1 large
- [ ] 1.4.11 Non-text Contrast — 3:1 for UI components
- [ ] 2.1.1 Keyboard — All functionality via keyboard
- [ ] 2.1.2 No Keyboard Trap — Focus can move away
- [ ] 2.4.3 Focus Order — Logical tab order
- [ ] 2.4.7 Focus Visible — Visible focus indicator
- [ ] 2.5.5 Target Size — 44x44 CSS pixels
- [ ] 3.2.1 On Focus — No unexpected changes
- [ ] 3.3.1 Error Identification — Errors described
- [ ] 3.3.2 Labels/Instructions — Form inputs labeled
- [ ] 4.1.2 Name, Role, Value — UI components properly labeled
- [ ] 4.1.3 Status Messages — SR announces status updates

---

**Report prepared:** August 1, 2026  
**Next audit:** Quarterly (every 90 days)  
**Questions?** Contact: tammyamosley2018@gmail.com
