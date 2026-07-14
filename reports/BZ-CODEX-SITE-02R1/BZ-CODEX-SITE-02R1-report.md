# BZ-CODEX-SITE-02R1 — tussenrapport vóór releasegates

Datum: 2026-07-14  
Status: lokaal gereed; deployment geblokkeerd door verplichte reviewgates.

## Checkpoint en versie

- Checkpoint 0.2.0: intact.
- Gecontroleerde rollbackroute: snapshot `boszien-base-20260713-221555` en actuele theme-snapshots waren beschikbaar.
- Live theme vóór/na deze run: `0.2.0` / `0.2.0`.
- Lokale kandidaat: Boszien Base `0.2.1`.
- GitHub-branch: `bz-codex-site-02r1-visual-parity`.
- Releasecommit: `3cd686af4efe8b2db173cc3e2ba3b47776a44ad9`.
- Draft-PR: https://github.com/simonvanieperen/Boszien/pull/1
- Deployment-ID: niet aangemaakt.
- Nieuw snapshot-ID: niet aangemaakt.
- Rollback uitgevoerd: nee.

## Gewijzigde releasebestanden

- `.gitignore`
- `themes/boszien-base/CHANGELOG.md`
- `themes/boszien-base/boszien-base-manifest.json`
- `themes/boszien-base/parts/header.html`
- `themes/boszien-base/style.css`
- `themes/boszien-base/theme.json`
- `reports/BZ-CODEX-SITE-02R1/scripts/release-wordpress-objects.ps1`

PHP, JavaScript, templates, template-parts buiten de header, fonts en assets zijn ongewijzigd. JSON en JavaScript valideren lokaal; de centrale CSS-validator meldt geldige CSS. Een lokale PHP-runtime is niet geïnstalleerd, waardoor PHP-lint pas in de theme-releasepreview kan worden bewezen.

## Geplande WordPress-objecten

De read-only preview is geslaagd voor:

- Custom CSS-noodlaag: alleen versiemarker `0.2.0` → `0.2.1`, 90 tekens;
- FSE template-part `boszien-base//header`: stylesheetversie, zoekinterface, CTA en actieve navigatiestaten.

Geen pagina- of postinhoud wordt gewijzigd. De claims, bronnen en inhoud van pagina 366 en post 519 blijven ongewijzigd.

## Visuele methode en lokaal resultaat

De primaire URL-, DOM- en visuele baseline kwam uit een nieuwe geïsoleerde in-app Browser-tab op `https://boszien.org/`. Omdat die Browser geen exacte viewportinstelling exposeerde, is de voorgeschreven fallback gebruikt: Playwright met de lokaal geïnstalleerde Chrome-binary.

- Mobiel 390 × 844: homepage en post 519 zonder horizontale overflow; full-bleed en veilige insets intact.
- Desktop 1440 × 900: homepage en post 519 zonder horizontale overflow; kerncompositie aantoonbaar gelijk aan de gekozen referentie.
- De homepage heeft de tweedelige hero, mint diagonale zone, volledig donker methodepaneel met vier iconische routes, pijlen en CTA-hiërarchie.
- Post 519 heeft een compacte hero, sticky linkerrail, brede redactionele kolom, primaire kernconclusie, verticale bewijsladder en donker eindoordeel rechts.
- De overige verplichte viewports en routes zijn bewust nog niet als live postflight uitgevoerd, omdat deployment niet is toegestaan vóór de reviewgates.

## Geblokkeerde gates

### CodeRabbit

- `coderabbit` is niet geïnstalleerd.
- De door de skill verplichte installer is uitgevoerd maar weigert Windows (`Unsupported operating system: mingw64_nt-10.0-26200`).
- WSL en Docker zijn niet beschikbaar.
- `@coderabbitai review` is op PR 1 geplaatst, maar de repository-integratie heeft geen review of reactie geleverd.
- CodeRabbit raised geen rapporteerbare issues, omdat geen review kon worden uitgevoerd.

### Codex Security

- Werkruimte: `78229521-9f06-4409-9154-c1a50741d047`.
- Duurzame scan: `b794a64a-792e-4591-8262-06f1366e8c77`.
- De werkruimte valideerde onverwacht modus `standard`; de gevraagde diffmodus is daardoor niet actief.
- Preflightstatus: `incomplete`.
- Open capability: `usable_worker_slots_6`; de actieve multi-agentmodus en minimaal zes bruikbare workers zijn niet aantoonbaar beschikbaar.
- Er zijn geen securitybevindingen, scanartefacten, fixes of approvals gegenereerd.
- De scan blijft veilig `running` voor hervatting; hij is niet geannuleerd of als mislukt gemarkeerd.

## Releasebesluit

Geen deployment, cachepurge of live WordPress-write uitgevoerd. Dat voorkomt dat een lokaal visueel goede kandidaat de expliciete CodeRabbit- en Codex Security-gates omzeilt.

## Screenshots

![Goedgekeurde homepage-referentie desktop](C:/Users/simon/OneDrive/Documenten/BoszienGPT/reports/BZ-CODEX-SITE-02/design/homepage-desktop-concept.png)

![Lokale 0.2.1 homepage desktop](C:/Users/simon/.codex/visualizations/2026/07/14/BZ-CODEX-SITE-02R1/parity-local-home-desktop-1440-v2.png)

![Lokale 0.2.1 homepage mobiel](C:/Users/simon/.codex/visualizations/2026/07/14/BZ-CODEX-SITE-02R1/parity-local-home-mobile-390.png)

![Goedgekeurde artikelreferentie desktop](C:/Users/simon/OneDrive/Documenten/BoszienGPT/reports/BZ-CODEX-SITE-02/design/article-519-desktop-concept.png)

![Lokale 0.2.1 post 519 desktop](C:/Users/simon/.codex/visualizations/2026/07/14/BZ-CODEX-SITE-02R1/parity-local-post519-desktop-1440.png)

![Lokale 0.2.1 post 519 mobiel](C:/Users/simon/.codex/visualizations/2026/07/14/BZ-CODEX-SITE-02R1/parity-local-post519-mobile-390.png)
