<?php

namespace  Mozart\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use  Mozart\Bundle\NucleusBundle\Annotation as Mozart;
use  Mozart\Bundle\UserBundle\Model\UserMeta as ModelUserMeta;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 *  Mozart\Bundle\UserBundle\Entity\UserMeta
 *
 * @ORM\Table(name="usermeta")
 * @ORM\Entity
 * @Mozart\WPTable
 */
class UserMeta extends ModelUserMeta
{
    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="umeta_id", type="bigint", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="meta_key", type="string", length=255, nullable=true)
     * @Constraints\NotBlank()
     */
    protected $key;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="meta_value", type="wordpressmeta", nullable=true)
     */
    protected $value;

    /**
     * {@inheritdoc}
     *
     * @ORM\ManyToOne(targetEntity=" Mozart\Bundle\UserBundle\Entity\User", inversedBy="metas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="ID")
     * })
     */
    protected $user;

    /**
     * Get user
     *
     * @return \ Mozart\Bundle\UserBundle\Model\UserInterface|null
     */
    public function getUser()
    {
        if ($this->user instanceof \Doctrine\ORM\Proxy\Proxy) {
            try {
                // prevent lazy loading the user entity because it might not exist
                $this->user->__load();
            } catch (\Doctrine\ORM\EntityNotFoundException $e) {
                // return null if user does not exist
                $this->user = null;
            }
        }

        return $this->user;
    }
}
