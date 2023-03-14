
**IUT La Rochelle**
**BUT INFO**
**R04.1**

## Lancer la stack

Lancer docker 
```bash

git clone https://forge.iut-larochelle.fr/rriole/2022-2023-butinfo2-r4.01-devapi
docker compose up 
docker compose exec sfapi bash
cd sfapi
composer update
php bin/console doctrine:migrations:migrate
```
Lancer les datas de moodle dans query sql

Vider le cache quand ca marche pas
```
php bin/console cache:clear
php bin/console assets:install public
```

## PhpStorm : Database, connexion a la BD `dbsfapi`

```
user: api
password: api
databse: dbsfapi
port:3306
```



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

## Definir les routes API dans le controler `AuteurController`

### La route `/api/auteurs`, methode=GET

- Route `/api/auteurs`
```php

public function getAuteurs(AuteurRepository $auteurRepository ) : Response
{
    $auteurs = $auteurRepository->findAll();
    return new JsonResponse($auteurs,200,[],true);
    

}

```

- On installe la sérialisation 
```bash
composer require serializer
```
livre
```php 

public function getAuteurs(AuteurRepository $auteurRepository, SerializerInterface $serializer ) : JsonResponse
{
    $auteurs = $auteurRepository->findAll();
    $auteursJson= $serializer->serialize($auteurs,'json');
    return new JsonResponse($auteurs,200,[],true);

}
```

### La route `/api/auteurs/{id}`; methode=GET           // Version 1
    ```php 
    
    public function getAuteurs(Request $request, SerializerInterface $serializer, AuteurRepository $auteurRepository ) : JsonResponse
    {
            $auteur = $auteurRepository->findOneBy(array('id' => $request->get('id')));
            $auteurJson= $serializer->serialize($auteur,'json');
            return new JsonResponse($auteurJson,200,[],true);
    }
    
    ```


### La route `/api/auteurs/{id}`; methode=GET           // Version 2
    ```php 
    
    public function getAuteurs(SerializerInterface $serializer, AuteurRepository $auteurRepository ) : JsonResponse
    {
            $auteurJson= $serializer->serialize($auteur,'json');
            return new JsonResponse($auteurJson,200,[],true);
    }
    
    ```

### La route `/api/auteurs`

```php

public function postAuteur(Request $request,SerializerInterface $serializer, AuteurRepository $auteurRepository ){

    $data = $request->getContent();
    $auteur = $serializer->deserializer($date,Auteur::class,'json');
    $repository->add($auteur,true);
    return new JsonResponse("",Response::HTTP_CREATED,[],true);
}

```

### La route `/api/auteurs`, methods=DELETE

```php 
public function deleteAuteur(Auteur $auteur, AuteurRepository $repository) : Response{

    $repository->remove($auteur,true);
    return new JsonResponse("",Response::HTTP_OK,[],true);
    }
```

## BILAN 

- Comprendre et maitrise le routage SF
- -techniquement c'est lannotation qui fait le routage
  - Mettre en oeuvre le routage ppur l'entité `Auteur`
    - GET
    - POST
    - DELETE


---

**Developper une API à base du routage de Symfony**

---

**Application à l'entité `Livre`**
**Branch Gitlab - Etape03-2**

---

## Objectf
-Comprendre et maîtriser le routage Sf
-Mettre en oeuvre le routage pour l'entité `Livre`
-GET (*), GET (1) (select)
-POST (insert)
-DELETE (delete)


## Controleur `LivreController`

```
php bin/console make:controller LivreController --no-template
```

## Jeu de test pour les livres
- Ajouter les livres

- Route `/api/livres methode=GET`
```php

public function getLivres(LivreRepository $livreRepository ) : Response
{
    $livres = $livreRepository->findAll();
    return new JsonResponse($livres,200,[],true);
    

}

```
- serializer groups

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

- On test les routes : 
```
http://localhost:8000/api/livres
http://localhost:8000/api/auteurs
```

- Une erreur se produit
```ERROR
- 502 bad Gateway // C'est une erreur serveur WEB
```


## La route `api/auteurs`, methode=GET, ajout de l'annotation @Groups pour les auteurs

- @Groups({"liste_livres","liste_auteurs"}) dans les attributs
- Serializez dans le controleur auteurs
- La route api/auteurs/1 ne fonctionne pas 


## La route `/api/auteurs/{id}`, methode=GET, ajout de l'annotation @Groups pour les auteurs

- Serialize dans le contrleur auteurs ['groups' => ['liste_auteurs']]


## La route `/api/livres/{id}`, methode=POST, ajout de l'annotation @Groups pour les auteurs

- On copie la route /api/livres/{id} de auteur et remplace par livres (get, post, delete)

- On passe de quoi construire un auteur mais pas qu'on voudrait le persister dans la BD donc ca ne marche pas

- Il faut donc passer par une séréalisation manuelle pour faire persister la data

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

- Dans la route localhost:8080/api/auteurs/2, il possede 2 livres

## La route `/api/livres`, methode = DELETE

## BILAN 

- Les controlleur sont similaires, ressemblance entres les POST reponse HTTP
- Les references entre attributs sont complexes, il faut faire attention a ne pas se tromper, utiliser les groups pour eviter les references circulaires,
- Serializer à la main pour bien faire persister dans la base de donnée 

## Conclusion 

- reutiliser le code souces des routes
- On peut avoir une bibliothèque permettant de crér le routes de l'API
- API plateform, automatiser la création du routage donc API, création d'entités et généralement le OpenAPis (openapis.org)


**R4.01 Architecture Logicielle - Developpement d'API**

---

**API Plateform**
**Installation**
**Application API**

---

## Pré-requis

1. Stack docker demarrée
2. conteneur sfapi démaré
3. être dans le bash

## Installlation 
```sh
composer require api
```

```
api-platforme/core
Aliases api api-plateform
```

Probleme : 
  l'install exige le symofny serializer donc on fait :

```
composer require serializer
```

** commande : **
```
composer recipes api-platform/core
```

** Résultat **
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

On essaye 

```
http://localhost:8000/api
```


## Transformation de l'entité Àuteur`

- On supprime l'attribut `livres` et ses getteur et setteur de l'entité `Auteurs`
- On suppprime l'annotation `@Groups`

Dans l'entete de l'entité Auteurs rajouter `@ApiResource()` et inclure

```php
use ApiPlatform\Core\Annotation\ApiResource;
```

## Si rien ne change il faut faire :

```
php bin/console cache:clear
php bin/console assets:install public
```

## Swagger : documentation d'une api 

Doit être conforme avec OpenAPI

Si on veut choisir un auteur en particulier :

```
http://localhost:8000/api/auteurs/34.jsonld
```

Affiche une arborescence

```
php bin/console debug:router
```

## Utilisation du profiler dans une API

## Installation

```sh

composer require profiler --dev
composer require debug --dev
```

## Enlever la fonction delete dans l'API

- Route `App/Entity/Auteur`
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

## Application : getter et setter asspcoés à un attrivut

Ajoute d'un nouvel attribut de l'entité Auteur

Dans l'entité Auteur, on ajoute un attribut :
- nom attribut : createAdt
- type : datetime_immutable
- il peut être null
- on peut envoer la migration si on souhaite voir les données

```php
 php bin/console make:migration
 php bin/console doctrine:migrations:migrate
```

## La date de création d'un auteur ne doit pas être modifiable 

- Avoir un champ createAdt sur la sortie n'est pas interessant, le client ne doit pas pouvoir modifier

- Donc, on doit interdite l'entrée dans le champ createAdt

- Trouver la methode setCreateAdt() et supprimez la

on ajoute 

```php
public function __construct(){

$this->createAdt = new \DateTimeImmutable();
}
```

## Personnaliser le champ createdAdt

- Disons qu'en plus du champ createAdt
- Qui est dans un format un peu laid mais standard
- Nous voulons également renvoyer la date sous dorme de chaîne
- quelque chose come il y a 5 minutes
- On installe : 

```php 
composer require nesbot/carbon
```

- Juste en dessous de la fontion getCreateAdt() on ajoute la fonction suivante :

```php 
public function getCreatedAdtAgo() : string{
return Carbon::instance($this->getCreatedAdt())->diffForHumans();
}

```

APIPlateform n'a pas besoin d'attribut, il prend juste un getter et/ou un setter pour l'afficher 

## Ajouter un groupe de sérialisation de normalisaiton

Rappel : normalisation = object TO array

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

- La propriété groups dans normalizationContext définit le nom du groupe à l'opération lecture des attributs de notre object pour les fournir à un array

- Nous avons ajouté un tag : `read` au nom de ce groupe pour rappeler que ce groupe ezst associer a l'oépration de lecture

- On ajoute groups aux attributs auteur pour lire

```php
 @Groups({"auteurs:read"})
 ```

## Ajouter un groupe de sérialisation de denormalisaiton

Rappel : denormalisation =  array TO object

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


On ajoute groups aux attrivuts auteur pour ecrire

```php
@Groups({"auteurs:read","auteurs:write"})
```


affichage en lecture / ecriture ---> groups

- On remarque que la propriété createdAdtAgo a disparu, pour l'ajouter on lui donne l'annotation de Groups

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

## 1. Modification de l'entité Auteur

# 1.1 Ajout de l'attribut biographie

Mise à jour de l'entité

```php 
php bin/console make:entity Auteur
- nom : biographie
- type : string
- taille :200
- null : 

php bin/console make:migration
php bon/console doctrine:migrations:migrate
```

Supposons maintenant, qu'un souhaite ajouter un setteur additionnel pour cet attribut qui transforme le texte brute, avec des retours de ligne '\n' en un text html par exemple. On peut ecrire ce setteur comme suit :


```php
    public function setTextBiographie(?string $biographie): self
    {
        $this->biographie = nl2br($biographie);

        return $this;
    }
```

## Objectif

- La lecture de la ressource Auteur doit fournir l'attribut biographie

- L'écriture de la ressource Auteur doit fournir un attribut qui s'appelle textBiographie

Solution : serialisation 

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


## Probleme 

- Si ca pouvait etre identiques ca serait simple pour les utilisateurs de l'API

## Comment controller le nomage des champs

Object : appeler notre champ lié à la biographie d'un auteru comme biographique en lecture ET en écriture

@SerializedName

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


## Contexte 

Nous savons que le sérialiseur aime travailler e appelant des méthodes getter et setter, ou en utilisant des propriétés publiques ou quelques choses autres comme les méthodes hasser ou isser

Mais que se passe-t-il on souhaite donner un constructeur à la classe Auteur ?

## Probleme et solution

Parce que chaque fois que notre entité Auteur a besoin obligatoirement d'un nom et prénom, je pense que c'est une bonnée idée de forunir ces deux attributs au construteur de cette classe.
Par conséquent, nous avons très problablement plus besoin des setteurs setNom et setPrenom

D'un point de vue orienté objet, cela rend les propriétés  nom et prenom immuable

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


## Quel conséquence pour serializer 

API Plateform se fit en fonction du noms des attributs, pour lier le nom de l'attribut dans le constructeur et la base de donnée il faut le meme nom.

Il faut faire attention a bien respecter les noms.

## Les arguments passés au constructeur peuvent altérer la validaiton des données

Mais il y a un cas limite.

Imaginiez que nous créeons un nouveau auteru et que nous oublions d'envoyer entierement le nom et ou prenom, ca fera de la d

Si on veut eviter cela, on doit se fier a la validation, on met les parametre en null.

Pour que l'api puisse tourner.

```php

    public function __construct(string $name = null, string $prenom = null){

        $this->name = $name;
        $this->prenom = $prenom;
        $this->createAdt = new \DateTimeImmutable();
    }

```

Une erreur 500, c'est une erreur de la base de donnée plus de l'api si on integre un auteur null.



## Contexte

Nous avons une ressource Auteur et une ressource Livre

Relions les ensemble 

un auteur peut etre associé à plusieurs livres
un livre est associé à un auteur

## Mise a jour de l'entité Livre : @ApiResource


On enleve l'attributs Groups de livre et les getter et setter Auteurs

```php
/**
 * ApiResource()
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 */

```

ajout d'une relation ManyToOne auteur dans attribut Livres

```php
bin/console make:migration
bin/console doctrine:mirations:migrate
```

on verifie le getter et ca marche


---

**API Plateform**
**Relation et IRIs**

---

## Contexte

Si on essaie de créer un `Livre` en définissant la propriété `auteur` sur 1 : l'identifiant d'un auteur réel dans la base de données
alors cela ne fonctionne pas !


- Pourquoi ? Parce que API-Plateforme et dans le developpement d'api moderne ne général, nous n'utilisons pas
d'identifiants pour faire réference à dees ressources. nous utilisons des IRIs

- Lorsqu'on execute la route `GET /api/livres`:

on obtient une url en reponse JSON 

et si on essaie la route `GET /api/livres/{1}` : Bad Request

C'est pourquoi Swagger documente l'attribut comme un "string" ... ce qui n'est pas totalement exact.
Bien sur, à premiere vue, l'auteur est un string... et c'est ce que Swagger montre dans le modèles Livres.livre.Write

Mais nous savons que c'est valeur est spéciale : elle represente un lien

# Conclusion : 

Une relation n'est qu'un propriété normale, sauf qu'elle est représentée dans l'API avec son IRI

## API- PLATEFORME côté entité `Auteur`

- Actuellement, si on exécute la route `GET /api/auteurs`, l'API renvoie toutes les données d'un auteur sauf a liste des livres associée à l'auteur

Mise à jour `Auteur` :

```php 

    public function getCreatedAdtAgo() : ?string{

        if($this->getCreateAdt())
            return Carbon::instance($this->getCreateAdt())->diffForHumans();
        return null;
    }
```


## Retourner les données des livres dans l'API Auteurs

Solution

Annoter dans la classe livre :

````php 
     * @Groups({"livres:read","livres:write","auteurs:read"})
````

# Conclusion

Le sérialiseur sait sérialiser tous les champs du grupe `auteurs:read`.
Il regarde d'abord toutes les données de `Auteur` qui font partie de ce groupe. Ensuite, il continue dans les autres ressource associées pour parcourir ce meme groupe et retourner les données annotées

## Retourner les données des auteurs dans l' API Livres

```php 
@Groups({"auteurs:read","auteurs:write","livres:read"})
```

## Embarquer les données de ressources avec restriction sur l'opération

Nous avons reussi à embarquer les données de nos ressource, et nous avons réuisi à reoutrner les données explicites, sans IRIs, avec l'opération de collection GET

On veut intégrer les données d'un auteur lorsque je récupère un suel livre, mais la reponse va etre gigantesque.

# Solution

Dans `Livre`

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

Dans `Auteurs`

```php 
* @Groups({"auteurs:read","auteurs:write","livres:item:get"})
```

Dans `Auteurs`

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

Dans `Livres`

````php 

@Groups({"livres:read","livres:write","auteurs:item:get"})

````


#

---

**API Plateform**
**Validation**

---
#

1) Le constructeur de l'entité `Auteurs` n'a pas de paramètres :

```php 
    public function __construct(){
        
        $this->createAdt = new \DateTimeImmutable();
        $this->livres = new ArrayCollection();
    }
```

2) On remet les setter sur name et prenom

## Contexte

Un client API peut envoyer de mauvaise donnnées de différentes manières :

- Il peut envoyer du JSON malformé
- ou envoyer un champ name, prenom
- ou etre rincé

Le travail de notre API est de répondre aux situations informatiques de façon cohérente afin que les erreurs puissent etre facilement comprises


## Traitemet JSON invalide

C'est l'un des dommaines dans lesquels API-Plateform excelle vraiment

Si on envoie un json tout flingué, on recoit une erreur 400 de type hydra:error

En gros API-Plateform gere le cas de problèmes liés à la syntaxe

## Validation d'attribut

si on envoie juste {}, erreur 500 internal erreur

````json 
  "hydra:description": "An exception occurred while executing a query: SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'name' cannot be null",
````

en gros on peut pas, API Plateform envoie un auteur VIDE mais la BD bloque au moment de persist

# Conclusion

Symfony ajoute ou erreur 500, ca veut dire que l'on doit controler et décider les règles exactes pour chaque attribut


## Validation des attributs

Regles métiers pour les attributs de l'entité `Auteurs`

- le nom d'un auteur ne doit pas etre null ou vide
- le prénom d'un auteur ne doit pas etre null ou vide
- la biographie d'un auteur ne doit pas etre null ou vide:
  - texte de longueur min : 10
  - texte de longueur max : 2000
  - message en cas d'échec de validation


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

## Conclusion

La seule chose dont nous devons prendre en charge en tant que developpeur d'API, ce sont les règles métiers.
Le reste est gérer par API Plateform.

#

---

**API Plateform**
****

---

#

Les relation 

## Mettre à jour un attribut d'une relation imbriquée


Résultat :

eh bien , la raison pour laquelle le nom d'un auteur est intégré lors de la sérialisation d'un livre est ue, au dessus du nom de l'auteru, nous avons ajouté le groupe 
`livres:item:get`, qui est l'un des groupes utilisé da l'opération get

```php 
 @Groups({"auteurs:read","auteurs:write","livres:item:get","livres:write"})
```


## Envoyer des nouveaux objets ou envoyer des références à des objets

Nous avons l'erreur suiviante
```
A new entity was gound trhough the relationship Livre#Auteurs tjat was not configured to cascade persist operations for entity
```



cela signifie que quelque chose a crée un objet nouveau, l'a defini sur la propriété Livres#Auteurs, il faut donc mettre a jour et non créer un objet

si on veut modifier, il faut rajouter @id

mapped

## L'attribut de type collection est modifiable 

```php 
class Auteur

    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur", orphanRemoval=true)
     * @Groups({"auteurs:read","auteurs:write"})
     */
    private $livres;
```

## Créer de nouvels items par l'attribut d'une collection

Pour l'instant on peut mettre que des IRI et pas un tableau JSON donc

## Gérer la persistence 

```php 
    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur", orphanRemoval=true, cascade={"persist"})
     * @Groups({"auteurs:read","auteurs:write"})
     */
    private $livres;
```

Cependant les contraintes de validation ne sont pas propagé aux enfants, sinon on peut envoyer des livres VIDES

On fait :

```php 
    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur", orphanRemoval=true, cascade={"persist"})
     * @Groups({"auteurs:read","auteurs:write"})
     * Assert\Valid()
     */
    private $livres;
```


-----

**R4.01 Architecture logiciel**

-----

**API Plateform**

**Mettre à jour une collection**

**Update et Delete**

-----



## Update

# Hypothèse

on enleve la propriété `orphanRemoval ` si elle existe dans la class auteurs et 
on execute ceci dans le PUT

```json
{
  "livres": [
      "/api/livres/3"
  ],
  "biographie": "super vie voir suer nteressfiefijsfsfslf"
}
```

(ca marche)


On essaie de delete avec ces lignes la

```json 
{

  "livres": [
    "/api/livres/4"
  ]

}
```

(ca marche pas) mais moi ca marche lol

Il faut donc modifer le removeList

## Experience 2 : Orphan Removal

On ajoute la propriété oprhanRemoval=true sur l'entité `Auteur` :
(évite les livres orphelins)

```php 
    class Auteurs
    
    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur", cascade={"persist"}, orphanRemoval=true)
     * @Groups({"auteurs:read","auteurs:write"})
     * @Assert\Valid()
     */
    private $livres;
```

# Filtrage et recherche

## Contexte

----

nous savons maintenant :

- comment exposer une entité etn tant que ressource API
- plein de trucs


Mais c'est quoi le filtrage?

Notre client API - qui pourrait simplement etre react ne voudra pas toujours récupérer chaque Auteur ou livre du systeme

Que se passe-t-il si vous avez besoin de truver que les livres dont le titre contient un mot ? ou par année
de publication ? que les autres dont le nom contient une chaine ?

On parle alors de filtrage ou de filtres


## Filtrage de type de recherche sur le titre de l'entité `Livres`

### Configuration

```php 
use ApiPlatform\Core\Bridge\Doctrine\ORM\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ApiResource(
 *     itemOperations={
 *     "get"={
 *     "normalization_context"={"groups"={"livres:read","livres:item:get"}},
 *     },
 *     "delete"={},
 *     "put",
 *     "patch",
 *     },
 *   normalizationContext={"groups"={"livres:read"}},
 *     denormalizationContext={"groups"={"livres:write"}}
 * )
 * @ApiFilter(SearcheFilter::class, properties={"titre" : "partial"})
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 */
class Livre
```

----> partial = nimporte ou dans le titre

Ca marche et c'est incroyable.
Il met dans l'ordre de l'id


## Filtrage de type intervalle de valeurs sur l'année de l'entité `Livre`

### Configuration 

```php 
 /**
 * @ApiFilter(SearchFilter::class, properties={"titre" : "partial"})
 * @ApiFilter(RangeFilter::class, properties={"annee"})
 */
```

pour trouver les formats, curseur sur la classe 

````php 
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
````

trois petits points en bas a droite et controle F sur ce qu'on veut 


## Filtrage sur les propriétés 

## Ou comment faire une ou plusieurs PROJECTIONS (choisir ses attributs)

On voudrait reduire la taille d'une biographie par un court resumé et avoir un bouton "voir plus" pour afficher toute la biographie

```php 

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    /**
     * @return string|null
     * @Groups ("auteurs:read")
     * Symphony sait le faire tout seul
     */

    public function getShortBiographie(): ?string
    {
        if (strlen($this->biographie) <40){
            return $this->biographie;
        }
        return substr($this->biographie,0,40).'...';
    }

```

# Soucis

- On affiche shortBiographie ET biographie ce qui est plutot relou
- Il faudrait ne pas afficher biographie

Use à utiliser

```php 
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;

```

```php 
 /**
 * @ApiFilter (PropertyFilter::class)
```

Recherche :)

```URL
http://localhost:8000/api/auteurs/1.json?properties[]=name&properties[]=prenom&properties[]=shortBiographie
```

Si on demande un attribut qui n'existe pas, il ne le ramene pas mais fonctionne

cahgner le nom en biographie :

````php 
    /**
     * @return string|null
     * @Groups("auteurs:read")
     * @SerializedName("biographie")
     * Symphony sait le faire tout seul
     */

    public function getShortBiographie(): ?string
    {
        if (strlen($this->biographie) <40){
            return $this->biographie;
        }
        return substr($this->biographie,0,40).'...';
    }

````




---

**R4.01 Architecture Logicielle**

----

**API Plateform**
**Filtrage & Relations **

----

branch gitlab etape-06

----


## Contexte 

actuellement si on exécute /api/auteurs/5.jsonld :

ca fait 


## Filtrage et recherceh dans les relations


### Configuration (1)

tableau de tableau avec properties

```
http://localhost:8000/api/auteurs/1.json?properties[]=name&properties[]=prenom&properties[livres][]=titre&properties[]=shortBiographie
```

(ca marche)

et on ajoute item

````php 
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"livres:read","livres:write","auteurs:read", "auteurs:write","auteurs:item:get"})
     */
    private $titre;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"livres:read","livres:write","auteurs:read", "auteurs:write","auteurs:item:get"})
     */
    private $annee;

````



### Configuration (2)

## Ajout du filtrge par auteur dans livre


````php 
/**
 * @ApiFilter(SearchFilter::class, properties={"titre" : "partial", "auteur" : "exact"})
 * @ApiFilter(RangeFilter::class, properties={"annee"})
 */
 class Livre
 {}
````

on peut demander l'auteurs a partir du livres maitenant 

### Configuration (3)


