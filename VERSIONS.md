# Versions du projet theArtbox



### Feat_BDD 0.0.5 Ajout methode hydrate Oeuvre

Ajout methode hydrate a la classe Oeuvre.
Oeuvres retournees par fetch ne plus plus hydratees.
Tenter de lire un champ null d'un objet non hydrate hydrate l'objet (si l'id est defini).

### Feat_BDD 0.0.4 Objets BDD & Oeuvre

Ajout classe BDD avec connexion auto PDO
Ajout classe Oeuvre avec methodes new et fetch
Ajout tests Oeuvre
Modification config pour permettre overrides en tests
Ajout Autoload pour classes includes/*

### Feat_BDD 0.0.3 Ajout du setup de tests phpunit

Ajout des librairies phpunit via composer
Ajout du dossier tests/
Ajout du fichier phpunit.xml

### Feat_BDD 0.0.2 Ajout base de donnee et lignes d'oeuvres

Cfg.php modifie pour obtenir les informations de connexion a la base de donnee.

### Feat_BDD 0.0.1

Ajout du formualaire d'ajout d'oeuvre.

## 1.0.0 FIX chemins configuration

Logique d'obtention des chemins root et urlRoot modifiees. Le serveur doit être situé dans un sous-dossier theArtbox ou a la racine du serveur.