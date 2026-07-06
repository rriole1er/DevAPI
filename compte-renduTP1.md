**IUT La Rochelle — BUT Informatique — Module R4.01 : Développement d'API avec le framework Symfony**

## Lancer la stack

Démarrer Docker :
```bash

git clone https://forge.iut-larochelle.fr/rriole/2022-2023-butinfo2-r4.01-devapi
docker compose up 
docker compose exec sfapi bash
cd sfapi
composer update
php bin/console doctrine:migrations:migrate
```
Charger les données de démonstration (issues de Moodle) via une requête SQL.

Vider le cache en cas de dysfonctionnement :
```
php bin/console cache:clear
php bin/console assets:install public
```

## PhpStorm : connexion à la base de données `dbsfapi`

```
user: api
password: api
databse: dbsfapi
port:3306
```

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


## Étape 3.2 — Routage de l'entité Livre

### Objectifs
- Comprendre et maîtriser le routage Symfony
- Mettre en œuvre le routage pour l'entité `Livre`
- GET (*), GET (1) (sélection)
- POST (insertion)
- DELETE (suppression)

### Contrôleur `LivreController`

```
php bin/console make:controller LivreController --no-template
```

### Jeu de test pour les livres
- Ajout des livres

- Route `/api/livres`, méthode GET
```php

public function getLivres(LivreRepository $livreRepository ) : Response
{
    $livres = $livreRepository->findAll();
    return new JsonResponse($livres,200,[],true);
    

}

```
- Groupes de sérialisation

```
    /**
     * @Route("/api/livres", name="app_livres_api",methods={"GET"})
     */
    public function getLivres(LivreRepository $livreRepository, SerializerInterface $serializer ) : Response
    {
        $livres = $livreRepository->findAll();
        $livresJson= $serializer->serialize($livres,'json',['groups'=>['liste_livres']]);
        return new JsonResponse($livresJson,200,[],true);

    }

```

- Test des routes :
```
http://localhost:8000/api/livres
http://localhost:8000/api/auteurs
```

- Une erreur se produit :
```ERROR
- 502 bad Gateway // C'est une erreur serveur WEB
```

### La route `/api/auteurs`, méthode GET — ajout de l'annotation @Groups pour les auteurs

- @Groups({"liste_livres","liste_auteurs"}) dans les attributs
- Sérialisation dans le contrôleur des auteurs
- La route `/api/auteurs/1` ne fonctionne pas

### La route `/api/auteurs/{id}`, méthode GET — ajout de l'annotation @Groups pour les auteurs

- Sérialisation dans le contrôleur des auteurs : `['groups' => ['liste_auteurs']]`

### La route `/api/livres/{id}`, méthode POST — ajout de l'annotation @Groups pour les auteurs

- La route `/api/livres/{id}` a été reprise de celle des auteurs, en adaptant les références (GET, POST, DELETE) à l'entité livres

- Les données transmises permettent de construire un auteur, mais celui-ci n'est pas destiné à être persisté en base de données : la sérialisation automatique ne fonctionne donc pas dans ce cas

- Il est par conséquent nécessaire de passer par une sérialisation manuelle afin de permettre la persistance des données

```php
public function postLivre(Request $request, LivreRepository $livreRepository, AuteurRepository $auteurRepository){

          $data = $request->toArray();
          $livre = new Livre();
          $livre->setTitre($data["titre"]);
          $livre->setAnnee($data["annee"]);
          $auteur = $auteurRepository->find($data["auteur"]["id"]);
          $livre->setAuteur($auteur);
          
          $livreRepository->add($livre, true);

        return new JsonResponse("",Response::HTTP_CREATED,[],true);
}
```

- Sur la route `localhost:8080/api/auteurs/2`, l'auteur possède 2 livres

### La route `/api/livres`, méthode DELETE

### BILAN

- Les contrôleurs présentent des similitudes, notamment une ressemblance entre les réponses HTTP des méthodes POST
- Les références entre attributs sont complexes ; il convient d'y prêter attention et d'utiliser les groupes de sérialisation pour éviter les références circulaires
- Une sérialisation manuelle est nécessaire pour garantir la persistance correcte des données en base

### Conclusion

- Réutiliser le code source des routes
- Il est possible de s'appuyer sur une bibliothèque permettant de créer les routes de l'API
- API Platform permet d'automatiser la création du routage de l'API ainsi que la création des entités, généralement en s'appuyant sur la spécification OpenAPI (openapis.org)


## Étape 4 — Découverte d'API Platform

### Pré-requis

1. Stack Docker démarrée
2. Conteneur `sfapi` démarré
3. Être positionné dans le terminal du conteneur (bash)

### Installation

```sh
composer require api
```

```
api-platforme/core
Aliases api api-plateform
```

Problème : l'installation nécessite le composant Symfony Serializer, on exécute donc :

```
composer require serializer
```

**Commande :**
```
composer recipes api-platform/core
```

**Résultat :**
```
name             : api-platform/core
version          : 2.5
status           : up to date
installed recipe : https://github.com/symfony/recipes/tree/1aa7d46/api-platform/core/2.5
files            : 

├──config
│  ├──packages
│  │  └──api_platform.yaml
│  └──routes
│     └──api_platform.yaml
└──src
   └──Entity
      └──.gitignore

```

On teste l'accès à l'API :

```
http://localhost:8000/api
```

### Transformation de l'entité Auteur

- On supprime l'attribut `livres` ainsi que son getteur et son setteur de l'entité `Auteur`.
- On supprime l'annotation `@Groups`.

Dans l'en-tête de l'entité `Auteur`, on ajoute l'annotation `@ApiResource()` et on inclut :

```php
use ApiPlatform\Core\Annotation\ApiResource;
```

### En cas de non prise en compte des modifications

Exécuter :

```
php bin/console cache:clear
php bin/console assets:install public
```

### Documentation de l'API avec Swagger

Elle doit être conforme à la spécification OpenAPI.

Pour consulter un auteur en particulier :

```
http://localhost:8000/api/auteurs/34.jsonld
```

Affiche l'arborescence des routes disponibles :

```
php bin/console debug:router
```

### Utilisation du Profiler dans une API

Installation :

```sh

composer require profiler --dev
composer require debug --dev
```

### Retirer l'opération de suppression (delete) de l'API

- Fichier concerné : `App/Entity/Auteur`
```php
/**
 * @ApiResource(
 *     collectionOperations={"get","post"},
 *      itemOperations={"get","put","patch"},
 *      shortName="authors"
 * )
 * @ORM\Entity(repositoryClass=AuteurRepository::class)
 */


```

### Getter et setter associés à un attribut

Ajout d'un nouvel attribut à l'entité `Auteur`.

Dans l'entité `Auteur`, on ajoute un attribut :
- nom de l'attribut : `createAdt`
- type : `datetime_immutable`
- il peut être `null`
- on peut exécuter la migration si l'on souhaite visualiser les données

```php
 php bin/console make:migration
 php bin/console doctrine:migrations:migrate
```

### La date de création d'un auteur ne doit pas être modifiable

- Le champ `createAdt` doit apparaître en sortie, mais le client ne doit pas pouvoir le modifier.
- Il faut donc interdire l'écriture sur le champ `createAdt`.
- Localiser la méthode `setCreateAdt()` et la supprimer.

On ajoute le constructeur suivant :

```php
public function __construct(){

$this->createAdt = new \DateTimeImmutable();
}
```

### Personnaliser le champ createAdt

- En plus du champ `createAdt`, qui est dans un format standard mais peu lisible, nous souhaitons également renvoyer la date sous forme de chaîne de caractères, par exemple sous la forme « il y a 5 minutes ».
- On installe la bibliothèque suivante :

```php 
composer require nesbot/carbon
```

- Juste en dessous de la méthode `getCreateAdt()`, on ajoute la méthode suivante :

```php 
public function getCreatedAdtAgo() : string{
return Carbon::instance($this->getCreatedAdt())->diffForHumans();
}

```

API Platform n'a pas besoin d'un attribut propre : il lui suffit d'un getter et/ou d'un setter pour l'exposer.

### Ajouter un groupe de sérialisation de normalisation

Rappel : la normalisation consiste à transformer un objet en tableau (object → array).

```php 

/**
 * @ApiResource(
 *     collectionOperations={"get","post"},
 *      itemOperations={"get","put","patch"},
 *      shortName="authors",
 *      normalizationContext={"groups"={"auteurs:read"}}
 * )
 * @ORM\Entity(repositoryClass=AuteurRepository::class)
 */
```

- La propriété `groups` de `normalizationContext` définit le nom du groupe utilisé lors de la lecture des attributs de l'objet, afin de les restituer sous forme de tableau.
- Nous avons ajouté le suffixe `read` au nom de ce groupe pour rappeler qu'il est associé à l'opération de lecture.
- On ajoute l'annotation `@Groups` aux attributs de l'entité `Auteur` à exposer en lecture :

```php
 @Groups({"auteurs:read"})
 ```

### Ajouter un groupe de sérialisation de dénormalisation

Rappel : la dénormalisation consiste à transformer un tableau en objet (array → object).

```php 

/**
 * @ApiResource(
 *     collectionOperations={"get","post"},
 *      itemOperations={"get","put","patch"},
 *      shortName="authors",
 *      normalizationContext={"groups"={"auteurs:read"}},
 *      denormalizationContext={"groups"={"auteurs:write"}}
 * )
 * @ORM\Entity(repositoryClass=AuteurRepository::class)
 */
```

On ajoute l'annotation `@Groups` aux attributs de l'entité `Auteur` à autoriser en écriture :

```php
@Groups({"auteurs:read","auteurs:write"})
```

L'affichage en lecture et en écriture est donc piloté par les groupes de sérialisation.

- On remarque que la propriété `createdAdtAgo` a disparu de la sortie ; pour la réintégrer, on lui ajoute l'annotation `@Groups` :

```php 
    /**
     * Retournes la date de création sous un format lisible
     * @Groups({"auteurs:read"})
     * @return string
     */
    public function getCreatedAdtAgo() : string{

        return Carbon::instance($this->getCreateAdt())->diffForHumans();
    }
```

### Ajout de l'attribut biographie à l'entité Auteur

Mise à jour de l'entité :

```php 
php bin/console make:entity Auteur
- nom : biographie
- type : string
- taille :200
- null : 

php bin/console make:migration
php bon/console doctrine:migrations:migrate
```

Supposons à présent que l'on souhaite ajouter un setteur additionnel pour cet attribut, qui transforme le texte brut (avec des retours à la ligne `\n`) en texte HTML. On peut écrire ce setteur comme suit :

```php
    public function setTextBiographie(?string $biographie): self
    {
        $this->biographie = nl2br($biographie);

        return $this;
    }
```

### Objectif

- La lecture de la ressource `Auteur` doit exposer l'attribut `biographie`.
- L'écriture de la ressource `Auteur` doit accepter un attribut nommé `textBiographie`.

Solution : sérialisation.

```php


class Auteur
{
    /**
     * Biographie text html
     * @Groups({"auteurs:read"})
     */
    private $biographie;

    /**
     * Biographie text html
     * @Groups({"auteurs:write"})
     */
    public function setTextBiographie(?string $biographie): self
    {
        $this->biographie = nl2br($biographie);

        return $this;
    }
}

```

### Problème

- Si les deux noms pouvaient être identiques, ce serait plus simple pour les utilisateurs de l'API.

### Comment maîtriser le nommage des champs

Objectif : nommer le champ lié à la biographie d'un auteur `biographie`, aussi bien en lecture qu'en écriture, à l'aide de `@SerializedName`.

```php 
    /**
     * Biographie text html
     * @Groups({"auteurs:write"})
     * @SerializedName ("biographie")
     */
    public function setTextBiographie(?string $biographie): self
    {
        $this->biographie = nl2br($biographie);

        return $this;
    }
```

### Contexte

Nous savons que le sérialiseur fonctionne en appelant des méthodes getter et setter, ou en utilisant des propriétés publiques ou d'autres éléments comme des méthodes `has*`/`is*` existantes.

Mais que se passe-t-il si l'on souhaite donner un constructeur à la classe `Auteur` ?

### Problème et solution

Étant donné que l'entité `Auteur` nécessite obligatoirement un nom et un prénom, il est pertinent de fournir ces deux attributs au constructeur de cette classe.
Par conséquent, nous n'avons vraisemblablement plus besoin des setteurs `setNom` et `setPrenom`.

D'un point de vue orienté objet, cela rend les propriétés `nom` et `prenom` immuables.

Solution :

```php 

    /*
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
    */
    
    /*
    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }
    
    */
    
        public function __construct(string $nom, string $prenom){

        $this->name = $name;
        $this->prenom = $prenom;
        $this->createAdt = new \DateTimeImmutable();
    }
```

### Quelle conséquence pour le sérialiseur ?

API Platform se base sur le nom des attributs : pour faire correspondre le nom de l'attribut du constructeur et celui de la base de données, il faut utiliser le même nom.

Il faut donc veiller à bien respecter la cohérence des noms.

### Les arguments passés au constructeur peuvent altérer la validation des données

Mais il y a un cas limite.

Imaginons que nous créions un nouvel auteur et que nous oubliions d'envoyer le nom et/ou le prénom : cela provoquera un dysfonctionnement.

Pour éviter cela, on s'appuie sur la validation en rendant les paramètres du constructeur nullables, afin que l'API continue de fonctionner.

```php

    public function __construct(string $name = null, string $prenom = null){

        $this->name = $name;
        $this->prenom = $prenom;
        $this->createAdt = new \DateTimeImmutable();
    }

```

Une erreur 500 correspond à une erreur de la base de données plutôt qu'à une erreur de l'API, si l'on tente d'enregistrer un auteur vide.

### Contexte

Nous disposons d'une ressource `Auteur` et d'une ressource `Livre`. Établissons une relation entre elles :

- un auteur peut être associé à plusieurs livres ;
- un livre est associé à un auteur.

### Mise à jour de l'entité Livre : @ApiResource

On retire l'attribut `Groups` de l'entité `Livre` ainsi que le getter et le setter de `Auteur`.

```php
/**
 * ApiResource()
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 */

```

Ajout d'une relation `ManyToOne` vers `Auteur` dans l'attribut de l'entité `Livre` :

```php
bin/console make:migration
bin/console doctrine:mirations:migrate
```

On vérifie le getter : cela fonctionne.

## Relations et IRIs

### Contexte

Si l'on essaie de créer un `Livre` en définissant la propriété `auteur` avec la valeur `1` (l'identifiant d'un auteur réel en base de données), cela ne fonctionne pas !

- Pourquoi ? Parce qu'avec API Platform, et plus généralement dans le développement d'API moderne, on n'utilise pas d'identifiants pour faire référence aux ressources : on utilise des IRIs.
- Lorsqu'on exécute la route `GET /api/livres` :

on obtient une URL dans la réponse JSON,

et si l'on essaie la route `GET /api/livres/{1}`, on obtient une erreur Bad Request.

C'est pourquoi Swagger documente l'attribut comme une chaîne de caractères (`string`), ce qui n'est pas totalement exact.
De prime abord, l'auteur est en effet une chaîne de caractères, et c'est ce que Swagger indique dans le modèle `Livre.livre.Write`.

Mais nous savons que cette valeur est spéciale : elle représente un lien.

### Conclusion

Une relation n'est qu'une propriété normale, à ceci près qu'elle est représentée dans l'API par son IRI.

### API Platform côté entité Auteur

- Actuellement, si l'on exécute la route `GET /api/auteurs`, l'API renvoie toutes les données de l'auteur, à l'exception de la liste des livres qui lui sont associés.

Mise à jour `Auteur` :

```php 

    public function getCreatedAdtAgo() : ?string{

        if($this->getCreateAdt())
            return Carbon::instance($this->getCreateAdt())->diffForHumans();
        return null;
    }
```

### Retourner les données des livres dans l'API Auteurs

Solution : annoter la classe `Livre` :

```php 
     * @Groups({"livres:read","livres:write","auteurs:read"})
```

### Conclusion

Le sérialiseur sait sérialiser tous les champs du groupe `auteurs:read`.
Il examine d'abord toutes les données de `Auteur` qui appartiennent à ce groupe, puis parcourt les ressources associées pour retourner, à leur tour, les données annotées avec ce même groupe.

### Retourner les données des auteurs dans l'API Livres

```php 
@Groups({"auteurs:read","auteurs:write","livres:read"})
```

### Embarquer les données de ressources avec restriction sur l'opération

Nous sommes parvenus à embarquer les données de nos ressources et à retourner des données explicites, sans IRIs, avec l'opération de collection GET.

Nous souhaitons intégrer les données d'un auteur lorsque l'on récupère un seul livre, mais la réponse risque alors d'être considérablement volumineuse.

### Solution

Dans `Livre` :

```php
/**
 * @ApiResource(
 *     itemOperations={
 *     "get"={
 *     "normalization_context"={"groups"={"livres:read","livres:item:get"}},
 *     },
 *     "delete"={}
 *     },
 *   normalizationContext={"groups"={"livres:read"}},
 *     denormalizationContext={"groups"={"livres:write"}}
 * )
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 */
```

Dans `Auteur` :

```php 
* @Groups({"auteurs:read","auteurs:write","livres:item:get"})
```

Dans `Auteur` :

```php 

/**
 * @ApiResource(
 *     collectionOperations={"get","post"},
 *      *     itemOperations={
 *     "get"={
 *     "normalization_context"={"groups"={"auteurs:read","auteurs:item:get"}},
 *     },
 *     "delete"={}
 *     },
 *      shortName="authors",
 *      normalizationContext={"groups"={"auteurs:read"}},
 *     denormalizationContext={"groups"={"auteurs:write"}}
 * )
 * @ORM\Entity(repositoryClass=AuteurRepository::class)
 */

```

Dans `Livre` :

```php 

@Groups({"livres:read","livres:write","auteurs:item:get"})

```

## Validation

### Prérequis pour la validation

1) Le constructeur de l'entité `Auteur` n'a pas de paramètres :

```php 
    public function __construct(){
        
        $this->createAdt = new \DateTimeImmutable();
        $this->livres = new ArrayCollection();
    }
```

2) On remet les setteurs sur `name` et `prenom`.

### Contexte

Un client de l'API peut envoyer des données invalides de différentes manières :

- il peut envoyer du JSON malformé ;
- ou omettre de renseigner un champ tel que `name` ou `prenom` ;
- ou envoyer des données vides.

Le rôle de notre API est de répondre à ces situations de façon cohérente, afin que les erreurs soient facilement compréhensibles.

### Traitement d'un JSON invalide

C'est l'un des domaines dans lesquels API Platform excelle particulièrement.

Si l'on envoie un JSON invalide, on reçoit une erreur 400 de type `hydra:error`.

API Platform gère ainsi nativement les problèmes liés à la syntaxe.

### Validation d'attribut

Si l'on envoie uniquement `{}`, on obtient une erreur 500 (Internal Server Error) :

```json 
  "hydra:description": "An exception occurred while executing a query: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'name' cannot be null",
```

En effet, API Platform tente d'enregistrer un auteur vide, mais la base de données rejette l'opération au moment de la persistance.

### Conclusion

Dès lors que Symfony renvoie une erreur 500, cela signifie qu'il nous revient de définir et de contrôler précisément les règles de validation pour chaque attribut.

### Validation des attributs

Règles métier pour les attributs de l'entité `Auteur` :

- le nom d'un auteur ne doit être ni `null` ni vide ;
- le prénom d'un auteur ne doit être ni `null` ni vide ;
- la biographie d'un auteur ne doit être ni `null` ni vide :
  - longueur minimale : 10 caractères ;
  - longueur maximale : 2000 caractères ;
  - un message doit être renvoyé en cas d'échec de validation.

```php 
    /**
     * Biographie text html
     * @ORM\Column(type="string", length=2000, nullable=true)
     * @Groups({"auteurs:read"})
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 10,
     *     max = 2000,
     *     maxMessage="La biographie est trop longue -2000 car"
     * )
     */
     private $biographie;
```

Ajoutons un message personnalisé :

```php 
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"auteurs:read","auteurs:write","livres:item:get"})
     * @Assert\NotBlank(
     *     message = "Le nom de l'auteur, ne peut pas être nul comme Jarod"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"auteurs:read","auteurs:write","livres:item:get"})
     * @Assert\NotBlank(
     *     message = "Le prenom de l'auteur, ne peut pas être nul comme Jarod"
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"auteurs:read","auteurs:write"})
     */
    private $createAdt;

    /**
     * Biographie text html
     * @ORM\Column(type="string", length=2000, nullable=true)
     * @Groups({"auteurs:read"})
     * @Assert\NotBlank(
     *     message = "La biographie de l'auteur, ne peut pas être nul comme Jarod"
     * )
     * @Assert\Length(
     *     min = 10,
     *     max = 2000,
     *     maxMessage="La biographie est trop longue -2000 car"
     * )
     */
```

### Conclusion

En tant que développeurs d'API, la seule chose dont nous devons nous charger, ce sont les règles métier.
Le reste est pris en charge par API Platform.

## Relations imbriquées

### Mettre à jour un attribut d'une relation imbriquée

Résultat :

La raison pour laquelle le nom d'un auteur est intégré lors de la sérialisation d'un livre est que nous avons ajouté, au-dessus du nom de l'auteur, le groupe `livres:item:get`, qui est l'un des groupes utilisés par l'opération GET.

```php 
 @Groups({"auteurs:read","auteurs:write","livres:item:get","livres:write"})
```

### Envoyer de nouveaux objets ou des références à des objets existants

Nous obtenons l'erreur suivante :
```
A new entity was gound trhough the relationship Livre#Auteurs tjat was not configured to cascade persist operations for entity
```

Cela signifie que quelque chose a créé un nouvel objet et l'a affecté à la propriété `Livre#Auteur` ; il faut donc mettre à jour une entité existante plutôt que d'en créer une nouvelle.

Si l'on souhaite modifier une entité existante, il faut renseigner son `@id`.

### L'attribut de type collection est modifiable

```php
class Auteur

    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur", orphanRemoval=true)
     * @Groups({"auteurs:read","auteurs:write"})
     */
    private $livres;
```

### Créer de nouveaux éléments via l'attribut d'une collection

Pour l'instant, seuls des IRIs peuvent être fournis pour cet attribut, et non un tableau JSON complet.

### Gérer la persistance

```php 
    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur", orphanRemoval=true, cascade={"persist"})
     * @Groups({"auteurs:read","auteurs:write"})
     */
    private $livres;
```

Cependant, les contraintes de validation ne sont pas propagées aux entités enfants ; sans cela, on pourrait envoyer des livres vides.

On ajoute alors la contrainte suivante :

```php 
    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur", orphanRemoval=true, cascade={"persist"})
     * @Groups({"auteurs:read","auteurs:write"})
     * Assert\Valid()
     */
    private $livres;
```
