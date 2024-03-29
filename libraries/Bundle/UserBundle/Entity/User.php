<?php

namespace  Mozart\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use  Mozart\Bundle\NucleusBundle\Annotation as Mozart;
use Symfony\Component\Validator\Constraints as Constraints;
use  Mozart\Bundle\UserBundle\Model\User as ModelUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *  Mozart\Bundle\UserBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 * @UniqueEntity({"fields": "email", "message": "Sorry, that email address is already used."})
 * @UniqueEntity({"fields": "username", "message": "Sorry, that username is already used."})
 * @UniqueEntity({"fields": "nicename", "message": "Sorry, that nicename is already used."})
 * @UniqueEntity({"fields": "displayName", "message": "Sorry, that display name has already been taken."})
 * @ORM\HasLifecycleCallbacks
 * @Mozart\WPTable
 */
class User extends ModelUser
{
    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="ID", type="wordpressid", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_login", type="string", length=60, unique=true)
     * @Constraints\NotBlank()
     */
    protected $username;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_pass", type="string", length=64)
     * @Constraints\NotBlank()
     */
    protected $password;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_nicename", type="string", length=64)
     * @Constraints\NotBlank()
     */
    protected $nicename;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_email", type="string", length=100)
     * @Constraints\NotBlank()
     * @Constraints\Email()
     */
    protected $email;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_url", type="string", length=100)
     * @Constraints\Url()
     */
    protected $url = '';

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_registered", type="datetime")
     */
    protected $registeredDate;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_activation_key", type="string", length=60)
     */
    protected $activationKey = '';

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="user_status", type="integer", length=11)
     */
    protected $status = 0;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="display_name", type="string", length=250)
     * @Constraints\NotBlank()
     */
    protected $displayName;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToMany(targetEntity="Mozart\Bundle\UserBundle\Entity\UserMeta", mappedBy="user", cascade={"persist"})
     */
    protected $metas;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToMany(targetEntity="Mozart\Bundle\PostBundle\Entity\Post", mappedBy="user")
     */
    protected $posts;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToMany(targetEntity="Mozart\Bundle\CommentBundle\Entity\Comment", mappedBy="user")
     */
    protected $comments;

    public function __construct()
    {
        $this->metas = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->registeredDate = new \DateTime('now');
    }
}
