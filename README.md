# MD-Pay - Dokumentáció

Miklós Dániel vagyok. Vizsgamunkának készítettem ezt a webshopot. Az oldal dokumentációi és a felhasznált adatbázis itt elérhető:<br><br>
MD-Pay dokumentáció: [https://github.com/MikiDani/mdpay/blob/master/documentation/mdpay_dokumentacio.pdf](https://github.com/MikiDani/mdpay/blob/master/documentation/mdpay_dokumentacio.pdf)<br>
MD-Pay technikai felépítése: [https://github.com/MikiDani/mdpay/blob/master/documentation/mdpay_felepitese.pdf](https://github.com/MikiDani/mdpay/blob/master/documentation/mdpay_felepitese.pdf)<br>
SQL adatbázis: [https://github.com/MikiDani/mdpay/blob/master/documentation/mdpay_sql.zip](https://github.com/MikiDani/mdpay/blob/master/documentation/mdpay_sql.zip)<br><br>
A tesztelés megkönnyítése érdekében feltöltöttem egy ideiglenes tárhelyre ahol tesztelhető:<br><br>
Landing page: [http://web.mikidani.probaljaki.hu/mdpay/index.html](http://web.mikidani.probaljaki.hu/mdpay/index.html)<br>
Frontend rész: [http://web.mikidani.probaljaki.hu/mdpay/frontend/index.html](http://web.mikidani.probaljaki.hu/mdpay/frontend/index.html)<br>
ADMIN rész: [http://web.mikidani.probaljaki.hu/mdpay/backend/index.php](http://web.mikidani.probaljaki.hu/mdpay/backend/index.php)<br>
REST API: [http://web.mikidani.probaljaki.hu/mdpay/api/api.php](http://web.mikidani.probaljaki.hu/mdpay/api/api.php)<br><br>
Az oldalnak három fő részre bontható. REST API rész, ADMIN rész, FRONTEND rész. Az adatok MYSQL adatbázisban vannak tárolva. A REST API kommunikál az adatbázissal. A FRONTEND és az ADMIN az API-n keresztül éri el az adatbázist. Az API és ADMIN felületet PHP-ban, a FRONTEND felület pedig javascript nyelven van megírva. Az oldal az ADMIN és FRONTEND felület reszponzív megjelenítéséhez Bootstrap keretrendszert használ.

## Frontend rész:

### Bemutatkozás menüpont:
Itt a vizsga munkával kapcsolatos információkat lehet megtalálni. A dokumentációkhoz linkeket találunk.

### Felhasználó menüpont:
A felhasználó menüpontban tudunk regisztrálni, vagy ha már regisztráltunk akkor belépni a profilunkhoz a frontend oldalon. Itt két felhasználó már regisztrálva van a könnyebb tesztelhetőség érdekében.

| Felhasználó | Jelszó | Rang  |
| ----------- |:------:| -----:|
| user00      | 123456 | user  |
| admin00     | 123456 | admin |

A bejelentkezés után aktiválódik a kosár és a kedvencek menüpont. Ilyenkor a felhasználó menüpontban megjelenik az adatlapunk ahol módosíthatjuk az elektronikus levél címünket illetve az info mezőnket.  Az email címnek email formátumúnak kell lennie. A jelszavunk megadása után törölhetjük profilunkat, vagy módosíthatjuk jelszavunkat. 
Regisztrációnál ha már regisztrált nevet vagy emailt adunk meg akkor hibaüzenettel tér vissza. Minimum és maximum karakterszámot jelzi a felület ha nem megfelelő hosszúságút adunk meg.

### Termékek menüpont:
Termékek menüpontban először a termékek listáját találjuk. Miután kiválasztottunk egy terméket akkor a termék tulajdonságai aloldalra kerülünk. A termékek menüpontot akkor is elérjük ha nem vagyunk bejelentkezve, viszont nem tudjuk a termékeket a kosárba belerakni, illetve a kedvencek gomb se jelenik meg a termék kiválasztásánál.
#### Termékek szűrése:
A következő módokon szűrhetjük a termékeket:
- Tipus szerint: Az adatbázisban megadott típusok szerint szűri a termékeket.
- Név szerint: ha megtalálható a termék nevében amit megadunk csak azokat listázza ki.
- Minimum ár és Maximális ár szerint: Csak a két ár közötti árnak megfelelő termékeket listázza.
- Akciós termék: Csak azokat a termékeket listázza aminek megvan adva kedvezmény.

Egyszerre több szűrést is megadhatunk. Ha a listából ráklikkelünk egy termékre akkor a termék információs lapra jutunk ahol részletes információkat tudhatunk meg az adott termékről. Ha Akciós a termék akkor külön megjeleníti az eredeti és az akciós árat is. Az akció százaléka is megjelenik a terméknév mellett. A kis képekre kattintva megtehinthetjük nagyban a képet.
##### Ha nem vagyunk bejelentkezve:
- A kosárba gomb helyett egy belépés gomb jelenik meg ami átnavigál a bejelentkezéshez.
##### Ha be vagyunk jelentkezve:
- Megjelenik a kedvencek gomb a terméknév másik oldalán.
- A termék kosárba helyezésének mennyiségét a készlet erejéig lehet növelni.
- A kosárba gombra kattintva a termék bekerül a kosárba és a kosár menüpontra ugrik a képernyő.

### Kosár menüpont:
A kosár ikonja bejelentkezés esetén a fejlécben folyamatosan látható. Ha nincsen benne termék 
akkor üresnek mutatja, ha van akkor sárgán jelzi tartalmát. Ilyenkor megjelenik egy szám ami azt mutatja hogy hány termék található jelenleg a kosárban. A kosár menüpont két részre bontható: kosár tartalmára és a megrendelési adatokra.
#### A kosár tartalma:
A kosár tartalmában azok a termékek jelennek meg amiket kiválasztottunk megvásárlásra. A termékszám változtatható a készlet erejéig. Ha a termékszámot nullára csökkentjük akkor autómatikusan kikerül a kosárból. Alul a kosár tartalmának aktuális végösszegét láthatjuk.
#### Megrendelési adatok:
Itt megjelennek a felhasználó ismert adatai. A megrendeléshez még ki kell tölteni a megrendelési címet és be kell ikszelni a "felhasználási és rendeltetési feltételeket". Ha hiányzik valami a kitöltésből a küldés gomb megnyomása után üzenetben jelzi nekünk a felület. Sikeres megrendelés esetén a megrendelés rögzítődik az adatbázisban, és a bemutatkozás menüpontra ugrunk ahol egy zöld szövegdoboz jelenik meg azzal az üzenettel hogy "Sikeres megrendelésrögzítés". Ekkor az oldal küld az emailünkre egy megrendelés visszaigazoló linket.

### Kedvencek menüpont:
A kedvencek ikonja bejelentkezés esetén a fejlécben folyamatosan megtalálható. A kedvencek listát a felhasználó profijában rögzítődik ellentétben a kosár tartalmával. Hogyha újra bejelentkezünk a kedvencek is újra betöltődnek.
- Ha nincsen kedvenc termékünk akkor egy üres szivecskét látunk.
- Ha van kedvenc termékünk a listában akkor sárgán kitöltött szivecske jelenik meg.
A kedvencek listából egyenként törölhetőek a kedvencek.
### Elérhetőség menüpont:
Az elérhetőség menüpontban megtalálhatóak az elérhetőségeim. Ez alatt van egy email küldési lehetőség ami a mdpay@mikidani.probaljaki.hu levélcímre küld üzenetet. A dokumentációban megtalálhatjuk az elérhetőségeit.

## ADMIN rész:

Az ADMIN felületen csak admin rangú felhasználók tudnak belépni. Itt használható az admin00 felhasználó. Belépéskor a felhasználónak egy token kódja generálódik amit hat órán keresztül használhat a belépésének az azonosítására. Ha közben újra belép akkor a hat óra újra indul a token kódnak.
Az admin felület három részből áll:

### Felhasználók menüpont:
Belépés után ide érkezik meg az oldal. Látjuk a felhasználók listáját. Ha megnyomunk egy felhasználót akkor a bootstrap harmónika kinyílik az adataival. Igény esetén módosíthatjuk a felhasználó rangját vagy törölhetjük az adatbázisból.

### Termékek menüpont:
A termékek listáját tartalmazza. Tartalmaz egy szűrő blokkot ahol igény esetén szűkíthetjük a keresett termékre vagy termékekre a listát. A menü alatt a terméknév megadása és típus kiválasztása után hozzá tudunk adni új terméket az adatbázishoz. Miután hozzáadtuk megjelenik a listában üres adatokkal. Ezután ha kiválasztjuk kitölthetjük a termék adatait. A termékek adatai mellett találunk egy gombot amivel a módosítást rögzíthetjük.
Kép feltöltésekor csak jpg, jpeg, png és gif kiterjesztések megengedettek. A fájl mérete legfeljebb 500 kilobyte lehet. Ha nem megfelelő file-t szeretnénk feltölteni akkor a rendszer üzenetben jelzi mivel van a probléma. Az első kép feltöltése esetén autómatikusan az lesz a kiemelt kép. Több kép feltöltése után az lesz a kiemelt kép amelyikre rákattintunk. A kiemelt kép elsőként listázódik és egy zöld keretet kap. Ha töröli szeretnénk a képet akkor egy piros kuka gombbal megtehetjük. Képaláírást nem kötelező adni a képnek. Termék törlés esetén autómatikusan törlődnek a hozzá kapcsolódó képek és adatok a szerverről. Törlés után nem marad adat szemét a szerveren a termék után.

### Megrendelés menüpont:
Itt a felhasználók által regisztrált megrendelések listáját találjuk időrendi sorrendben listázva a megrendelési azonosítóval. A megrendelő adatain felül megtalálhatjuk a megrendelési címet és a megrendelt termékek listáját. A rendellés levélen történő megerősítését is mutatja a "rendelés megerősítése" sor.

## REST API rész:

Ez az api.php backend rész ami kommunikál a MySQL adatbázissal. A megfelelően felépített beérkező JSON kérésekre válaszol JSON formában. Az azonosítás úgy van felépítve hogy mindenképpen végrehajódik. Ha a kérés szerepel a listán akkor az api továbbengedi az adatcsoport kiválasztása részhez. Ha az API nem kap toket-t a felhasználó azonosítására, és nincsen a kérés a listán akkor Unauthorized üzenettel tér vissza 401-es státuszkóddal. Ha az azonosítás megtörtént az adatcsoport kiválasztása után ér el a megfelelő művelethez a kérés. Ha minden bejövő adat megfelelő akkor vérehajtódik a kért művelet.