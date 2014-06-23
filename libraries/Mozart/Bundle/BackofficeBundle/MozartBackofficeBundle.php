<?php

namespace Mozart\Bundle\BackofficeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mozart\Bundle\BackofficeBundle\DependencyInjection\Compiler\ReduxSectionsCompilerPass;

/**
 * Class MozartBackofficeBundle
 *
 * @package Mozart\Bundle\BackofficeBundle
 */
class MozartBackofficeBundle extends Bundle
{
    /**
     * @var Redux\Configuration
     */
    protected $optionsManager;
    /**
     * @var Redux\Extensions\Configuration
     */
    protected $optionsExtensionManager;

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ReduxSectionsCompilerPass);
    }

    /**
     * Boot the bundle
     */
    public function boot()
    {
        if (\Mozart::isWpRunning() === false) {
            return;
        }

        $this->optionsManager = new Redux\Configuration();
        $this->optionsManager->init(array(), $this->container);

        $this->optionsExtensionManager = new Redux\Extensions\Configuration();
        $this->optionsExtensionManager->init();

        $this->redux = new Redux\Redux();
    }
}
