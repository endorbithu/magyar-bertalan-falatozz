# Magyar Bertalan webshop teszt feladat
## Feladat

### Cél
A cél egy nagyon egyszerű “webshop” elkészítése, alapműveletek lefejlesztésével. A feladat elvégzéséhez két dologra lesz szükség:
- PHP backend, ami egy API interfészt biztosít a műveletekhez.
- Kliens oldal, ami majd meghívja a szükséges api végpontokat és minimális vizuális felületet
  biztosít a termékekkel kapcsolatos műveletekhez  
  Lehetőség szerint mind kliens oldalon, mind API oldalon történjen meg az adatok ellenőrzése és az eredménytől függő visszajelzés.
### API
Az API végzi majd a tényleges munkát, ami lehetőleg REST megvalósítás legyen. Nem lesz szükség sem authentikációra,
sem authorizációra, de nem is hátrány, ha van. A kommunikáció során JSON formátum használata javasolt.
A következő műveleteket kell megvalósítania:
- Termék létrehozása
- Termék módosítása
- Termék törlése
- Termékek listázása
  A termék a következő tulajdonságokkal rendelkezik:
- Név: Termék megnevezése, kötelező adat
- Leírás: A termék rövid leírása, üresen hagyható
- Ár: A termék listaára, kötelező adat
### Kliens
A kliens bármilyen webes megoldással elkészíthető (pl.: html/js). Célja az API interfész
tesztelése/kipróbálása.
#### Termék listaoldal(Főoldal)
A korábban rögzített termékek listája jelenik itt meg. Minden terméknél két gomb található:
- Módosítás
- Törlés
  Erről a felületről(is) tud létrehozni a felhasználó új terméket.
  Ha nincs elérhető termék, akkor csak írja ki, hogy “Jelenleg nincs elérhető termék.”.
#### Termék létrehozás
Egy form ahol meg lehet adni a termék adatait majd elmenteni azokat. Sikeres mentés esetén ezt
jelezze a felhasználónak, majd irányítson át a termék lista oldalra
#### Termék módosítás
A termék létrehozásnál látható felületre hasonlít. De mentéskor itt az adott termék adatait
módosítja.
#### Termék törlése
A terméket törli, majd frissíti a termék listát Ajax kéréssel a háttérben vagy az oldal újra töltésével.

## Áttekintés
webshop teszt feladat megoldása  
Egy egyszerű CRUD rendszer HTML klienssel + API elérhetőség egyéb kliensek számára.  
Egy entitás van felvéve jelenleg: `Product`.  
Authentikáció és authorizáció nincsen.

## Környezet

- Linux (ubuntu)
- Apache 2.4
- Composer 2
- PHP 8
- MySql 8

## Install

- `.env` létrehozása `.env.example` alapján
- `composer install`
- `php artisan migrate`

## Backend

- Laravel 9 framework
- Eloquent ORM

### DB struktúra
#### Táblák

- products
    - `name (idx) | desc | price (idx) | created_at | updated_at`
        
### Entitás hozzáadása a CRUD rendszerhez
Miután elkészítettük és futtattuk a migrációs fájlt, és megvan a Models mappában a táblához tartozó Eloquent modellünk, 
implementáljuk ezen az Eloquent modellen a `App\Contracts\Services\CrudModelInterface` -t.  
Ez egy metódust vár el: `getAttributesInfo():array`.  
Ezt ilyen struktúrában kell kitöltenünk, hogy a frontenden ki tudja "rajzolni", nem kötelező minden mezőt itt felvenni, 
de amelyik nincs felvéve, annak a táblában nullable-nek kell lennie.

```
return [
 'db_attributum_neve|típus|required(opcionális)' => 'Attribútum "szép" neve',
 ...
]
```
típus a frontend input mező típusát jelöli, ezek lehetnek:
- text
- textarea
- number
- hidden
 
Ez után le tudja generálni az összes CRUD HTML oldalt ehhez az entitáshoz. az URL-ben `/crud/{EloquentModelNeve}` azonosítja.

Ez még elég kezdetleges megoldás: boolean, foreign_id, datetime stb mezőket nem tud kezelni, de a bővítés megoldható könnyen.

### Controller, végpontok
A főoldal "/" egy "Admin" felületet mutat rajta egy hivatkozással a `Product` entitás CRUD eléréséhez.  

- GET `/crud/Product` => A Product entitáshoz listaoldal
- GET `/crud/form/Product` => Product entitás létrehozádsa
- POST `/crud/form/Product` => Product entitás létrehozás feldolgozás
- GET `/crud/form/Product/16` => Product (ID:16) entitás szerkesztése
- POST `/crud/form/Product/16` => Product entitás (ID:16) szerkesztés feldolgozás
- POST `/crud/delete/Product` => Product entitás törlése



### Contracts, Services

Az implementációs függés csökkentése érdekében nem példányosítunk közvetlenül Service osztályt, hanem a Contracts-ban
határozzuk meg
mit várunk az egyes Service-ktől, és ezeket a Interface-eket az `AppServiceProvider`-ben kapcsoljuk össze a konkrét
service osztályokkal,és a laravel Dependency Injection technikáját felhasználva példányosítjuk a beállított
service-t:
```
public function show(Request $request, CrudServiceInterface $crudService)
```

így a konkrét service osztályoktól nem fogunk függeni.

- `CrudServiceInterface`
    - CRUD műveletekkel kapcsolatos service.


#### API végpontok
A sima HTML kliens mellett lehetőségünk van elérni a CRUD rendszert REST API üzenetekkel is. Ennek a backend logikája ugyanott van,
ahol a HTML kliensé, a controllerben szálazza szét, a request URL-től függően (api/* vagy nem api/*), és HTTP státusz +
JSON objektumokkal kommunikál. E köré sincsen authentikáció/authorizáció építve:

- GET `/api/crud/Product` => Product entitás listázása
- GET `/api/crud/Product/11` => Product (ID:11) entitás részletezése
- POST `/crud/Product` => (HTTP contentben json formátumban a mező:value-k) Product felvétele
- PUT `/crud/Product/11` => (HTTP contentben json formátumban a mező:value-k) Product szerkesztése
- DELETE `/crud/Product/11` => Product (ID:11) entitás törlése


### Hibakezelés

A Laravel built-in hibakezelési mechanizmusokon túl, egy új `Exception` lett bevezetve: `StatusBarExcetion`, ha ezt
dobjuk fel, annál az esetnél a Laravel Handler.php ban beállított
módon visszairányít az előző oldalra, és a blade fájlokban globálisan elérhető `$errors` változóba be lesz állítva ennek
az exceptionnek a  
`$exception->getMessage()` -je, és az oldal a status barban ki is írja a hibát.

## Frontend
- Bootstrap JS
- Datatable.js
