<?php

namespace Mozart\Bundle\BlogBundle\Event;

use  Mozart\Bundle\PostBundle\Model\AttachmentManager;
use  Mozart\Bundle\CommentBundle\Model\CommentManager;
use  Mozart\Bundle\ConfigBundle\Model\OptionManager;
use  Mozart\Bundle\PostBundle\Model\PostManager;
use  Mozart\Bundle\PostBundle\Model\PostMetaManager;
use  Mozart\Bundle\TaxonomyBundle\Model\TermManager;
use  Mozart\Bundle\NucleusBundle\Twig\Extension\WordpressExtension;
use  Symfony\Component\DependencyInjection\ContainerInterface;

class SwitchBlogEventListener
{
    /**
     * @var ContainerInterface
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
     * @param BlogEvent $event
     */
    public function onSwitchBlog(BlogEvent $event)
    {
        $this->updateModelManagerServices( $event );
        $this->updateWordpressTwigExtension( $event );
    }

    /**
     * @param BlogEvent $event
     */
    private function updateModelManagerServices(BlogEvent $event)
    {
        $em = $event->getBlog()->getEntityManager();

        $this->container->set( 'config.manager', new OptionManager( $em ) );
        $this->container->set( 'mozart.post.manager', new PostManager( $em ) );
        $this->container->set( 'mozart.post.meta.manager', new PostMetaManager( $em ) );
        $this->container->set( 'mozart.post.attachment_manager', new AttachmentManager( $em ) );
        $this->container->set( 'mozart.taxonomy.term.manager', new TermManager( $em ) );
        $this->container->set( 'mozart.comment.manager', new CommentManager( $em ) );
    }

    /**
     * @param BlogEvent $event
     */
    private function updateWordpressTwigExtension(BlogEvent $event)
    {
        /** @var $extension WordpressExtension */
        $extension = $this->container->get( 'mozart.twig.wordpress' );
        $extension->reloadManagers();
    }
}
