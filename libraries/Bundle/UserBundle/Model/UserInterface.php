<?php

namespace Mozart\Bundle\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

/**
 * Interface UserInterface
 *
 * @package Mozart\Bundle\UserBundle\Model
 */
interface UserInterface extends SymfonyUserInterface
{
    /**
     * @return mixed
     */
    public function getDisplayName();

    /**
     * @return mixed
     */
    public function getUrl();

    /**
     * @return mixed
     */
    public function getEmail();
}
