**IUT La Rochelle — BUT Informatique — Module R4.01 : Développement d'API avec le framework Symfony**

---

## Étape 1 — Mise en place du projet

*Branche Gitlab : `etape01`*

### Nouveau projet sur Gitlab

- Créer un nouveau projet vide (sans README).
- Nom du projet : `r4.01-devapi`.
- Slug du projet : `2022-2023-butinfo2-r4.01-devapi`.

### La stack Docker

- L'archive `r4.01-devapi-docker-stack.zip` est disponible sur Moodle.
- Télécharger cette archive.
- Désarchiver cette archive.
- Le contenu se trouve dans le dossier `r4.01-devapi-docker-stack`.
- Vérifier le contenu.

### Pousser la stack Docker dans le projet Gitlab

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

### Démarrer la stack Docker

```
docker compose up --build 
```

### Créer le projet `sfapi`

```bash
docker compose exec sfapi bash
composer install

composer create-project symfony/skeleton:"^5.4" sfapi
```

### Vérifier l'installation

http://localhost:8000

*Séance du 26 janvier.*

### Ajout du support des en-têtes CORS

Application Symfony `sfapi`.

#### Recette officielle et documentation

https://github.com/symfony/recipes/blob/flex/main/RECIPES.md

https://github.com/symfony/recipes/tree/main/nelmio/cors-bundle/1.5

#### Mise en œuvre

- Se connecter au conteneur du service `sfapi`, puis exécuter :

```
cd sfapi
composer req cors
```

- Consulter le fichier `sfapi/config/package/nelmio_cors.yaml`.
- Consulter le fichier `sfapi/.env` et la valeur de la variable d'environnement `CORS_ALLOW_ORIGIN`.

#### Fin de l'étape et mise à jour du dépôt Git

`git commit -m "fin install cros"`

### Bilan

- Démarrage du projet : ne pas recréer le squelette Symfony, mais se positionner sur la branche Git appropriée.
- Installation de CORS : en cas de dysfonctionnement, exécuter `composer install`.

---

## Étape 2 — Modèle de domaine, entités et migration

*Branche Gitlab : `etape02`*

### Créer la branche `etape02`

```
git branch
git checkout -b etape02
git push --set-upstream origin etape02
```

### Modèle du domaine

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

### Créer les entités

```
composer require doctrine/annotations
composer require orm
composer require symfony/maker-bundle --dev
php bin/console make:entity
```

### Lancer la migration de la base de données

Modifier le fichier `.env` avec : `DATABASE_URL="mysql://api:api@database:3306/dbsfapi?serverVersion=10.10.2"`

```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### PhpStorm : connexion à la base de données `dbsfapi`

```
user: api
password: api
databse: dbsfapi
port:3306
```

### Bilan

Cette étape a permis de mettre en place la stack technique complète du projet : conteneurs Docker, application Symfony, modélisation du domaine (entités `Auteur` et `Livre`), génération des entités Doctrine, création et exécution de la migration de base de données, ainsi que la configuration de la connexion à la base `dbsfapi` depuis PhpStorm.

---

## Étape 3.1 — Routage de l'entité `Auteur`

*Branche Gitlab : `etape03-1`*

Développement d'une API à base du routage de Symfony, appliqué à l'entité `Auteur`.

### Objectifs

- Comprendre et maîtriser le routage Symfony.
- Mettre en œuvre le routage pour l'entité `Auteur` :
  - GET (liste complète), GET (par identifiant, sélection unitaire)
  - POST (insertion)
  - DELETE (suppression)

### Contrôleur `AuteurController`

Mettre à jour les annotations dans `composer.json` : `"doctrine/annotations" : "^1.0"`.

```
php bin/console make:controller AuteurController --no-template
http://localhost:8000/auteur
```

### Définir les routes API dans le contrôleur `AuteurController`

#### Route `/api/auteurs` (méthode GET)

```php

public function getAuteurs(AuteurRepository $auteurRepository ) : Response
{
    $auteurs = $auteurRepository->findAll();
    return new JsonResponse($auteurs,200,[],true);
    

}

```

Installation du composant de sérialisation :

```bash
composer require serializer
```

```php 

public function getAuteurs(AuteurRepository $auteurRepository, SerializerInterface $serializer ) : JsonResponse
{
    $auteurs = $auteurRepository->findAll();
    $auteursJson= $serializer->serialize($auteurs,'json');
    return new JsonResponse($auteurs,200,[],true);

}
```

#### Route `/api/auteurs/{id}` (méthode GET) — Version 1

```php 

public function getAuteurs(Request $request, SerializerInterface $serializer, AuteurRepository $auteurRepository ) : JsonResponse
{
        $auteur = $auteurRepository->findOneBy(array('id' => $request->get('id')));
        $auteurJson= $serializer->serialize($auteur,'json');
        return new JsonResponse($auteurJson,200,[],true);
}

```

#### Route `/api/auteurs/{id}` (méthode GET) — Version 2

```php 

public function getAuteurs(SerializerInterface $serializer, AuteurRepository $auteurRepository ) : JsonResponse
{
        $auteurJson= $serializer->serialize($auteur,'json');
        return new JsonResponse($auteurJson,200,[],true);
}

```

#### Route `/api/auteurs` (méthode POST)

```php

public function postAuteur(Request $request,SerializerInterface $serializer, AuteurRepository $auteurRepository ){

    $data = $request->getContent();
    $auteur = $serializer->deserializer($date,Auteur::class,'json');
    $repository->add($auteur,true);
    return new JsonResponse("",Response::HTTP_CREATED,[],true);
}

```

#### Route `/api/auteurs` (méthode DELETE)

```php 
public function deleteAuteur(Auteur $auteur, AuteurRepository $repository) : Response{

    $repository->remove($auteur,true);
    return new JsonResponse("",Response::HTTP_OK,[],true);
    }
```

### Bilan

- Compréhension et maîtrise du routage Symfony : techniquement, c'est l'annotation qui définit le routage.
- Mise en œuvre du routage pour l'entité `Auteur` :
  - GET
  - POST
  - DELETE
