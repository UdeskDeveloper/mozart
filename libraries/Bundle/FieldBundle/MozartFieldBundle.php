<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\FieldBundle;

use Mozart\Bundle\FieldBundle\ACF\Extension\NavMenu;
use Mozart\Bundle\FieldBundle\ACF\Extension\StarRating;
use Mozart\Bundle\FieldBundle\ACF\Extension\Url;
use Mozart\Bundle\FieldBundle\ACF\Extension\UserRole;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MozartFieldBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build( $container );
    }

    public function boot()
    {
        parent::boot();
        add_action( 'acf/include_field_types', array( $this, 'registerAdvancedCustomFields' ) );
    }

    public function registerAdvancedCustomFields()
    {
        // TODO: move this MenuBundle and register with FieldBundle's DIExtension
        new NavMenu();
        new StarRating();
        new UserRole();
        new Url();
    }
}
