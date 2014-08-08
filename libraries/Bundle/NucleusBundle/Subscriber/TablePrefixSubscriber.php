<?php

namespace Mozart\Bundle\NucleusBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\Annotations\AnnotationReader;
use Mozart\Bundle\NucleusBundle\Annotation\WPTable;

/**
 * Class TablePrefixSubscriber
 *
 * @package Mozart\Bundle\NucleusBundle\Subscriber
 */
class TablePrefixSubscriber implements EventSubscriber
{
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @param $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array( 'loadClassMetadata' );
    }

    /**
     * @param LoadClassMetadataEventArgs $args
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();

        // Get class annotations
        $reader           = new AnnotationReader();
        $classAnnotations = $reader->getClassAnnotations( $classMetadata->getReflectionClass() );

        // Search for WPTable annotation
        $found = false;
        foreach ($classAnnotations as $classAnnotation) {
            if ($classAnnotation instanceof WPTable) {
                $found = true;
                break;
            }
        }

        // Only apply to classes having WPTable annotation
        if (!$found) {
            return;
        }

        // set table prefix
        $prefix = $this->getPrefix( $classMetadata->name, $args->getEntityManager() );

        $classMetadata->setPrimaryTable(
            array(
                'name' => $prefix . $classMetadata->getTableName()
            )
        );

        // set table prefix to associated entity
        // TODO: make sure prefix won't apply to user table
        foreach ($classMetadata->associationMappings as &$mapping) {
            if (isset( $mapping['joinTable'] ) && !empty( $mapping['joinTable'] )) {
                $mapping['joinTable']['name'] = $prefix . $mapping['joinTable']['name'];
            }
        }
    }

    /**
     * Returns the table prefix for entity, with blog ID appened if needed
     *
     * @param string        $entityName fully-qualified class name of the persistent class.
     * @param EntityManager $em
     *
     * @return string
     */
    private function getPrefix($entityName, $em)
    {
        $prefix = $this->prefix;

        // users and usermeta table won't have blog ID appended.
        if ($entityName === 'Mozart\Bundle\UserBundle\Entity\User' ||
            $entityName === 'Mozart\Bundle\UserBundle\Entity\UserMeta'
        ) {
            return $this->prefix;
        }

        if (method_exists( $em, 'getBlogId' )) {
            $blogId = $em->getBlogId();

            // append blog ID to prefix
            if ($blogId > 1) {
                $prefix = $prefix . $blogId . '_';
            }
        }

        return $prefix;
    }
}
