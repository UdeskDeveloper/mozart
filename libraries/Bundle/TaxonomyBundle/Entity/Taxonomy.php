<?php

namespace Mozart\Bundle\TaxonomyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Mozart\Bundle\NucleusBundle\Annotation as Mozart;
use Mozart\Bundle\TaxonomyBundle\Model\Taxonomy as ModelTaxonomy;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Table(name="term_taxonomy")
 * @ORM\Entity
 * @Mozart\WPTable
 */
class Taxonomy extends ModelTaxonomy
{
    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="term_taxonomy_id", type="wordpressid", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="taxonomy", type="string", length=32)
     * @Constraints\NotBlank()
     */
    protected $name;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="description", type="text")
     */
    protected $description = '';

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="parent", type="bigint", length=20)
     */
    protected $parent;

    /**
     * {@inheritdoc}
     *
     * @ORM\Column(name="count", type="bigint", length=20)
     */
    protected $count = 0;

    /**
     * {@inheritdoc}
     *
     * @ORM\OneToOne(targetEntity="Mozart\Bundle\TaxonomyBundle\Entity\Term", inversedBy="taxonomy")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="term_id", referencedColumnName="term_id", unique=true)
     * })
     */
    protected $term;

    /**
     * {@inheritdoc}
     *
     * @ORM\ManyToMany(targetEntity="Mozart\Bundle\PostBundle\Entity\Post", mappedBy="taxonomies")
     */
    protected $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }
}
