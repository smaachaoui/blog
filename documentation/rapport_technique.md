# Rapport Technique : Blog Littéraire Symfony

## 1. Présentation du projet

### 1.1 Contexte

J'ai développé un blog littéraire avec Symfony dans le cadre du titre professionnel Développeur Web et Web Mobile. L'application permet la publication d'articles, la navigation par catégories et la modération de commentaires.

### 1.2 Objectifs fonctionnels

J'ai conçu l'application autour de trois profils utilisateurs :

**Visiteur**
- Consulter les derniers articles publiés
- Parcourir la liste complète avec recherche et pagination
- Naviguer par catégories
- Déposer un commentaire sur un article

**Utilisateur connecté**
- Créer, modifier et supprimer ses propres articles
- Uploader une image de couverture

**Administrateur**
- Gérer l'ensemble des articles, catégories et utilisateurs
- Modérer les commentaires avant publication

### 1.3 Stack technique

| Composant | Version | Rôle |
|-----------|---------|------|
| PHP | 8.2+ | Langage serveur |
| Symfony | 7.4 | Framework applicatif |
| Doctrine ORM | - | Mapping objet-relationnel |
| Twig | - | Moteur de templates |
| Bootstrap | 5.3 | Framework CSS responsive |
| MySQL/PostgreSQL | 8.0+/16+ | Base de données |

---

## 2. Choix techniques

### 2.1 Pourquoi Symfony

J'ai choisi Symfony pour plusieurs raisons :

- **Architecture MVC** : le framework impose une séparation claire entre modèle, vue et contrôleur, ce qui rend le code lisible et maintenable.
- **Injection de dépendances** : le container de services facilite le découplage et les tests.
- **Composant Security** : la gestion de l'authentification et des autorisations est native et robuste.
- **Doctrine ORM** : l'abstraction de la base de données simplifie les requêtes et les migrations.
- **Ecosystem mature** : la documentation et la communauté permettent de résoudre rapidement les problèmes.

### 2.2 Pourquoi Bootstrap via Importmap

J'ai intégré Bootstrap via Symfony AssetMapper et Importmap pour :

- Éviter une dépendance à Node.js et npm
- Garder une configuration simple sans build complexe
- Bénéficier du responsive et des composants standards

```php
// importmap.php
'bootstrap' => [
    'version' => '5.3.8',
],
'@popperjs/core' => [
    'version' => '2.11.8',
],
'bootstrap/dist/css/bootstrap.min.css' => [
    'version' => '5.3.8',
    'type' => 'css',
],
```

```javascript
// assets/app.js
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import './styles/app.css';
```

---

## 3. Architecture applicative

### 3.1 Organisation des contrôleurs

J'ai structuré les contrôleurs par zone fonctionnelle pour séparer clairement les responsabilités :

```
src/Controller/
├── Admin/              # Gestion administration (ROLE_ADMIN)
│   ├── CategoryController.php
│   ├── CommentController.php
│   ├── DashboardController.php
│   ├── PostController.php
│   └── UserController.php
├── Dashboard/          # Espace utilisateur (ROLE_USER)
│   ├── PostController.php
│   └── ProfileController.php
├── Public/             # Pages accessibles à tous
│   ├── BlogController.php
│   ├── CategoryController.php
│   └── HomeController.php
├── RegistrationController.php
└── SecurityController.php
```

Cette organisation permet de :
- Localiser rapidement le code selon la fonctionnalité
- Appliquer des restrictions de sécurité par namespace
- Faciliter la maintenance et l'évolution

### 3.2 Organisation des templates

J'ai appliqué la même logique aux templates :

```
templates/
├── base.html.twig              # Layout principal
├── layouts/
│   └── sidebar_base.html.twig  # Layout avec sidebar
├── components/                  # Composants réutilisables
│   ├── _sidebar.html.twig
│   ├── _page_header.html.twig
│   ├── _danger_zone.html.twig
│   ├── _post_card.html.twig
│   ├── _empty_state.html.twig
│   └── _form_actions.html.twig
├── form/                        # Formulaires partagés
│   ├── _post.html.twig
│   ├── _category.html.twig
│   ├── _user.html.twig
│   └── _comment.html.twig
├── partials/                    # Éléments de layout
│   ├── _header.html.twig
│   ├── _footer.html.twig
│   ├── _flashes.html.twig
│   └── _pagination.html.twig
├── admin/                       # Vues administration
├── dashboard/                   # Vues espace utilisateur
├── public/                      # Vues publiques
└── security/                    # Connexion et inscription
```

J'ai créé une sidebar unifiée qui détecte automatiquement le rôle de l'utilisateur et affiche le menu correspondant :

```twig
{% if is_granted('ROLE_ADMIN') %}
    {# Menu administration #}
{% elseif is_granted('ROLE_USER') %}
    {# Menu espace personnel #}
{% endif %}
```

---

## 4. Modèle de données

### 4.1 Schéma des entités

J'ai construit le modèle autour de quatre entités :

```
┌─────────────┐       ┌─────────────┐
│    User     │       │   Category  │
├─────────────┤       ├─────────────┤
│ id          │       │ id          │
│ email       │       │ name        │
│ password    │       │ slug        │
│ roles       │       └──────┬──────┘
└──────┬──────┘              │
       │                     │
       │ 1:n                 │ 1:n
       │                     │
       ▼                     ▼
┌─────────────────────────────────┐
│              Post               │
├─────────────────────────────────┤
│ id                              │
│ title                           │
│ slug                            │
│ bookAuthor                      │
│ content                         │
│ coverImage                      │
│ createdAt                       │
│ updatedAt                       │
│ author_id (FK User)             │
│ category_id (FK Category)       │
└────────────────┬────────────────┘
                 │
                 │ 1:n
                 ▼
        ┌─────────────────┐
        │     Comment     │
        ├─────────────────┤
        │ id              │
        │ authorName      │
        │ authorEmail     │
        │ content         │
        │ createdAt       │
        │ isApproved      │
        │ post_id (FK)    │
        └─────────────────┘
```

### 4.2 Entité User

J'ai implémenté `UserInterface` et `PasswordAuthenticatedUserInterface` pour intégrer Symfony Security :

```php
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'author')]
    private Collection $posts;
}
```

### 4.3 Entité Post

J'ai initialisé `createdAt` dans le constructeur pour garantir une valeur par défaut :

```php
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
    }
}
```

### 4.4 Entité Comment

J'ai ajouté un booléen `isApproved` pour contrôler la modération :

```php
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Column]
    private bool $isApproved = false;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isApproved = false;
    }
}
```

---

## 5. Fonctionnalités publiques

### 5.1 Page d'accueil

J'affiche les six derniers articles pour offrir une entrée rapide dans le contenu :

```php
#[Route('/', name: 'app_home', methods: ['GET'])]
public function home(PostRepository $postRepository): Response
{
    $latestPosts = $postRepository->findBy([], ['createdAt' => 'DESC'], 6);

    return $this->render('public/home/index.html.twig', [
        'posts' => $latestPosts,
    ]);
}
```

### 5.2 Liste des articles avec recherche et pagination

J'ai centralisé la logique de recherche dans le repository pour garder le contrôleur lisible :

```php
// PostRepository.php
public function search(?string $query, int $limit, int $offset): array
{
    $qb = $this->createQueryBuilder('p')
        ->orderBy('p.createdAt', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult($offset);

    if ($query) {
        $qb->andWhere('p.title LIKE :q OR p.bookAuthor LIKE :q OR p.content LIKE :q')
           ->setParameter('q', '%' . $query . '%');
    }

    return $qb->getQuery()->getResult();
}

public function countSearch(?string $query): int
{
    $qb = $this->createQueryBuilder('p')
        ->select('COUNT(p.id)');

    if ($query) {
        $qb->andWhere('p.title LIKE :q OR p.bookAuthor LIKE :q OR p.content LIKE :q')
           ->setParameter('q', '%' . $query . '%');
    }

    return $qb->getQuery()->getSingleScalarResult();
}
```

```php
// BlogController.php
#[Route('/articles', name: 'blog_index', methods: ['GET'])]
public function index(Request $request, PostRepository $postRepository): Response
{
    $q = $request->query->get('q', '');
    $page = max(1, $request->query->getInt('page', 1));
    $limit = 12;
    $offset = ($page - 1) * $limit;

    if ($q !== '') {
        $posts = $postRepository->search($q, $limit, $offset);
        $total = $postRepository->countSearch($q);
    } else {
        $posts = $postRepository->findBy([], ['createdAt' => 'DESC'], $limit, $offset);
        $total = $postRepository->count([]);
    }

    return $this->render('public/blog/index.html.twig', [
        'posts' => $posts,
        'query' => $q,
        'currentPage' => $page,
        'totalPages' => ceil($total / $limit),
    ]);
}
```

### 5.3 Navigation par catégories

J'ai ajouté une recherche dans une catégorie pour affiner les résultats :

```php
// PostRepository.php
public function searchInCategory(int $categoryId, ?string $query, int $limit, int $offset): array
{
    $qb = $this->createQueryBuilder('p')
        ->andWhere('p.category = :categoryId')
        ->setParameter('categoryId', $categoryId)
        ->orderBy('p.createdAt', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult($offset);

    if ($query) {
        $qb->andWhere('p.title LIKE :q OR p.bookAuthor LIKE :q')
           ->setParameter('q', '%' . $query . '%');
    }

    return $qb->getQuery()->getResult();
}
```

### 5.4 Lecture d'un article et commentaires

J'ai choisi le slug pour les URLs afin d'améliorer le référencement et la lisibilité :

```php
#[Route('/article/{slug}', name: 'blog_show', methods: ['GET', 'POST'])]
public function show(
    string $slug,
    PostRepository $postRepository,
    CommentRepository $commentRepository,
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    $post = $postRepository->findOneBy(['slug' => $slug]);

    if (!$post) {
        throw $this->createNotFoundException();
    }

    $comment = new Comment();
    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $comment->setPost($post);
        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Commentaire envoyé. Il sera visible après validation.');

        return $this->redirectToRoute('blog_show', ['slug' => $slug], Response::HTTP_SEE_OTHER);
    }

    return $this->render('public/blog/show.html.twig', [
        'post' => $post,
        'comments' => $commentRepository->findApprovedForPost($post->getId()),
        'commentForm' => $form,
    ]);
}
```

J'affiche uniquement les commentaires approuvés côté public :

```php
// CommentRepository.php
public function findApprovedForPost(int $postId): array
{
    return $this->createQueryBuilder('c')
        ->andWhere('c.post = :postId')
        ->andWhere('c.isApproved = true')
        ->setParameter('postId', $postId)
        ->orderBy('c.createdAt', 'ASC')
        ->getQuery()
        ->getResult();
}
```

---

## 6. Espace utilisateur (Dashboard)

### 6.1 Protection de l'accès

J'ai protégé l'ensemble du contrôleur avec `ROLE_USER` :

```php
#[IsGranted('ROLE_USER')]
#[Route('/dashboard/posts')]
class PostController extends AbstractController
{
    // ...
}
```

### 6.2 Liste des articles de l'utilisateur

J'affiche uniquement les articles dont l'utilisateur connecté est l'auteur :

```php
#[Route('/', name: 'dashboard_post_index', methods: ['GET'])]
public function index(PostRepository $postRepository): Response
{
    $user = $this->getUser();
    $posts = $postRepository->findBy(['author' => $user], ['createdAt' => 'DESC']);

    return $this->render('dashboard/post/index.html.twig', [
        'posts' => $posts,
    ]);
}
```

### 6.3 Création d'un article avec upload de couverture

J'ai configuré la validation du fichier uploadé :

```php
// PostType.php
->add('coverImageFile', FileType::class, [
    'mapped' => false,
    'required' => false,
    'label' => 'Couverture (jpg, png, webp)',
    'help' => 'Taille max 2 Mo.',
    'constraints' => [
        new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'image/jpeg',
                'image/png',
                'image/webp',
            ],
            'mimeTypesMessage' => 'Format non autorisé.',
        ]),
    ],
])
```

J'ai externalisé le chemin de stockage dans la configuration :

```yaml
# config/services.yaml
parameters:
    covers_directory: '%kernel.project_dir%/public/uploads/covers'
```

J'ai géré le remplacement de l'ancienne image pour éviter les fichiers orphelins :

```php
#[Route('/{id}/edit', name: 'dashboard_post_edit', methods: ['GET', 'POST'])]
public function edit(
    Request $request,
    Post $post,
    EntityManagerInterface $entityManager,
    #[Autowire('%covers_directory%')] string $coversDirectory
): Response {
    $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $coverFile = $form->get('coverImageFile')->getData();

        if ($coverFile) {
            // Suppression de l'ancienne image
            if ($post->getCoverImage()) {
                $oldPath = $coversDirectory . '/' . $post->getCoverImage();
                $filesystem = new Filesystem();
                if ($filesystem->exists($oldPath)) {
                    $filesystem->remove($oldPath);
                }
            }

            // Upload de la nouvelle image
            $newFilename = uniqid() . '.' . $coverFile->guessExtension();
            $coverFile->move($coversDirectory, $newFilename);
            $post->setCoverImage($newFilename);
        }

        $post->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->flush();

        $this->addFlash('success', 'Article modifié.');

        return $this->redirectToRoute('dashboard_post_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('dashboard/post/edit.html.twig', [
        'post' => $post,
        'form' => $form,
    ]);
}
```

---

## 7. Administration

### 7.1 Double protection de l'accès

J'ai sécurisé l'administration à deux niveaux :

**Niveau 1 : Configuration globale**

```yaml
# config/packages/security.yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
```

**Niveau 2 : Attribut sur chaque contrôleur**

```php
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/comments')]
final class CommentController extends AbstractController
{
    // ...
}
```

Cette double protection évite toute exposition accidentelle si une route change.

### 7.2 Modération des commentaires

J'ai implémenté un système de modération avec protection CSRF :

```php
#[Route('/', name: 'admin_comment_index', methods: ['GET'])]
public function index(Request $request, CommentRepository $commentRepository): Response
{
    $pending = $request->query->getBoolean('pending');

    if ($pending) {
        $comments = $commentRepository->findBy(['isApproved' => false], ['createdAt' => 'DESC']);
    } else {
        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);
    }

    return $this->render('admin/comment/index.html.twig', [
        'comments' => $comments,
    ]);
}

#[Route('/{id}/approve', name: 'admin_comment_approve', methods: ['POST'])]
public function approve(
    Request $request,
    Comment $comment,
    EntityManagerInterface $entityManager
): Response {
    $token = $request->getPayload()->getString('_token');

    if (!$this->isCsrfTokenValid('approve' . $comment->getId(), $token)) {
        throw $this->createAccessDeniedException();
    }

    $comment->setIsApproved(true);
    $entityManager->flush();

    $this->addFlash('success', 'Commentaire approuvé.');

    return $this->redirectToRoute('admin_comment_index', ['pending' => true], Response::HTTP_SEE_OTHER);
}
```

---

## 8. Sécurité et authentification

### 8.1 Configuration Security

J'ai configuré le firewall avec un provider Doctrine et mon authenticator personnalisé :

```yaml
# config/packages/security.yaml
security:
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: app_user_provider
            custom_authenticator: App\Security\AppAuthenticator
            logout:
                path: app_logout
            remember_me:
                secret: '%kernel.secret%'
                lifetime: 604800
                path: /

    access_control:
        - { path: ^/register, roles: PUBLIC_ACCESS }
        - { path: ^/login, roles: PUBLIC_ACCESS }
        - { path: ^/admin, roles: ROLE_ADMIN }
```

### 8.2 SecurityController

J'ai créé un contrôleur minimal pour exposer les routes de connexion et déconnexion :

```php
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Intercepté par le firewall.');
    }
}
```

### 8.3 Authenticator personnalisé

J'ai créé un authenticator personnalisé pour contrôler le flux de connexion et gérer la redirection selon le rôle :

```php
class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private AuthorizationCheckerInterface $authChecker
    ) {}

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        if ($this->authChecker->isGranted('ROLE_ADMIN')) {
            return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
```

### 8.4 Voter pour les articles

J'ai créé un voter pour gérer les droits métier sur les articles :

```php
class PostVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const DELETE = 'POST_DELETE';
    public const CREATE = 'POST_CREATE';

    public function __construct(
        private AuthorizationCheckerInterface $auth
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        // Un administrateur peut tout faire
        if ($this->auth->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // La création est ouverte à tout utilisateur connecté
        if ($attribute === self::CREATE) {
            return $this->auth->isGranted('ROLE_USER');
        }

        /** @var Post $post */
        $post = $subject;

        // Un utilisateur ne peut modifier/supprimer que ses propres articles
        return $post->getAuthor() !== null
            && $post->getAuthor()->getId() === $user->getId();
    }
}
```

J'applique le voter dans les contrôleurs :

```php
$this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
$this->denyAccessUnlessGranted(PostVoter::DELETE, $post);
```

---

## 9. Interface utilisateur

### 9.1 Système de layouts

J'ai créé deux layouts principaux :

**base.html.twig** : layout standard avec header et footer

```twig
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{% block title %}Blog{% endblock %}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {% block importmap %}{{ importmap('app') }}{% endblock %}
</head>

<body id="top" class="d-flex flex-column min-vh-100">
    {% block header %}
        {% include 'partials/_header.html.twig' %}
    {% endblock %}

    <main class="flex-grow-1 d-flex flex-column {% block main_class %}py-4 py-lg-5{% endblock %}">
        <div class="{% block page_container_class %}container{% endblock %} flex-grow-1 d-flex flex-column">
            {% include 'partials/_flashes.html.twig' %}
            {% block body %}{% endblock %}
        </div>
    </main>

    {% block footer %}
        {% include 'partials/_footer.html.twig' %}
    {% endblock %}
</body>
</html>
```

**sidebar_base.html.twig** : layout avec sidebar pour admin et dashboard

```twig
{% extends 'base.html.twig' %}

{% block main_class %}{% endblock %}
{% block page_container_class %}container-fluid px-0{% endblock %}

{% block body %}
<div class="row g-0 flex-grow-1">
    <aside class="col-12 col-lg-3 col-xl-2 bg-dark d-flex">
        <div class="position-lg-sticky top-0 w-100 align-self-start" style="min-height: 100%;">
            {% block sidebar %}{% endblock %}
        </div>
    </aside>

    <main class="col-12 col-lg-9 col-xl-10 bg-light">
        <div class="container-xxl py-3 py-md-4 px-3 px-md-4">
            {% include 'partials/_flashes.html.twig' %}
            {% block content %}{% endblock %}
        </div>
    </main>
</div>
{% endblock %}
```

### 9.2 Composants réutilisables

J'ai créé des composants pour éviter la duplication :

**_page_header.html.twig** : en-tête de page standardisé

```twig
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1">{{ title }}</h1>
        {% if subtitle %}
            <p class="text-muted mb-0 small">{{ subtitle }}</p>
        {% endif %}
    </div>

    {% if back_route is defined or actions is defined %}
        <div class="d-flex flex-wrap gap-2">
            {% if back_route is defined %}
                <a href="{{ path(back_route, back_params|default({})) }}" class="btn btn-outline-dark btn-sm">
                    {{ back_label|default('Retour') }}
                </a>
            {% endif %}
            {% if actions is defined %}
                {{ actions|raw }}
            {% endif %}
        </div>
    {% endif %}
</div>
```

**_danger_zone.html.twig** : bloc de suppression avec confirmation

```twig
<div class="card border-danger shadow-sm">
    <div class="card-body p-4">
        <h2 class="h6 text-danger mb-2">Zone dangereuse</h2>
        <p class="text-muted small mb-3">Cette action est irréversible.</p>

        <form method="post"
              action="{{ path(delete_route, delete_params) }}"
              onsubmit="return confirm('{{ confirm_message|default('Confirmer la suppression ?')|e('js') }}');">
            <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ entity_id) }}">
            <button class="btn btn-outline-danger w-100">Supprimer</button>
        </form>
    </div>
</div>
```

### 9.3 Responsive design

J'ai utilisé les classes Bootstrap pour assurer le responsive :

- **Grille** : `col-12 col-md-6 col-lg-4` pour les cartes d'articles
- **Sidebar** : `col-12 col-lg-3 col-xl-2` avec collapse sur mobile
- **Tableaux** : `table-responsive` avec colonnes masquées sur mobile (`d-none d-md-table-cell`)
- **Navigation** : `navbar-expand-lg` avec toggler pour mobile

---

## 10. Conclusion

J'ai développé un blog littéraire complet et sécurisé avec Symfony 7.4. L'application respecte les bonnes pratiques attendues pour un projet professionnel :

**Architecture**
- Séparation claire des responsabilités (MVC)
- Organisation par zone fonctionnelle (public, dashboard, admin)
- Composants réutilisables côté templates

**Sécurité**
- Authentification via authenticator personnalisé avec CSRF et remember_me
- Autorisation via voter pour les règles métier sur les articles
- Double protection de l'administration (access_control + IsGranted)
- Validation des uploads (type MIME, taille)

**Fonctionnalités**
- Recherche et pagination côté public
- Modération des commentaires
- Gestion des images avec nettoyage des fichiers orphelins

**Interface**
- Design responsive avec Bootstrap
- Sidebar unifiée détectant le rôle utilisateur
- Composants réutilisables pour la cohérence visuelle