# DOCUMENTATION TECHNIQUE – BLOG LITTÉRAIRE SYMFONY

## 1. Présentation générale du projet

Je développe un blog littéraire avec Symfony. L’objectif du projet est de proposer une application web complète, structurée et sécurisée, permettant la publication et la gestion d’articles littéraires, avec une distinction claire entre visiteurs, utilisateurs connectés et administrateurs.

Le projet repose sur Symfony, Doctrine ORM, Twig et Bootstrap. J’ai cherché à appliquer de bonnes pratiques de développement, notamment en matière de sécurité, de structuration du code et de maintenabilité.

---

## 2. Architecture globale

J’ai structuré l’application autour de trois grandes zones fonctionnelles :

- Partie publique : consultation des articles et des catégories
- Dashboard utilisateur : gestion des articles personnels
- Administration : gestion globale du contenu et des utilisateurs

Les contrôleurs sont organisés par responsabilité :

- Controller/Public
- Controller/Dashboard
- Controller/Admin

Cette séparation permet de limiter les risques de sécurité et de garder des contrôleurs lisibles et cohérents.

---

## 3. Gestion de la sécurité

### 3.1 Firewalls et accès

Je protège les différentes zones de l’application par rôles :

- ROLE_USER pour l’espace utilisateur
- ROLE_ADMIN pour l’administration

Exemple de configuration d’accès :

```yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: ^/dashboard, roles: ROLE_USER }
```

Cela empêche tout accès non autorisé aux zones sensibles.

---

## 4. Authentification personnalisée

### Pourquoi j’ai utilisé un Authenticator

J’ai mis en place un authenticator personnalisé afin de centraliser toute la logique de connexion dans une seule classe. Cela me permet de maîtriser précisément le comportement du login.

Extrait simplifié :

```php
public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    if ($this->isGranted('ROLE_ADMIN')) {
        return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
    }

    return new RedirectResponse($this->urlGenerator->generate('app_home'));
}
```

Grâce à cette approche :
- la redirection est cohérente selon le rôle,
- la logique n’est pas dupliquée,
- le comportement est facilement modifiable.

---

## 5. Autorisations fines avec Voter

### Pourquoi j’ai utilisé un Voter

Pour gérer les droits sur les articles, j’ai utilisé un Voter. Cela permet de centraliser les règles d’autorisation liées à une entité métier.

Extrait du PostVoter :

```php
protected function voteOnAttribute(string $attribute, $post, TokenInterface $token): bool
{
    $user = $token->getUser();

    if (!$user instanceof User) {
        return false;
    }

    if (in_array('ROLE_ADMIN', $user->getRoles())) {
        return true;
    }

    return $post->getAuthor() === $user;
}
```

Ce mécanisme garantit que :
- un utilisateur ne peut modifier que ses propres articles,
- un administrateur a tous les droits,
- les contrôleurs restent simples.

---

## 6. Organisation des contrôleurs

Chaque contrôleur est volontairement léger. Il délègue :
- la validation aux formulaires,
- les règles de sécurité aux voters,
- la persistance à Doctrine.

Exemple :

```php
$this->denyAccessUnlessGranted('EDIT', $post);
```

Cette ligne suffit à sécuriser une action d’édition.

---

## 7. Gestion des formulaires

J’utilise le composant Form de Symfony pour gérer :
- la validation des données,
- la protection CSRF,
- la cohérence des formulaires.

Les formulaires sont réutilisés entre l’admin et le dashboard lorsque c’est pertinent.

---

## 8. Gestion des templates

### Mutualisation

J’ai réduit la duplication des templates en mutualisant :
- les formulaires
- les formulaires de suppression

Exemple :

```twig
{{ include('post/_form.html.twig', {
    form: form,
    button_label: 'Enregistrer'
}) }}
```

### Fusion new / edit

Lorsque cela est pertinent, j’ai fusionné les pages new et edit en une seule vue form afin de limiter la duplication.

---

## 9. Layouts et navigation

J’utilise plusieurs layouts :
- un layout public
- un layout dashboard
- un layout admin

Les layouts admin et dashboard reposent sur une base commune avec sidebar afin d’assurer une cohérence visuelle.

La navigation est conditionnée par les rôles afin d’éviter toute confusion entre les espaces.

---

## 10. Upload des fichiers

J’ai configuré un dossier spécifique pour les images de couverture.

```yaml
parameters:
    covers_directory: '%kernel.project_dir%/public/uploads/covers'
```

Je génère un nom de fichier unique afin d’éviter les collisions. Lors d’une modification, l’ancienne image est supprimée pour éviter les fichiers orphelins.

---

## 11. Slugs et unicité

Je génère les slugs à partir des titres. Si un slug existe déjà, j’ajoute un suffixe numérique afin de garantir l’unicité.

Cette approche améliore le référencement tout en évitant les conflits.

---

## 12. Commentaires et modération

Les visiteurs peuvent poster des commentaires sur les articles. Ceux-ci sont enregistrés avec un statut en attente.

Seuls les administrateurs peuvent valider un commentaire. Les commentaires non validés ne sont jamais affichés publiquement.

---

## 13. Bonnes pratiques appliquées

- Séparation claire des responsabilités
- Contrôleurs légers
- Sécurité centralisée
- Templates réutilisables
- Code lisible et maintenable

---

## 14. Pistes d’évolution

Le projet peut évoluer vers :
- un système de brouillons et de publication programmée
- une API REST ou headless
- des tests fonctionnels et unitaires
- un système de notifications