# Boszien Base

Boszien Base is een WordPress block theme voor boszien.org.

Status: architectuur-hardening release `0.1.2`.

Het thema legt een veilige, moderne en onderhoudbare basis voor een Nederlandstalig kennisplatform dat gezondheidsclaims, media-artikelen, supplementclaims en wetenschappelijke beweringen kritisch en begrijpelijk duidt.

## Doel

Boszien Base is geen pagebuilder en geen klassiek PHP-thema. Het is een FSE/block-theme met:

- `theme.json` versie 3 als primaire bron voor kleur, typografie, spacing en layout;
- Gutenberg HTML-templates in `templates/`;
- template parts in `parts/`;
- handmatige, redactionele patronen in `patterns/`;
- één eerste native Interactivity API block: `boszien/evidence-meter`.

## Publicatieveiligheid

0.1.2 behoudt bewust een strenge publicatiegrens:

- geen automatische queryloops;
- geen comments-template;
- geen related posts;
- geen automatische “laatste berichten”;
- geen ongecontroleerde archive-feed;
- geen ongecontroleerde search-resultaten;
- geen automatische weergave van medische claimchecks;
- geen kerninhoud die afhankelijk is van JavaScript.

Archive, search en home gebruiken veilige fallbackteksten totdat redactionele status en contentselectie technisch betrouwbaar zijn ingericht.

## Templates

- `single.html` is bewust minimaal: categorie, titel, datum en post-content.
- `page.html` toont automatisch de paginatitel.
- `page-plain.html` is bedoeld voor redactionele landingpages zonder automatische titel. De editor beheert daar de volledige contentopbouw.
- `front-page.html` gebruikt handmatige secties en veilige patronen.

## Interactivity API

Het block `boszien/evidence-meter` is server-rendered en gebruikt de native WordPress Interactivity API als progressive enhancement.

Zonder JavaScript blijft de inhoud in de HTML aanwezig en leesbaar. Met JavaScript kunnen panelen open en dicht.

## Onderhoudsnotities

- Gebruik geen klassieke templatebestanden zoals `header.php`, `footer.php` of `sidebar.php`.
- Voeg geen externe frontend libraries toe voor eenvoudige interactie.
- Houd `style.css` beperkt tot componentgedrag, focusstates, toegankelijkheidsfixes en layoutdetails die niet logisch in `theme.json` passen.
- Voeg queryloops pas toe wanneer redactionele goedkeuringsstatus betrouwbaar gefilterd kan worden.

## WordPress.com PHP-compatibiliteit

Deze distributie gebruikt `Requires PHP: 8.4`, omdat WordPress.com op dit moment op de betreffende site alleen PHP 8.2, 8.3 en 8.4 aanbiedt. De themecode is met PHP 8.4 gelint; er zijn geen PHP 8.5-only constructies gebruikt.

