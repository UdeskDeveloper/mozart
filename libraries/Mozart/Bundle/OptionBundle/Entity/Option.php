<?php

namespace  Mozart\Bundle\OptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use  Mozart\Bundle\NucleusBundle\Annotation as Mozart;
use  Mozart\Bundle\OptionBundle\Model\Option as ModelOption;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 *  Mozart\Bundle\OptionBundle\Entity\Option
 *
 * @ORM\Table(name="options")
 * @ORM\Entity
 * @Mozart\WPTable
 */
class Option extends ModelOption
{
    /**
     * @var int $id
     *
     * @ORM\Column(name="option_id", type="bigint", length=20)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="option_name", type="string", length=64, nullable=false, unique=true)
     * @Constraints\NotBlank()
     */
    protected $name;

    /**
     * @var string $value
     *
     * @ORM\Column(name="option_value", type="wordpressmeta", nullable=false)
     */
    protected $value;

    /**
     * @var string $autoload
     *
     * @ORM\Column(name="autoload", type="string", length=20, nullable=false)
     * @Constraints\NotBlank()
     */
    protected $autoload = 'yes';
}
