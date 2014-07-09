<?php

namespace Mozart\Bundle\BlogBundle\EventListener;

use  Mozart\Bundle\BlogBundle\Event\SwitchBlogEvent;
use  Mozart\Bundle\PostBundle\Model\AttachmentManager;
use  Mozart\Bundle\CommentBundle\Model\CommentManager;
use  Mozart\Bundle\OptionBundle\Model\OptionManager;
use  Mozart\Bundle\PostBundle\Model\PostManager;
use  Mozart\Bundle\PostBundle\Model\PostMetaManager;
use  Mozart\Bundle\TaxonomyBundle\Model\TermManager;
use  Mozart\Bundle\NucleusBundle\Twig\Extension\WordpressExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SwitchBlogListener
 *
 * @package Mozart\Bundle\BlogBundle\EventListener
 */
class SwitchBlogListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param SwitchBlogEvent $event
     */
    public function onSwitchBlog(SwitchBlogEvent $event)
    {
        $this->updateModelManagerServices( $event );
        $this->updateWordpressTwigExtension( $event );
    }

    /**
     * @param SwitchBlogEvent $event
     */
    private function updateModelManagerServices(SwitchBlogEvent $event)
    {
        $em = $event->getBlog()->getEntityManager();

        $this->container->set( 'mozart.option.manager', new OptionManager( $em ) );
        $this->container->set( 'mozart_post.manager', new PostManager( $em ) );
        $this->container->set( 'mozart_post.meta.manager', new PostMetaManager( $em ) );
        $this->container->set( 'mozart_post.attachment_manager', new AttachmentManager( $em ) );
        $this->container->set( 'mozart_taxonomy.term.manager', new TermManager( $em ) );
        $this->container->set( 'mozart_comment.manager', new CommentManager( $em ) );
    }

    /**
     * @param SwitchBlogEvent $event
     */
    private function updateWordpressTwigExtension(SwitchBlogEvent $event)
    {
        /** @var $extension WordpressExtension */
        $extension = $this->container->get( 'mozart_nucleus.twig.wordpress' );
        $extension->reloadManagers();
    }
}
