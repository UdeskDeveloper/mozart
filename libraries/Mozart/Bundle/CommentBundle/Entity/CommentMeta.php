<?php

namespace  Mozart\Bundle\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use  Mozart\Bundle\NucleusBundle\Annotation as Mozart;
use  Mozart\Bundle\CommentBundle\Model\CommentMeta as ModelCommentaMeta;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Table(name="commentmeta")
 * @ORM\Entity
 * @Mozart\WPTable
 */
class CommentMeta extends ModelCommentaMeta
{
    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="meta_id", type="bigint", length=20)
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
     * @var string $value
     *
     * @ORM\Column(name="meta_value", type="wordpressmeta", nullable=true)
     */
    protected $value;

    /**
     * {@inheritdoc}
     *
     * @ORM\ManyToOne(targetEntity="Mozart\Bundle\CommentBundle\Entity\Comment", inversedBy="metas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comment_id", referencedColumnName="comment_ID")
     * })
     */
    protected $comment;
}
