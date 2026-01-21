<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    public const CREATE = 'POST_CREATE';
    public const EDIT = 'POST_EDIT';
    public const DELETE = 'POST_DELETE';

    public function __construct(private readonly AuthorizationCheckerInterface $auth)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::EDIT, self::DELETE], true)) {
            return false;
        }

        if ($attribute === self::CREATE) {
            return true;
        }

        return $subject instanceof Post;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        if ($this->auth->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($attribute === self::CREATE) {
            return $this->auth->isGranted('ROLE_USER');
        }

        $post = $subject;
        return $post->getAuthor() !== null && $post->getAuthor()->getId() === $user->getId();
    }
}
