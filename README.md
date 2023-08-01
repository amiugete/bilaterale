# bilaterale

Funzionalità per il bilaterale

## Installazione

Vedi dipendenze sotto e crea i file nascosti:

- *conn_test.php*  per creare le connessioni al DB usato dall'ambiente di test
- *conn.php* per creare le connessioni al DB usato dall'ambiente di produzione

```
<?php 
$conn = pg_connect("host=XXX.XXX:X.XXX" port=5432 dbname=XXXX user=XXXX password=XXXXXX");
if (!$conn) {
        die('<br>Could not connect to DB PostgreSQL, please contact the administrator.');
}



include 'ldap.php';
include 'jwt.php';
?>
```

- *ldap.php*: parte segreta per connettersi al dominio di AMIU e verificare gli utenti (vedi autenticazione Gruppo Sigla)

```
<?php
$ldapDomain = "@domain.com"; 			// set here your ldap domain
$ldapHost = "ldap://XXX.XXX:X.XXX"; 	// set here your ldap host
$ldapPort = "389"; 						// ldap Port (default 389)
$ldapUser  = "USER"; 						// ldap User (rdn or dn)
$ldapPassword = "PWD";
?>
```

- *jwt.php*: parte segreta per creare jwt al SIT di AMIU (vedi autenticazione Gruppo Sigla)

```
<?php
// provenienza
$iss= 'Manutenzione oggetti';
// PWD
$secret_pwd = 'XXXXXXXXXXXX';
?>
```

## Dipendenze

### Scaricate

- jquery scaricata (ultima versione stabile)
- fontawasome-free (ultima versione stabile)

### Usando composer

- Bootstrap versione 5.1.3 è stato scaricato nella sua versione compilata usando composer
- Stesso discorso per la libreria con le icone *bootstrap-icons*
- libreria per generare i codici a barre

```
composer require picqer/php-barcode-generator
composer require twbs/bootstrap:5.1.3 
composer require twbs/bootstrap-icons
composer require snapappointments/bootstrap-select
composer require firebase/php-jwt
```

Per installare l'applicazione è sufficiente lanciare un `composer install` nella cartella principale dlel'applicazione dove è contenuto il file `composer.json`.

Per fare update di versione è sufficiente un `composer update`

### Submodules

Si tratta di altri repository github che sono direttamente caricati dentro il repo:

Un esempio è la libreria bootstrap-table per realizzare grafici:

Con il comando ``git submodule``  si aggiunge il repository:

```
git submodule add https://github.com/wenzhixin/bootstrap-table.git vendor/bootstrap-table
```

Quindi si può aggiornare ad una specifica versione il submodule per aggiornare il repository (analogo del comando push).

```
git submodule update --remote vendor/bootstrap-table
cd vendor/bootstrap-table 
git checkout 1.20.2
```

Alla prima installazione

```
git submodule update --init --remote vendor/bootstrap-table
```

Per "scaricare" l'aggiornamento ai submodules sul proprio server è possibile fare un *sync*:

```
git submodule sync
```

Le dipendenze (al 2022-06-17) sono:

* https://github.com/snapappointments/bootstrap-select.git
* https://github.com/wenzhixin/bootstrap-table.git
