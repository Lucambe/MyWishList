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

## Fonctionnalités

### Participant

- [ ] *Afficher une liste de souhaits*
- [ ] *Afficher un item d'une liste*
- [ ] *Réserver un item*
- [ ] *Ajouter un message avec sa réservation*

### Créateur
- [ ] *Créer une liste*
- [ ] *Modifier les informations générales d'une de ses listes*
- [ ] *Ajouter des items*
- [ ] *Modifier un item*
- [ ] *Supprimer un item*
- [ ] *Rajouter une image à un item*
- [ ] *Modifier une image à un item*
- [ ] *Supprimer une image d'un item*
- [ ] *Partager une liste*
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
