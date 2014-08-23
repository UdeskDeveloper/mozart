<?php

namespace Mozart\Bundle\NucleusBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mozart\Bundle\NucleusBundle\DependencyInjection\Security\Factory\WordpressFactory;
use Doctrine\DBAL\Types\Type;
use Mozart\Bundle\NucleusBundle\Types\WordpressMetaType;
use Mozart\Bundle\NucleusBundle\Types\WordpressIdType;

/**
 * Class MozartNucleusBundle
 *
 * @package Mozart\Bundle\NucleusBundle
 */
class MozartNucleusBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build( $container );

        // Security
        $container->getExtension( 'security' )->addSecurityListenerFactory( new WordpressFactory() );
    }

    /**
     *
     */
    public function boot()
    {
        parent::boot();

        if (!Type::hasType( WordpressMetaType::NAME )) {
            Type::addType( WordpressMetaType::NAME, 'Mozart\Bundle\NucleusBundle\Types\WordpressMetaType' );
        }

        if (!Type::hasType( WordpressIdType::NAME )) {
            Type::addType( WordpressIdType::NAME, 'Mozart\Bundle\NucleusBundle\Types\WordpressIdType' );
        }

//
//        Action::listen('init', $this, 'init')->dispatch();
//        Action::listen('generate_rewrite_rules', $this, 'rewrite')->dispatch();
//
//        if (Application::get('rewrite') && !is_admin()) {
//            add_filter('script_loader_src', array($this, 'rewriteAssetUrl'));
//            add_filter('style_loader_src', array($this, 'rewriteAssetUrl'));
//            add_filter('stylesheet_directory_uri', array($this, 'rewriteAssetUrl'));
//            add_filter('template_directory_uri', array($this, 'rewriteAssetUrl'));
//            add_filter('bloginfo', array($this, 'rewriteAssetUrl'));
//            add_filter('plugins_url', array($this, 'rewriteAssetUrl'));
//        }
//
//        // Admin actions
//        Action::listen('admin_head', $this, 'adminHead')->dispatch();
    }

    /**
     * Run a series of methods at WP init hook
     *
     * @return void
     */
    public function init()
    {
//        if (Application::get('cleanup')) $this->cleanup();
//
//        $access = Application::get('access');
//        if (!empty($access) && is_array($access)) $this->restrict();
    }
}
