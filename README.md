# Blueghost Test – Aplikace Kontakty

## Obecné informace

Tato aplikace slouží jako jednoduchý adresář pro správu kontaktů. Umožňuje vytvářet, editovat a mazat kontakty s následujícími položkami:

- Jméno (povinné)
- Příjmení (povinné)
- Telefonní číslo
- Email (povinné, validace formátu)
- Dlouhá poznámka

## Použité technologie

- Symfony 7.3 (a jeho recipes)
- PHP 8.2
- Doctrine ORM
- Twig šablony
- Alpine.js pro interaktivitu a AJAX volání
- [Symfony Docker](https://github.com/dunglas/symfony-docker) pro dockerizaci aplikace

Aplikace obsahuje dle zadání pouze základní HTML prvky bez CSS.

Paginace je řešena pomocí AJAX volání, které načítá další kontakty bez nutnosti obnovovat celou stránku.

## Technické poznámky

Základem aplikace je entita `src/Entity/Contact`, kolem které jsou vytvořeny CRUD operace v `src/Controller/ContactController`.
Vzhled dodávají Twig šablony v adresáři `templates`.
Pro správu kontaktů je použit `src/Form/ContactType`, který definuje formulář pro přidání a editaci kontaktů.
`src/Repository/ContactRepository` poskytuje metody pro vyhledávání a filtrování kontaktů.
`src/Factory/ContactFactory` slouží k vytváření testovacích dat pomocí Fakeru.

## Instalace a spuštění

Následující kroky popisují, jak aplikaci nainstalovat a spustit lokálně pomocí Dockeru. Předpokládá se, že máte nainstalovaný Docker a Docker Compose.

1. Klonujte repozitář:
   ```bash
   git clone https://github.com/Komareal/blueghost_test.git
   cd blueghost_test
   ```
2. (Volitelné) Vytvořte soubor `.env.local` pro lokální konfiguraci:
   ```dotenv
   DATABASE_URL=postgresql://db_user:db_password@db_host:5432/db_name
   POSTGRES_HOST=db_host
   POSTGRES_PORT=5432
   POSTGRES_USER=db_user
   POSTGRES_PASSWORD=db_password
   POSTGRES_DB=db_name
   HTTP_PORT=80
   HTTPS_PORT=443
   ```
3. Spusťte Docker Compose a připravte databázi (je občas potřeba počkat, než se spustí kontejnery):

- Vyvíjel jsem s rootless Dockerem, avšak jsem testoval i s běžným Dockerem. Pokud používáte běžný Docker, můžete potřebovat zakomentováné příkazy.
   ```bash
   #spustit Docker Compose a připravit databázi
   docker compose up -d --build
  
   # Bez rootless Dockeru může být potřeba nastavit bezpečnou cestu pro git
   #docker compose exec -it php git config --global --add safe.directory /app
  
   # Bez rootless Dockeru může být potřeba změnit vlastnictví souborů
   #sudo chown -R $USER:$USER . 
   
   # Nainstalovat závislosti.
   docker compose exec -it php composer install

   # Bez rootless Dockeru může být znova potřeba změnit vlastnictví souborů
   #sudo chown -R $USER:$USER .
  
    # Vytvořit databázi a spustit migrace
   docker compose exec -it php bin/console doctrine:migrations:migrate 
  
    # Načíst testovací data pomocí Foundry
   docker compose exec -it php bin/console foundry:load-stories
   ```

Aplikace bude dostupná na localhostu na Vámi zvoleném portu (výchozí je 80 pro HTTP a 443 pro HTTPS).

Symfony Docker poskytuje lokální TLS certifikát. Tudíž je potřeba
[přijmout certifikát jako důvěryhodný](https://stackoverflow.com/questions/7580508/getting-chrome-to-accept-a-self-signed-localhost-certificate/15076602#15076602) ve Vašem prohlížeči.
