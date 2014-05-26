<?php

namespace Mozart\Component\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle as SymfonyBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Bundle extends SymfonyBundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
        if (\Mozart::isWpRunning() === false) {
            return;
        }

//        register_activation_hook($file, array($this, 'onActivation'));
//        register_deactivation_hook($file, array($this, 'onDeactivation'));
//        register_uninstall_hook($file, array($this, 'onUninstall'));

        $this->addFilters();
        $this->addActions();
    }

    public function addFilters()
    {

    }

    public function addActions()
    {

    }

    /*
     * @see register_activation_hook()
     */
    public function onActivation()
    {

    }

    /*
     * @see register_deactivation_hook()
     */
    public function onDeactivation()
    {

    }

    /*
     * @see register_uninstall_hook()
     */
    public function onUninstall()
    {

    }

}
