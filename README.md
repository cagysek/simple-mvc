# simple-mvc
Jedná se o cvičnou jednoúčelovou aplikaci, která má sloužit jako demonstrační ukázka současně pro předměty KIV/WEB a KIV/OKS.

Aplikace je napsána tak, aby byla smysluplně použita většina technologií požadovaných v KIV/WEB.

Aplikace je připravena jako testovatelná, což zejména znamená:
* každý pro ovládání významný element má svoje unikátní ID,
* každá akce má svojí ihned viditelnou – a tudíž ověřitelnou – odezvu.

Aplikace používá reálná data z validátoru studentských úloh.

* pro demonstrační spuštění v lokální instalaci jsou v nich osobní údaje anonymizované, v reálné webové instalaci jsou osobní údaje skutečné.
* Je použit velmi jednoduchý – leč postačující – databázový model.


# Prerekvizity
* PHP >= 7.4 !!! (typované funkce a parametry)

# Postup instalace
* Rozbalte instalační soubor do adresáře, odkud se spouští `localhost`
* Pokud nebude složka rozbalena v kořenovém adresáři `localhost` je nutné specifikovat cestu od kořenového adresáře k souboru `index.php` v konfiguračním souboru `config/env.php`. Např. projekt bude umístěn v adresáři `localhost` ve složce `simple-mvc`, v konfiguračním souboru bude tedy nutné nastavit konstantu `path_to_root` na hodnotu `/simple-mvc`
* V lokální SŘBD si založte novou databázi, ideálně se jménem `upa2` databáze bude prázdná
* V souboru `config/database.php` zkontrolujte a případně přenastavte hodnoty konstant `host`, `database`, `username` a `password`
* Do adresního řádku webového prohlížeče zadejte `http://localhost/`
* Po úplně prvním spuštění se v SŘBD automaticky vytvoří tři tabulky: `settings` (předvyplněná), `students` (prázdná) a `tasks` (prázdná)
* Zvolte submenu  Nepřihlášený / Registrace
* Zaregistrujte se v roli  Učitel – protože je učitel pouze jeden, stačí jen zadat jakékoliv počáteční heslo
* V menu  Nastavení v záložce Počáteční inicializace načtěte soubor `studenti-debug.csv`
* V menu  Nastavení v záložce  Aktualizace úloh načtěte soubor `ulohy-debug.csv`
* Od této chvíle můžete aplikaci používat