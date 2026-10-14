# AMKLogi

[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Yii 2](https://img.shields.io/badge/Yii-2.0.55-40B3D8)](https://www.yiiframework.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![SQLite](https://img.shields.io/badge/SQLite-kehitys%20%26%20testit-003B57?logo=sqlite&logoColor=white)](https://www.sqlite.org/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)](https://docs.docker.com/compose/)
[![Testit](https://img.shields.io/badge/testit-Codeception%205-success)](https://codeception.com/)
[![PHPStan](https://img.shields.io/badge/PHPStan-taso%205-brightgreen)](https://phpstan.org/)

**AMKLogi** on sosiaalinen mediapalvelu ammattikorkeakoulun opiskelijoille. Käyttäjä voi
rekisteröityä, täyttää profiilinsa, ladata profiilikuvan ja kirjoittaa julkisia tai yksityisiä
kommentteja muiden käyttäjien seinille.

Sovellus tehtiin alun perin vuonna 2013 opinnäytetyönä Yii 1.1 -sovelluskehyksellä
([opinnäytetyö Theseuksessa](https://www.theseus.fi/items/e550838b-7119-40a6-afe7-92c0ec3d4b0b)).
Tämä versio on uudistettu **Yii 2:lle ja PHP 8.5:lle**: tietoturva-aukot on korjattu, tietokantarakenne
on rakennettu uudelleen, ja sovelluksen voi käynnistää yhdellä komennolla Dockerissa.

## Pika-aloitus

```bash
./dev          # rakentaa ja käynnistää sovelluksen, MySQL:n ja Mailpitin
```

| Palvelu | Osoite |
|---|---|
| AMKLogi | http://localhost:8080 |
| Mailpit (lähtevät sähköpostit) | http://localhost:8025 |

Demotunnukset: `matti`, `liisa` ja `admin`. Salasana on kaikilla `demo1234`.

---

<details>
<summary><strong>Ominaisuudet</strong></summary>

<br>

| Ominaisuus | Kuvaus |
|---|---|
| Rekisteröityminen | Tunnus, sähköposti ja salasana kahteen kertaan. Tunnus on uniikki kirjainkoosta riippumatta, eikä se voi olla sovelluksen varattu polku (esim. `login`). |
| Kirjautuminen | Tunnus ja salasana, "Muista minut" -eväste. Salasanat tallennetaan bcrypt-tiivisteinä. |
| Profiilisivu | Osoitteessa `/<tunnus>`, kuten alkuperäisessä sovelluksessa. Näyttää profiilikuvan ja täytetyt tiedot. |
| Profiilin muokkaus | Nimi, sukupuoli, syntymäpäivä, paikkakunta, parisuhde, sähköposti, kotisivu ja Skype. Tämä puuttui alkuperäisestä versiosta. |
| Profiilikuva | JPG, PNG, GIF tai WebP, enintään 5 Mt. Kuva pienennetään (max 600 px) ja tallennetaan JPEG-muodossa. |
| Kommenttiseinä | Julkiset ja yksityiset kommentit. Lähetys toimii ilman sivun uudelleenlatausta, ja myös ilman JavaScriptiä. |
| Yksityiset kommentit | Näkyvät vain kirjoittajalle, seinän omistajalle ja ylläpitäjälle (`admin`-rooli). |
| Uusien kommenttien ilmoitus | Valikossa näkyy uusien kommenttien määrä. Se nollautuu, kun käyttäjä avaa oman sivunsa. |
| Käyttäjälista | Kaikki käyttäjät sivutettuna. |
| Yhteydenottolomake | Lähettää sähköpostin ylläpidolle. CAPTCHA on käytössä, kun GD tai Imagick on asennettu. |
| Käyttöliittymä | Suomenkielinen, Bootstrap 5, vaalea ja tumma teema. |

</details>

<details>
<summary><strong>Käynnistys Dockerilla (<code>./dev</code>)</strong></summary>

<br>

Vaatimukset: Docker ja Docker Compose. Kaikki komennot ajetaan projektin juuresta.

| Komento | Mitä tekee |
|---|---|
| `./dev` tai `./dev up` | Rakentaa imaget tarvittaessa ja käynnistää kaiken taustalle |
| `./dev run` | Käynnistää etualalle lokit näkyvissä (Ctrl+C pysäyttää) |
| `./dev restart` | Käynnistää kontit uudelleen |
| `./dev recreate` | Rakentaa imaget uudelleen ja luo kontit uudestaan. **Data säilyy.** |
| `./dev down` | Pysäyttää ja poistaa kontit. Data säilyy. |
| `./dev reset` | Kuten `recreate`, mutta **poistaa tietokannan, kuvat ja runtime-datan** (kysyy varmistuksen, `-y` ohittaa) |
| `./dev status` | Konttien tila |
| `./dev logs [app\|db\|mailpit]` | Seuraa lokeja |
| `./dev shell` | Komentotulkki app-konttiin (www-data-käyttäjänä) |
| `./dev yii <komento>` | Yii-konsolikomento, esim. `./dev yii migrate/history` |
| `./dev migrate` | Ajaa tietokantamigraatiot |
| `./dev seed` | Luo demokäyttäjät, jos tietokanta on tyhjä |
| `./dev mysql` | MySQL-asiakas sovelluksen tietokantaan |
| `./dev test` | Ajaa testit omassa test-imagessaan |

Ilman skriptiä samat asiat onnistuvat suoraan Composella:

```bash
docker compose up -d --build                    # käynnistys
docker compose --profile test run --rm tests    # testit
docker compose down                             # pysäytys
```

#### Mitä Docker-ympäristössä on

| Palvelu | Image | Tehtävä |
|---|---|---|
| `app` | `yiisoftware/yii2-php:8.5-apache` + sovellus | PHP 8.5 + Apache. Käynnistyessä odottaa tietokantaa, ajaa migraatiot ja tarvittaessa luo demodatan. |
| `db` | `mysql:8.4` | Tietokanta (utf8mb4) |
| `mailpit` | `axllent/mailpit:v1.31` | Ottaa vastaan sovelluksen sähköpostit, joita voi lukea selaimella |
| `tests` | Dockerfilen `test`-vaihe | Ajetaan vain `test`-profiililla |

Pysyvä data on nimetyissä volumeissa: `db-data` (tietokanta), `uploads` (profiilikuvat) ja `runtime`
(lokit, välimuisti, evästeavain).

</details>

<details>
<summary><strong>Ympäristömuuttujat</strong></summary>

<br>

Docker Compose lukee arvot `.env`-tiedostosta. Kopioi `.env.example` → `.env` ja muuta tarvittaessa.

| Muuttuja | Oletus | Kuvaus |
|---|---|---|
| `APP_PORT` | `8080` | Sovelluksen portti |
| `MAILPIT_PORT` | `8025` | Mailpitin selainkäyttöliittymän portti |
| `YII_ENV` | `prod` (Docker) / `dev` (paikallinen) | Yii-ympäristö. `dev` ottaa käyttöön debug-palkin ja Giin. |
| `YII_DEBUG` | `0` (Docker) / `1` (paikallinen) | Virheiden yksityiskohtainen näyttö |
| `DB_DSN` | SQLite: `runtime/amklogi.sqlite` | Tietokantayhteys, esim. `mysql:host=db;dbname=amklogi` |
| `DB_USERNAME` / `DB_PASSWORD` | `amklogi` / `amklogi` | Tietokannan tunnukset (vaihda tuotannossa) |
| `DB_ROOT_PASSWORD` | `amklogi-root` | MySQL:n root-salasana (vain Docker) |
| `MAILER_DSN` | – | SMTP-yhteys, esim. `smtp://mailpit:1025`. Ilman tätä viestit tallennetaan hakemistoon `runtime/mail`. |
| `ADMIN_EMAIL` | `admin@amklogi.test` | Yhteydenottolomakkeen vastaanottaja |
| `SENDER_EMAIL` | `noreply@amklogi.test` | Lähettäjän osoite |
| `COOKIE_VALIDATION_KEY` | generoidaan | Evästeiden allekirjoitusavain. Tyhjänä avain luodaan tiedostoon `runtime/cookie_validation.key`. |
| `SEED_DEMO_DATA` | `true` | Luo demokäyttäjät ensimmäisellä käynnistyksellä (vain Docker) |
| `APP_TIMEZONE` | `Europe/Helsinki` | Aikavyöhyke |

</details>

<details>
<summary><strong>Paikallinen kehitys ilman Dockeria</strong></summary>

<br>

Vaatimukset: PHP ≥ 8.2 (laajennukset `pdo_sqlite`, `intl`, `mbstring`) ja Composer. GD tai Imagick
on vapaaehtoinen. Ilman niitä profiilikuvia ei pienennetä eikä CAPTCHA ole käytössä.

```bash
composer install
./yii migrate --interactive=0   # luo SQLite-tietokannan runtime/amklogi.sqlite
./yii seed                      # demokäyttäjät (valinnainen)
composer serve                  # http://localhost:8080
```

Paikallisesti sovellus toimii `dev`-tilassa: Yii-debug-palkki ja Gii-koodigeneraattori (`/gii`)
ovat käytössä. Sähköpostit tallennetaan hakemistoon `runtime/mail`.

MySQL:n saa käyttöön asettamalla muuttujat, esimerkiksi:

```bash
DB_DSN='mysql:host=127.0.0.1;dbname=amklogi' DB_USERNAME=amklogi DB_PASSWORD=salasana composer serve
```

</details>

<details>
<summary><strong>Testit ja laadunvarmistus</strong></summary>

<br>

| Komento | Mitä tekee |
|---|---|
| `composer tests` | Ajaa migraatiot testitietokantaan (`runtime/test.sqlite`) ja Codeceptionin yksikkö- ja toiminnalliset testit |
| `./dev test` | Samat testit Docker-imagessa (GD käytössä) |
| `composer static` | PHPStan, taso 5 |
| `composer cs` | Koodityylin tarkistus (Yii 2 coding standard) |

Testit kattavat muun muassa:

- salasanojen bcrypt-tiivisteet, kirjautumisen ja rekisteröitymisen validoinnit
- kirjainkoosta riippumattoman tunnusten yksilöllisyyden ja varatut tunnukset
- yksityisten kommenttien näkyvyyden vieraalle, muille käyttäjille, omistajalle, kirjoittajalle ja ylläpitäjälle
- sen, ettei kommentin kirjoittajaa voi väärentää pyynnöllä
- HTML:n escapeoinnin (XSS) kommenteissa ja profiilissa
- `javascript:`-kotisivulinkkien hylkäämisen ja profiilikuvan tiedostotyypin tarkistuksen
- uusien kommenttien ilmoituksen nollautumisen

</details>

<details>
<summary><strong>Tietokanta</strong></summary>

<br>

Alkuperäisen sovelluksen tietokantarakennetta ei ollut tallennettu repositorioon, joten se rakennettiin
uudelleen mallien dokumentaation pohjalta. Migraatiot (`migrations/`) toimivat sekä MySQL:ssä
että SQLitessä.

| Taulu | Sarakkeet | Huomioita |
|---|---|---|
| `user` | `id`, `username`, `email`, `password_hash`, `auth_key`, `role`, `last_login_at`, `created_at`, `updated_at` | `username` ja `email` uniikkeja, `role` = `student` tai `admin` |
| `profile` | `id`, `user_id`, `firstname`, `surname`, `gender`, `birthday`, `email`, `city`, `relationship`, `homepage`, `skype`, `avatar_path`, aikaleimat | Yksi profiili per käyttäjä, luodaan rekisteröitymisen yhteydessä |
| `comment` | `id`, `author_id`, `recipient_id`, `body`, `is_private`, `read_at`, aikaleimat | Viiteavaimet käyttäjiin (`ON DELETE CASCADE`) |

</details>

<details>
<summary><strong>Hakemistorakenne</strong></summary>

<br>

```text
assets/          Asset bundle (CSS/JS)
commands/        Konsolikomennot (php yii seed)
config/          Konfiguraatio: web, console, test, tietokanta, sähköposti, URL-säännöt
controllers/     Site-, User-, Profile- ja CommentController
docker/          Docker-entrypoint ja PHP-asetukset
migrations/      Tietokantamigraatiot
models/          ActiveRecord-mallit ja lomakemallit
tests/           Codeception: Unit- ja Functional-testit
views/           Näkymät (layout, site, user, profile)
web/             Julkinen hakemisto: index.php, css, js, uploads/
widgets/         Alert-widget
dev              Kehitysskripti Docker Composelle
Dockerfile       app- ja test-imaget
docker-compose.yml
```

</details>

<details>
<summary><strong>Mitä vuoden 2013 versiosta muuttui</strong></summary>

<br>

| Alkuperäinen (Yii 1.1, 2013) | Uudistettu (Yii 2, 2026) |
|---|---|
| SQL-kyselyt rakennettiin merkkijonoja yhdistelemällä, mikä altisti SQL-injektioille | ActiveRecord ja parametrisoidut kyselyt |
| Kommentit ja profiilitiedot tulostettiin escapeoimatta, mikä mahdollisti XSS-hyökkäykset | Kaikki käyttäjän syöte escapeoidaan, ja kotisivulinkiksi kelpaa vain `http(s)` |
| Kommentin kirjoittaja (`from_uid`) luettiin lomakkeelta, ja kommentointi ja kuvan lataus onnistuivat kirjautumatta | Kirjoittaja on aina kirjautunut käyttäjä, ja kirjautuminen vaaditaan |
| Salasanat tallennettiin suolaamattomina MD5-tiivisteinä | bcrypt |
| Ei CSRF-suojausta AJAX-kommenteille | CSRF-tarkistus kaikissa lomakkeissa |
| Tunnuksen yksilöllisyyttä ja salasanan vahvistusta ei tarkistettu | Validoinnit suomeksi |
| Kovakoodatut polut (`C:\xampp\...`, `http://localhost/amklogi/...`) | Asetukset ympäristömuuttujista |
| Gii päällä salasanalla `1234`, debug-tila aina päällä, tietokantana root ilman salasanaa | Debug ja Gii vain `dev`-tilassa, tuotantoasetukset Dockerissa |
| Yksityisyys tallennettiin merkkijonoina `"true"`/`"false"` | Boolean-sarake ja luettu-aikaleima |
| jQuery 1.5/1.9 ja Blueprint CSS | Bootstrap 5, `fetch()` ja tumma teema |
| Ei testejä | Codeceptionin yksikkö- ja toiminnalliset testit, PHPStan |
| XAMPP | Docker Compose: PHP 8.5, MySQL 8.4 ja Mailpit |

Alkuperäinen Yii 1.1 -koodi löytyy `master`-haarasta.

</details>

<details>
<summary><strong>Rajoitukset ja jatkokehitys</strong></summary>

<br>

- **Vanhojen käyttäjien siirto:** alkuperäinen tietokanta ei ole tallessa. Jos vanha dump löytyy,
  käyttäjät pitää siirtää erikseen, eikä MD5-salasanoja voi muuntaa. Käyttäjien täytyy asettaa uusi salasana.
- **Kuva-albumi:** alkuperäisessä versiossa oli vain tyhjä `album`-sivu ja käyttämätön `images`-taulu,
  joten niitä ei siirretty.
- **Kurssit ja ryhmät:** alkuperäisen valikon linkit eivät johtaneet mihinkään, joten ne on jätetty pois.
- **Salasanan palautus sähköpostilla** ja **kommenttien poisto** ovat luontevia seuraavia ominaisuuksia.

</details>

<details>
<summary><strong>Linkit</strong></summary>

<br>

| | |
|---|---|
| Alkuperäinen opinnäytetyö | [Theseus](https://www.theseus.fi/items/e550838b-7119-40a6-afe7-92c0ec3d4b0b) |
| Yii 2 -opas | https://www.yiiframework.com/doc/guide/2.0/en |
| Yii 2 API | https://www.yiiframework.com/doc/api/2.0 |
| Codeception + Yii 2 | https://codeception.com/for/yii |

</details>
