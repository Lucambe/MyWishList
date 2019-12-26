# MyWishList

Projet PHP @IUT-NC 2019

## Installation

Utilisez [composer](https://getcomposer.org/) pour installer MyWishList.

```bash
git clone https://github.com/Wilders/MyWishList.git
cd MyWishList
composer install
```

Il faut créer un fichier de configuration pour la base de donnée nommé **database.ini** dans le répertoire src/config.
En insérant:

| Paramètre     | Valeur d'exemple | Description               |
| :------------:|:----------------:|:-------------------------:|
| driver        | mysql            | Driver de votre SGBD      |
| host          | localhost        | Hôte de votre BDD         |
| database      | mywishlist       | Nom de votre BDD          |
| username      | root             | Nom d'user de votre BDD   |
| password      | root             | Mot de passe de votre BDD |
| charset       | utf8             | Méthode d'encodage        |
| collation     | utf8_unicode_ci  | Collation de la BDD       |

## Utilisation

Lancez un serveur XAMP, importez la BDD et connectez-vous sur le site.

## Disparités avec le sujet

- [x] *Nous utilisons Slim 3*
- [x] *Nous utilisons [FIG Cookies](https://github.com/dflydev/dflydev-fig-cookies) 1.0.2 pour gérer les cookies*
- [x] *Nous utilisons [PHPView](https://github.com/slimphp/PHP-View) 2.2 pour gérer les vues et les layouts*
- [x] *Nous utilisons [Flash](https://github.com/slimphp/Slim-Flash) pour gérer les messages Flash (erreurs,succès..)*


## Fonctionnalités

### Participant

- [x] *Afficher une liste de souhaits* (Jules)
- [x] *Afficher un item d'une liste* (Jules)
- [x] *Réserver un item* (Anthony, Jules)
- [x] *Ajouter un message avec sa réservation* (Anthony, Jules)
- [x] *Ajouter un message sur une liste* (Nathan, Jules)

### Créateur
- [x] *Créer une liste* (Anthony, Jules)
- [x] *Modifier les informations générales d'une de ses listes* (Anthony, Jules)
- [x] *Ajouter des items* (Anthony, Jules)
- [x] *Modifier un item* (Nathan, Jules)
- [x] *Supprimer un item* (Anthony, Jules)
- [ ] *Rajouter une image à un item*
- [ ] *Modifier une image à un item*
- [ ] *Supprimer une image d'un item*
- [x] *Partager une liste* (Jules)
- [ ] *Consulter les réservations d'une de ses listes avant échéance*
- [ ] *Consulter les réservations et messages d'une de ses listes après échéance*

### Extensions
- [ ] *Créer un compte*
- [ ] *S'authentifier*
- [ ] *Modifier son compte*
- [ ] *Rendre une liste publique*
- [ ] *Afficher les listes de souhaits publiques*
- [ ] *Créer une cagnotte sur un item*
- [ ] *Participer à une cagnotte*
- [ ] *Uploader une image*
- [ ] *Créer un compte participant*
- [ ] *Afficher la liste des créateurs*
- [ ] *Supprimer son compte*
- [ ] *Joindre les listes à son compte*

## Contributions
**SAYER Jules** - S3B @[Wilders](https://github.com/Wilders/MyWishList/commits?author=Wilders)

**PERNOT Anthony** - S3B @[anthopernot](https://github.com/Wilders/MyWishList/commits?author=anthopernot)

**CHEVALIER Nathan** - S3B @[FuretVolant](https://github.com/Wilders/MyWishList/commits?author=FuretVolant)
