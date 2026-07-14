# Changelog

## 0.2.1 — visual parity recovery

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
