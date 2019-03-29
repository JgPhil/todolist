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

    private $decisionManager;
    private $token;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

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

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $this->token = $token;
        $user = $this->token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Task object, thanks to supports
        /** @var Task $task */
        $task = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($task, $user);
            case self::DEL:
                return $this->canDelete($task, $user);
            case self::TOGGLE:
                return $this->canToggle($task, $user);
            default :
                return false;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Task $task, User $user)
    {
        // if user is ROLE_ADMIN he can edit the anonymous task
        if ($task->getCreatedBy() == null && $this->decisionManager->decide($this->token, ['ROLE_ADMIN'])) {
            return true;
        }
        return $user === $task->getCreatedBy();
    }

    private function canDelete(Task $task, User $user)
    {
        if ($this->decisionManager->decide($this->token, ['ROLE_ADMIN'])) {
            return true;
        }
        return $user === $task->getCreatedBy();
    }

    private function canToggle(Task $task, User $user)
    {
        if ($this->decisionManager->decide($this->token, ['ROLE_ADMIN'])) {
            return true;
        }
        return $user === $task->getCreatedBy();
    }
}