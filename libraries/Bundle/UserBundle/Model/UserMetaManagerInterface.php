<?php

namespace  Mozart\Bundle\UserBundle\Model;

interface UserMetaManagerInterface
{
    public function addMeta(User $user, UserMeta $meta);

    public function findAllMetasByUser(User $user);

    public function findMetasBy(array $criteria);

    public function findOneMetaBy(array $criteria);
}
