
**IUT La Rochelle**
**BUT INFO**
**R04.1**

---

**Developpement API**
**Developpement avec le framework symfony**

---

**Mise en place du projet**
**Branch Gitlab - Etape01**

---

## Nouveau projet sur Gitlab
- Créer un nouveau projet vide (sans readMe)
- project Name : `r4.01-devapi`
- Project slug : `2022-2023-butinfo2-r4.01-devapi`

## Le docker stack
- Sur moodle, se trouve l'archive : `r4.01-devapi-docker-stack.zip`
- Télécharger cette archive 
- Désarchiver cette archive
- Le contenu se trouve dans le dossier `r4.01-devapi-docker-stack`
- Vérifier

## Pousser la docker stack dans le projet gitlab
```bash
cd r4.01-devapi-docker-stack
git init
git remote add origin "https://forge.iut-larochelle.fr/rriole/2022-2023-butinfo2-r4.01-devapi"
git add .
git commit -m "Initial commit"
git push
//optionnel
git checkout -b etape01
```


## Démarrer la docker stack

```
docker compose up --build 
```

## Créer le projet sfapi

```bash
docker compose exec sfapi bash
composer install

composer create-project symfony/skeleton:"^5.4" sfapi
````

## Verifier 
http://localhost:8000

### Seance TP 26 janv

---

**Ajout du support des en-têtes CORS**

**Application Symfony `sfapi`**

---

**branch gitlab - etape01**

---

### Recette officielle et documentation
https://github.com/symfony/recipes/blob/flex/main/RECIPES.md

https://github.com/symfony/recipes/tree/main/nelmio/cors-bundle/1.5

### A faire

- Se connecter au conteneur du service `sfapi`
- puis :

```
cd sfapi
composer req cors
```

- consulter le fichier `sfapi/config/package/nelmio_cors.yaml``
- consulter le fichier `sfapi/.env`, et la vlauer de la env var `CORS_ALLOW_ORIGIN`

### Fin de l'étape et mise a jour du git


`git commit -m "fin install cros"`

#### Bilan

- Demarrage du projet : Ne pas refaire le squelette de symfony, se mettre sur la bonne branche

- Installer cors : Si ca ne fonctionne pas, faire composer install 


---

**Developpement API**
**Developpement avec le framework symfony**

---

**Mise en place du projet**
**Branch Gitlab - Etape02**

---

## Créer la branche `etape02`

```
git branch
git checkout -b etape02
git push --set-upstream origin etape02
```

## Modele du domaine

```plantuml
@startuml
package "Modèle du domain" #DDDDDD{
    class Auteur {
        + nom string
        + prenom string
    }
    class Livre {
        + titre string
        + annee integer
    }
    Auteur "1" -* "*" Libre
}
@enduml
```

## Créer les entités 

```
composer require doctrine/annotations
composer require orm
composer require symfony/maker-bundle --dev
php bin/console make:entity
```

## Lancer la migration de la BD

Modifier le .env par :  DATABASE_URL="mysql://api:api@database:3306/dbsfapi?serverVersion=10.10.2"

```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## PhpStorm : Database, connexion a la BD `dbsfapi`

```
user: api
password: api
databse: dbsfapi
port:3306
```
## Bilan

Repondre aux questions :
- Qu'est ce j'ai fait ? : J'ai mis en place une stack technique 

- Qu'est ce que j'ai appris ? La conjugaison du verbe faire (j'ai fait et non j'ai fais !)


---

**Developper une API à base du routage de Symfony**

---

**Application à l'entité `Auteur`**
**Branch Gitlab - Etape03-1**

---

## Objectf
-Comprendre et maîtriser le routage Sf
-Mettre en oeuvre le routage pour l'entité `Auteur`
-GET (*), GET (1) (select)
-POST (insert)
-DELETE (delete)


## Controleur `AuteurController`

mettre à jour les annotations dans composer.json : "doctrine/annotations" : "^1.0"

```
php bin/console make:controller AuteurController --no-template
http://localhost:8000/auteur
```

