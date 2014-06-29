<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\OptionBundle;

use Mozart\Bundle\OptionBundle\DependencyInjection\Compiler\ReduxSectionsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class MozartOptionBundle
 *
 * @package Mozart\Bundle\OptionBundle
 */
class MozartOptionBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build( ContainerBuilder $container )
    {
        parent::build( $container );
        $container->addCompilerPass( new ReduxSectionsCompilerPass );
    }

    /**
     *
     */
    public function boot()
    {
        $this->runRedux();
    }

    /**
     *
     */
    protected function runRedux()
    {
        //  var_dump($this->container->get( 'redux.sectionmanager' )->getSections());
        $this->container->get( 'redux.configuration' )->init(
            array(
                'sections' => $this->container->get( 'redux.sectionmanager' )->getSections()
            )
        );

        $this->container->get( 'redux.extensions.configuration' )->init();
        $this->container->get( 'redux' )->init();
    }

} 