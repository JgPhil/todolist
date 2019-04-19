<?php
namespace AppBundle\Security;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class TaskVoter extends Voter
{
    const EDIT = 'edit';
    const DEL = 'delete';
    const TOGGLE = 'toggle';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT, self::DEL, self::TOGGLE])) {
            return false;
        }

        // only vote on Task objects inside this voter
        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $task, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($task, $user);
            case self::DEL:
                return $this->canDelete($task, $user);
            case self::TOGGLE:
                return $this->canToggle($task, $user);
        }
    }

    private function canEdit(Task $task, User $user)
    {
        // if user is ROLE_ADMIN he can edit the anonymous task
        if ($task->getCreatedBy() === null && $user->isAdmin()) {
            return true;
        }
        
        return $user === $task->getCreatedBy();
    }

    private function canDelete(Task $task, User $user)
    {
        return $user->isAdmin() || $user === $task->getCreatedBy();
    }

    private function canToggle(Task $task, User $user)
    {
        return $user->isAdmin() || $user === $task->getCreatedBy();
    }
}
