# BLOG LITTÉRAIRE SYMFONY

Je développe un blog littéraire avec Symfony. Le site propose une partie publique permettant de lire des articles et de naviguer par catégories. J’ai ajouté un espace utilisateur pour gérer ses propres articles, ainsi qu’une zone d’administration destinée à la modération et à la gestion du contenu.

## PRÉREQUIS

Je travaille avec **PHP 8.2 ou supérieur**, **Composer** et une base de données compatible avec **Doctrine** (par exemple MySQL ou PostgreSQL). J’utilise **Symfony CLI** pour lancer le serveur plus facilement, mais ce n’est pas obligatoire.

## INSTALLATION

1. J’installe les dépendances PHP avec Composer :

   ```bash
   composer install
   ```

2. Je configure la base de données dans le fichier `.env.local`.  
   Je définis la variable `DATABASE_URL` avec mes paramètres locaux.

3. Je crée la base de données et j’applique les migrations :

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

4. Je prépare le dossier d’upload des images de couverture.  
   Je vérifie que le dossier `public/uploads/covers` existe et qu’il est accessible en écriture par PHP.

## LANCER LE PROJET

Je peux lancer le serveur Symfony :

```bash
symfony serve
```

Je peux également utiliser le serveur PHP intégré :

```bash
php -S 127.0.0.1:8000 -t public
```

## ACCÈS AU SITE

Je peux accéder à la partie publique du site :

- Page d’accueil : `/`
- Liste des articles : `/articles`
- Détail d’un article : `/blog/{slug}`
- Liste des catégories : `/categories`
- Détail d’une catégorie : `/category/{slug}`

Je peux accéder à l’authentification :

- Connexion : `/login`
- Inscription : `/register`
- Déconnexion : `/logout`

## TESTS

```bash
php bin/phpunit
