# Changelog

## 0.6.0 — native stylesheet delivery

- Replaced the hand-authored header stylesheet link with WordPress-native, theme-versioned style enqueueing.

## 0.5.2 — concise Dutch validation copy

- Corrected the singular announced form-error summary to clear B1 Dutch.

## 0.5.1 — deterministic invalid-field focus

- Applied first-invalid-field focus after Jetpack finishes rendering its validation messages.

## 0.5.0 — form and skip-link accessibility

- Kept the native WordPress skip link as the single sitewide bypass control.
- Added an announced Jetpack form error summary, reliable first-error focus and error relationships for required checkboxes.
- Added visible invalid-field styling without changing form submission or data handling.

## 0.4.1 — dark-card heading contrast

- Kept level-two headings in dark cards on the audited light text color after the consolidated cascade.

## 0.4.0 — visitor-facing taxonomy hygiene

- Redirected empty taxonomy archives temporarily to the seven-topic overview while keeping their terms available for future editorial use.
- Redirected internal `type-*` tag archives to Claimchecks and excluded them from core taxonomy sitemaps.
- Added a defensive `noindex` directive for non-editorial taxonomy archives that are rendered upstream.

## 0.3.0 — reproducible FSE source

- Synchronized all nine public template files with their proven live FSE content, including the functional home and index Query Loops.
- Replaced duplicated desktop and mobile header links with native WordPress Navigation object 4 while preserving the dedicated search control.
- Documented the versioned theme, FSE synchronization and rollback workflow.

## 0.2.5 — dead cascade cleanup

- Removed the non-matching legacy `body:root` critical-header block after 16 route/viewport comparisons proved zero computed-style or geometry changes.
- Removed its 40 unnecessary priority declarations and corrected the migrated prelude opener without changing public presentation.

## 0.2.4 — canonical CSS ownership

- Absorbed the two remaining pre-theme live style layers in their original cascade order so the versioned theme is the sole owner of public presentation.
- Preserved the audited production composition while making the legacy Site Tools and Agent Bridge CSS stores safe to empty without a layout shift.

## 0.2.3 — live CSS consolidation

- Migrated the snapshotted Boszien Site Tools cascade into the versioned theme stylesheet while preserving audited public layout parity.
- Removed legacy `html body` specificity escalation and retained priority only where computed-style or interaction-state testing proved it necessary.
- Restored WCAG AA contrast for the homepage medical boundary text from the canonical dark-section variant.

## 0.2.2 — article breakpoint recovery

- Prevented the article evidence triad from collapsing between the tablet and full three-column desktop layouts.
- Bumped the directly linked stylesheet URL so the responsive fix reaches public visitors without a stale `0.2.1-patch3` browser cache.

## 0.2.1 — visual parity recovery

- Aligned the homepage and article compositions more closely with the approved desktop and mobile reference images.
- Replaced improvised CSS and text glyphs with a local, pinned Lucide 1.10.0 icon set under the ISC license; no runtime CDN or external script was added.
- Added the segmented mobile menu/search control, single-column mobile method panel, and reference-led desktop article rail.
- Restored the approved two-part homepage hero with diagonal mint field, full method panel, icon cues, route rows, and clear CTA hierarchy.
- Recomposed post 519 as an asymmetric editorial layout with compact hero, sticky navigation, primary core conclusion, visual evidence ladder, and dark final verdict.
- Added reliable home/article navigation states and aligned header controls without changing editorial claims or source meaning.
- Bumped direct stylesheet delivery to `0.2.1` and widened only the editorial layout token needed by the article composition.

## 0.2.0

- Publieke CSS-architectuur geconsolideerd naar `theme.json` en `style.css`.
- Poppins/Inter, kleur-, ruimte-, radius- en layouttokens centraal vastgelegd.
- Header, footer, navigatie, zoekingang en CTA sitebreed gestandaardiseerd.
- Native zoek- en archieftemplates met Query Loop en veilige lege staat toegevoegd.
- Homepage, informatiepagina's, intakeformulier en artikel 519 in één responsief componentsysteem gebracht.
- Donkere oppervlakken gehard op `#051410` met lichte tekst en zichtbare focusstates.
- Mobiele insets, targetgroottes en viewportgedrag zonder `100vw`- of overflowmaskers gecorrigeerd.
- Artikelprogressie, desktop-TOC, mobiele details-TOC, bewijsladder en sharecontrols gestyled.
- Custom CSS is geen eigenaar meer van publieke layout- of componentlogica.

## 0.1.2

- WordPress.com PHP 8.4-compatibiliteitsbuild: `Requires PHP` gezet op `8.4`, zonder wijziging van theme-identiteit of functionaliteit.

- Herstelde theme package-identiteit: `Boszien Base`, versie `0.1.2`, text domain `boszien-base`.
- Themamap genormaliseerd naar `boszien-base/`.
- `theme.json` geüpgraded naar versie 3.
- Design tokens verplaatst naar `theme.json`.
- Dubbele CSS-tokens opgeschoond.
- CSS beperkt tot component-layout, focusstates, toegankelijkheid en gerichte hardening.
- Block styles zichtbaar werkend gemaakt voor `.is-style-bz-card` en `.is-style-bz-secondary`.
- Block style registratie verplaatst naar `inc/block-styles.php`.
- Interactivity API block registratie toegevoegd via `inc/interactivity-api.php`.
- Eerste native Interactivity API block toegevoegd: `boszien/evidence-meter`.
- `href="#"` uit patterns verwijderd.
- `page-plain.html` behouden als landingpage zonder automatische titel.
- `home.html` toegevoegd als veilige fallback zonder queryloop.
- Releasebestanden toegevoegd: `README.md`, `CHANGELOG.md`, `LICENSE.md`, `screenshot.png`.
- `load_theme_textdomain()` verwijderd zolang er geen `languages/`-map met vertaalbestanden is ingericht.
