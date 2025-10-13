# Blog MVC

Ce projet est un blog développé selon le modèle MVC (Modèle-Vue-Contrôleur) en PHP.

## Description

Ce dépôt contient un système de blog simple et personnalisable, construit en PHP (99.8%) avec une légère utilisation de CSS (0.2%) pour le style. L'objectif est d'offrir une structure claire pour développer ou étendre un site de blog tout en appliquant les principes du MVC.

## Fonctionnalités principales

- Création, modification et suppression d’articles de blog
- Gestion des utilisateurs et de l’authentification
- Affichage des articles avec pagination
- Système de commentaires
- Interface d’administration sécurisée
- Séparation stricte entre le modèle, la vue et le contrôleur

## Prérequis

- PHP 7.4 ou supérieur
- Serveur web (Apache, Nginx, etc.)
- Base de données MySQL ou MariaDB

## Installation

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/kiki40keke/Blog-MVC.git
   ```
2. Placez le projet dans le répertoire racine de votre serveur web.
3. Configurez la connexion à la base de données dans le fichier de configuration (ex: `config/database.php`).
4. Importez le schéma de la base de données fourni dans votre SGBD.
5. Accédez à l’application depuis votre navigateur.

## Utilisation

- Rendez-vous sur la page d’accueil pour voir les articles publiés.
- Connectez-vous pour accéder à l’interface d’administration et gérer le contenu.
- Personnalisez le thème et les fonctionnalités selon vos besoins en modifiant les fichiers PHP et CSS.

## Arborescence du projet

```
Blog-MVC/
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
├── public/
│   ├── css/
│   └── index.php
├── config/
└── README.md
```

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à proposer des améliorations ou à signaler des bugs via les issues ou les pull requests.

## Licence

Ce projet est sous licence MIT.

---

**Auteur** : [kiki40keke](https://github.com/kiki40keke)
