# Versions du projet theArtbox

### Feat_BDD 0.2.2 Reorganisation des fichiers

Creation du dossier assets pour les fichiers statiques (css, js, images)
Deplacement du dossier vendor dans le dossier includes
Modification config pour une utilisation plus dynamique des chemins (path_img, url_img, etc.)
Mise a jout du gitignore pour les nouveaux chemins

### Feat_BDD 0.2.1 Ajout js UX admin

Confirmation de suppression / edition / ajout d'oeuvre
Affichage dynamique des images importees
Modification class Template pour permettre l'ajout de scripts (via nommage js_SCRIPTNAME)

### Feat_BDD 0.2.0 Ajout formulaire d'ajout / edition / suppression d'oeuvre

Ajout formulaire d'ajout / edition / suppression d'oeuvre
Ajout d'une option "return" a Oeuvre::fetch, not. pour Count, methode plus performante pour recuperer le nombre de resultats
Ajout d'une methode "delete" a Oeuvre
Maj du style css pour affichage du formulaire
Nb max de résultats configurable dans config.php
Ajout d'une todo list dans le fichier TODO.md
Correction typos mineures

### Feat_BDD 0.1.0 Implemetation de la classe Oeuvre dans le site

Ajout de la classe Oeuvre dans le site.
Ajout d'une classe Template pour acceder plus facilement aux templates.
Reorganisation des fichiers du site.
Suppression du dossier 'old'

### Feat_BDD 0.0.7 Ajout methode to_array et to_array_multiple Oeuvre

Ajout methode to_array et to_array_multiple a la classe Oeuvre pour obtenir un tableau associatif des champs de l'objet.

### Feat_BDD 0.0.6 Ajout methode save, insert et update Oeuvre

La methode update choisir entre INSERT et UPDATE selon si l'objet est deja en base ou non.
L'objet est toujours hydrate avant un update.

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