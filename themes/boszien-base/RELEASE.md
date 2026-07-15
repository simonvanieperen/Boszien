# Boszien Base releaseworkflow

## Bronnen van waarheid

- Git beheert de negen bestanden in `templates/`, de twee bestanden in `parts/`, `theme.json` en `style.css`.
- WordPress Navigation-object `4` beheert de logische headerlinks. Desktop en mobiel renderen hetzelfde object.
- WordPress Navigation-object `178` beheert de uitgebreidere footerlinks.
- Er is geen afzonderlijk Global Styles-gebruikersobject; `theme.json` is daarom de design-tokenbron.
- Custom CSS, critical CSS en presentatie-snippets blijven leeg. Publieke presentatie hoort in het versiegebonden thema.

## Release

1. Leg branch, commit, gitstatus, live themamanifest en FSE-objectinhoud met hashes vast.
2. Maak een volledige live theme-snapshot.
3. Valideer de release met verwachte stylesheet, versie en bestandshashes.
4. Pas de theme-release atomair toe.
5. Werk alleen wanneer nodig het FSE-object bij naar exact dezelfde genormaliseerde inhoud.
6. Verwijder een custom FSE-override pas nadat het gedeployde themebestand inhoudelijk gelijk is.
7. Purge uitsluitend de bestaande object- en paginacache.
8. Controleer normale publieke URL's zonder cachebuster én één cachebustercontrole.
9. Test status, canonical, console, netwerk, focus, interactie en responsive gedrag.

## Rollback

- Herstel themabestanden via de snapshot-ID van de batch.
- Herstel een FSE-object uit de vóór de batch gelezen raw content of de WordPress-revision.
- Purge daarna object- en paginacache en herhaal de publieke QA.
- Verwijder nooit een afwijkende databasekopie voordat bestandsgelijkheid en rollbackbron zijn bewezen.
