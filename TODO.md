dodaj menu dla mobilnej wersji
dostosuj footer do mobnilnej wersji
Zoptymalizuj strone dla SEO

Stworz prosty panel administracyjny z haslem do zmiany tresci dla artykulow, SEO, konfiguracji strony, menu, mediow, korzystaj z SQLite w folderze ../
przenies wszystkie zmienne do SQL i pozwol je edytowac, stworz API poprzez ktore beda dostepne dla frontendu

ustandaryzuj style css dla index.php i training.php
Stworz aplikacje w dockerfile i docker compose
stworz testy do strony i panelu administracyjnego
dodaj kluczowe zmienne i sciezki do .env

Każda sekcja powinna modularna, niezależna, być w formacie html w osobnych plikach dla html, css, js, sql query,  sql scehma, 
folder powinien mieć nazwe sekcji, np header, footer, menu, itp
Każdy folder sekcji będzie dynamicznie dodawany do index.php w zależnosci od konfiguracji w SQL, gdzie będzie określone ktora sekcja ma być ładowana i w jakiej kolejności

zaktualizauj install.php i install.sh, gdzie jest generowana baza danych, aktualizuj baze z kazdej sekcji sections/*/schema.sql

Dodaj do kazdej sekcji plik admin.php, ktory bedzie ladowany przy ladowaniu panelu administracyjnego na podobnej zasadzie co index.php strony www, w celu edycji danej sekcji na landing page dla admina w pliku sections/index.php

Dodaj sekcję footer,
Dodaj sekcję menu, categories, tags, article, blog
Dodaj sekcję sitemap, rss, do wyswietlania mapy strony w xml i rss rozszerzeniem odpowiednim dla danej sekcji z administracją i sql, obie sekcje powinny korzystać z sekcji menu, categories, article, tags

Stworz mechanizm dodawania komponentów w folderze components/,
Dodaj component 'upload', 'files', 'gallery' i wygeneruj takie, ktore mozna reuzywac w podstronach, sekcjach, stworz przyklad w sections uzycia component 


Do kazdego component-u dodaj plik admin.php, oraz api.php z curl.sh do testowania z prostym generowatorem tokenow do uwierzytelnionej komunikacji

pozwol na zarzadzanie każdą stroną na podstawie struktury component i sections w panelu administracyjnym, zaktualizuj sql i uzyj admin.php z modulow z section i components

plik ./schema.sql nie powinien zawierac juz raz zdeklarowanych tabeli z component lub sections np z /sections/rss/schema.sql, gdyż każda scehma z modułu powinna być załadowana oddzielnie i tam powinny znajdować sie dane dotyczące tabeli do konrkretnego modułu

panel administracyjny admin/index.php powinien mieć menu do wyboru sections.php gdzie będą edytowane ładowane sekcje z folderu sections/*/admin.php

panel administracyjny admin/index.php powinien mieć menu do wyboru component.php gdzie będą edytowane ładowane komponenty z folderu component/*/admin.php




Stworz diagnostyke do testowania czy w plikach componentow, sections są błędy, uruchom je i sprawdz i pokaz błędy w panelu administracyjnym oraz w pliku test.php i test.sh w folderze glownym z opcjonalnym parametrem dotyczacym co ma byc testowane
Dodaj opcję w test.sh do sprawdzania czy sa duplikaty tworzonych tabel i kolumn w scehma.sql głównym i w modułach, komponentach, usuń duplikaty w ./schema.sql  

Dopasuj wyglad panelu administracyjnego aby byl bardziej mobilny i latwiejszy w uzyciu z mozliwoscia tesowania live w okienku obok, zwłasza tutaj: http://localhost:8007/admin/index.php?page=sections

Stworz sekcje Meta, gdzie będą przechowywane metadane m.in. dla SEO i roznego typu dla lepszej rozpoznawalnosci strony w interrnecie i integracji z roznymi aplikacjami i social mediami
Dodaj przykladowe dane dom sekcji meta w schema.sql na podstawie pliku header.php

Stworz folder na kompletne aplikacje/strony niezalezne moduły typu: blog, RSS, SITEMAP, które korzystaja z danych strony
przeniesś wcześniej utworzone blog, RSS, SITEMAP

Dodaj więcej funkcji testowania api/ i admin/ poprzez uruchomienie skryptu ./test.sh


Stworz folder na funkcje przetwarzania danych w locie i integracji z frameworkiem/sql, nazwij go odpowiednio i dodaj: shortcode, webhooks, API, ktore bedą integrowały się z sections, pokaż przykłady użycia jak skonfigurowac i uzywac w sekcjach, np przy wyswietlaniu filmu z youtube  oraz translacji treści do innych języków pobieranych z sql
zaktualizuj panel administracji do tego rozwiazania


Przebuduj panel admin aby był modularny i składał się z integrations, components, modules, sections
Stwórz odseparowaną bazę danych dla administracji i dla projektów stron użytkownika




W folderze admin jest oddzielna aplikacja do zarządzania stroną, gdzie mają dostęp tylko zalogowani użytkownicy



NOCODE

Dodaj sekcje kontakt, gdzie bedzie formularz zapytania z obslugą SMTP i zapisaniem haseł w panelu z SQL oraz testem w panelu poprzez test.php
Dodaj do kazdej sekcji plik test.php, ktory pozwoli na przetestowanie modulu pod wzgledem mozliwosci zainstalowania, powinny pokazywac sie new try Exceptions

Dodaj rss



Stworz strukture dla projektow stron www, wedle wzoru: /www/{domain}/sections/{section}/data.sql
Przenieś dla pierwszej strony localhost wszystkie SQL z index.sql do odpowiednich folderów i plików w  /www/{domain}/sections/{section}/data.sql

Stworz nową stronę w tabeli site z suffixem, gdzie będzie uruchamiany visualizer po wejsciu na http://localhost:8007/visualizer/