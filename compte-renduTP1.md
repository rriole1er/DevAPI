
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

