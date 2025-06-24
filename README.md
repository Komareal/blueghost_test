# Blueghost test - Aplikace konatkty

## Obecné informace

Tato aplikace slouží jako jednoduchý adresář pro správu kontaktů. Umožňuje vytvářet, editovat a mazat kontakty s následujícími položkami:

- Jméno (povinné)
- Příjmení (povinné)
- Telefonní číslo
- Email (povinné, validace formátu)
- Dlouhá poznámka

## Použité technologie

- Symfony 7.3 (a jeho recepies)
- PHP 8.2
- Doctrine ORM
- Twig šablony
- Alpine.js pro interaktivitu a AJAX volání
- https://github.com/dunglas/symfony-docker - Symfony Docker pro dockerizaci aplikace

Aplikace obsahuje dle zadání pouze základní HTML prvky bez CSS

Paginace je řešena pomocí AJAX volání, které načítá další kontakty bez nutnosti obnovovat celou stránku.

## Instalace a spuštění

Následující kroky popisují, jak aplikaci nainstalovat a spustit lokálně pomocí Dockeru. Předpokládá se, že máte nainstalovaný Docker a Docker Compose.

1. Klonujte repozitář:
   ```bash
    git clone https://github.com/Komareal/blueghost_test.git
    cd blueghost_test
    ```
2. (Volitelné) Vytvořte `.env.local` soubor pro lokální konfiguraci:
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
3. Spusťte Docker Compose a připravte databázi:
    ```bash
    docker-compose up -d --build
    docker-compose exec php bin/console doctrine:migrations:migrate #provede migrace
    docker-compose exec php bin/console foundry:load-stories #načte testovací data
    ```

Aplikace bude dostupná na localhostu na Vámi zvoleném portu (výchozí je 80 pro HTTP a 443 pro HTTPS).

Symfony Docker poskytuje lokální TLS certifikát. Tudíž je potřeba
[přijmout certifikát jako důvěryhodný](https://stackoverflow.com/questions/7580508/getting-chrome-to-accept-a-self-signed-localhost-certificate/15076602#15076602) ve Vašem prohlížeči.
