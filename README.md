# Blog Littéraire

Blog littéraire développé avec Symfony 7.4 permettant la publication et la gestion d'articles, avec modération des commentaires.

## Fonctionnalités

**Visiteur**
- Consultation des articles avec recherche et pagination
- Navigation par catégories
- Dépôt de commentaires (soumis à modération)

**Utilisateur connecté**
- Création, modification et suppression de ses articles
- Upload d'images de couverture

**Administrateur**
- Gestion complète des articles, catégories et utilisateurs
- Modération des commentaires

## Prérequis

- PHP 8.2+
- Composer
- MySQL 8.0+ ou PostgreSQL 16+
- Symfony CLI (recommandé)

## Installation

```bash
# Cloner le projet
git clone <url-du-repo>
cd blog-litteraire

# Installer les dépendances
composer install

# Configurer la base de données dans .env.local
cp .env .env.local
```

Modifier `DATABASE_URL` dans `.env.local` :

```env
# MySQL
DATABASE_URL="mysql://user:password@127.0.0.1:3306/blog_litteraire?serverVersion=8.0"

# PostgreSQL
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/blog_litteraire?serverVersion=16&charset=utf8"
```

```bash
# Créer la base et appliquer les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Vérifier que le dossier d'upload existe
mkdir -p public/uploads/covers
```

## Lancement

```bash
# Avec Symfony CLI
symfony serve

# Ou avec PHP
php -S 127.0.0.1:8000 -t public
```

## Créer un administrateur

1. Créer un compte via `/register`
2. Modifier le champ `roles` en base de données :
   ```sql
   UPDATE user SET roles = '["ROLE_ADMIN"]' WHERE email = 'admin@example.com';
   ```
3. Gérer ensuite les utilisateurs depuis `/admin/users`

## Routes principales

| Route | Description |
|-------|-------------|
| `/` | Accueil |
| `/articles` | Liste des articles |
| `/categories` | Liste des catégories |
| `/login` | Connexion |
| `/register` | Inscription |
| `/dashboard/posts` | Espace utilisateur |
| `/admin` | Administration |

## Structure

```
src/
├── Controller/
│   ├── Admin/          # Back-office administration
│   ├── Dashboard/      # Espace utilisateur connecté
│   └── Public/         # Pages publiques
├── Entity/             # Entités Doctrine
├── Form/               # Formulaires
├── Repository/         # Requêtes base de données
└── Security/           # Authenticator et Voter

templates/
├── admin/              # Vues administration
├── dashboard/          # Vues espace utilisateur
├── public/             # Vues publiques
├── components/         # Composants réutilisables
├── form/               # Formulaires partagés
├── layouts/            # Layouts avec sidebar
└── partials/           # Header, footer, pagination
```

## Stack technique

- **Framework** : Symfony 7.4
- **ORM** : Doctrine
- **Templates** : Twig
- **CSS** : Bootstrap 5.3 (via Importmap)
- **Authentification** : Security component avec authenticator personnalisé
- **Autorisation** : Voter pour les droits sur les articles