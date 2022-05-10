# Magyar Bertalan LP teszt feladat

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
Ezt ilyen struktúrában kell kitöltenünk:

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

GET `/crud/Product` => A Product entitáshoz listaoldal
GET `/crud/form/Product` => Product entitás létrehozádsa
POST `/crud/form/Product` => Product entitás létrehozás feldolgozás
GET `/crud/form/Product/16` => Product (ID:16) entitás szerkesztése
POST `/crud/form/Product/16` => Product entitás (ID:16) szerkesztés feldolgozás
POST `/crud/delete/Product` => Product entitás törlése



### Contracts, Services

Az implementációs függés csökkentése érdekében nem példányosítunk közvetlenül Service osztályt, hanem a Contracts-ban
határozzuk meg
mit várunk az egyes Service-ktől, és ezeket a Interface-eket az `AppServiceProvider`-ben kapcsoljuk össze a konkrét
service osztályokkal,és a laravel DI container `app(Contracts\AnInterface::class)` keresztül példányosítjuk a
meghatározott
service-t. így a konkrét service osztályoktól nem fogunk függeni.

- `CrudServiceInterface`
    - CRUD műveletekkel kapcsolatos service.


#### API végpontok
A sima HTML kliens mellett lehetőségünk van elérni a CRUD rendszert REST API üzenetekkel is. Ennek a backend logikája ugyanott van,
ahol a HTML kliensé, a controllerben szálazza szét, a request URL-től függően (api/* vagy nem api/*), és HTTP státusz +
JSON objektumokkal kommunikál. E köré sincsen authentikáció/authorizáció építve:

GET `/api/crud/Product` => Product entitás listázása
GET `/api/crud/Product/11` => Product (ID:11) entitás részletezése
POST `/crud/Product` => (HTTP contentben json formátumban a mező:value-k) Product felvétele
PUT `/crud/Product/11` => (HTTP contentben json formátumban a mező:value-k) Product szerkesztése
DELETE `/crud/Product/11` => Product (ID:11) entitás törlése


### Hibakezelés

A Laravel built-in hibakezelési mechanizmusokon túl, egy új `Exception` lett bevezetve: `StatusBarExcetion`, ha ezt
dobjuk fel, annál az esetnél a Laravel Handler.php ban beállított
módon visszairányít az előző oldalra, és a blade fájlokban globálisan elérhető `$errors` változóba be lesz állítva ennek
az exceptionnek a  
`$exception->getMessage()` -je, és az oldal a status barban ki is írja a hibát.

## Frontend
- Bootstrap JS
- Datatable.js
