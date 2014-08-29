<?php
/*
Plugin Name: Visual Composer Extensions All In One
Description: Add Animate Button, Parallax, Image with Arrow, Product Cover Gallery, Medium Gallery, Ribbon, Timeline, Figure Navigation, Animate Text, Stack Gallery, App Mockup gallery, Testimonial Carousel, Font Awesome animation, iHover, Direction-Aware 3D hover gallery, smooth Fluidbox lightbox, 3D hover Profile Card, Depth Modal, Scrolling Notification and Masonry Gallery to your Visual Composer.
Author: Sike
Version: 1.9
Author URI: http://codecanyon.net/user/sike?ref=sike
*/

require_once( 'faanimation/vc-extensions-faanimation.php' );
require_once( 'dagallery/vc-extensions-dagallery.php' );
require_once( 'appmockup/vc-extensions-appmockup.php' );
require_once( 'depthmodal/vc-extensions-depthmodal.php' );
require_once( 'ihover/vc-extensions-ihover.php' );
require_once( 'profilecard/vc-extensions-profilecard.php' );
require_once( 'testimonialcarousel/vc-extensions-testimonialcarousel.php' );
require_once( 'stackgallery/vc-extensions-stackgallery.php' );
require_once( 'animatetext/vc-extensions-animatetext.php' );
require_once( 'figurenav/vc-extensions-figurenav.php' );
require_once( 'timeline/vc-extensions-timeline.php' );
require_once( 'ribbon/vc-extensions-ribbon.php' );
require_once( 'mediumgallery/vc-extensions-mediumgallery.php' );
require_once( 'productcover/vc-extensions-productcover.php' );
require_once( 'imagewitharrow/vc-extensions-imagewitharrow.php' );
require_once( 'parallax/vc-extensions-parallax.php' );
require_once( 'buttons/vc-extensions-cqbutton.php' );

if (!class_exists('VC_Extensions_CQBundle')) {
    class VC_Extensions_CQBundle {
        function VC_Extensions_CQBundle() {
            // function vc_extensions_cqbundle_map_fucn(){
              if(!function_exists('cq_vc_animationfw_func')){
                  $vc_extensions_faanimation = new VC_Extensions_FAanimation();
              }
              if(!function_exists('cq_vc_dagallery_func')) $vc_extensions_dagallery = new VC_Extensions_DAGallery();
              if(!function_exists('cq_vc_appmockup_func')) $vc_extensions_appmockup = new VC_Extensions_AppMockup();
              if(!function_exists('cq_vc_depthmodal_func'))$vc_extensions_depthmodal = new VC_Extensions_DepthModal();
              if(!function_exists('cq_vc_ihover_func')) $vc_extensions_ihover = new VC_Extensions_iHover();
              if(!function_exists('cq_vc_profilecard_func')) $vc_extensions_profilecard = new VC_Extensions_ProfileCard();
              if(!function_exists('cq_vc_testimonialcarousel_func')) $vc_extensions_testimonialcarousel = new VC_Extensions_TestimonialCarousel();
              if(!function_exists('cq_vc_stackgallery_func')) $vc_extensions_stackgallery = new VC_Extensions_StackGallery();
              if(!function_exists('cq_vc_animatetext_func')) $vc_extensions_animatetext = new VC_Extensions_AnimateText();
              if(!function_exists('cq_vc_figurenav_func')) $vc_extensions_figurenav = new VC_Extensions_FigureNav();
              if(!function_exists('cq_vc_timeline_func')) $vc_extensions_timeline = new VC_Extensions_Timeline();
              if(!function_exists('cq_vc_ribbon_func')) $vc_extensions_ribbon = new VC_Extensions_Ribbon();
              if(!function_exists('cq_vc_mediumgallery_func')) $vc_extensions_mediumgallery = new VC_Extensions_MediumGallery();
              if(!function_exists('cq_vc_productcover_func')) $vc_extensions_productcover = new VC_Extensions_ProductCover();
              if(!function_exists('cq_vc_imagewitharrow_func')) $vc_extensions_imagewitharrow = new VC_Extensions_ImageWithArrow();
              if(!function_exists('cq_vc_parallax_func')) $vc_extensions_parallax = new VC_Extensions_Parallax();
              if(!function_exists('cq_vc_cqbutton_func')) $vc_extensions_cqbutton = new VC_Extensions_CQButton();
            // }

            // if(version_compare(WPB_VC_VERSION,  "4.2") >= 0) {
            //   add_action('init', 'vc_extensions_cqbundle_map_fucn');
            // }else{
            //   vc_extensions_cqbundle_map_fucn();
            // }

        }
  }

  function vc_addons_cq_notice(){
    $plugin_data = get_plugin_data(__FILE__);
    echo '
    <div class="updated">
      <p>'.sprintf(__('<strong>%s</strong> requires <strong><a href="http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431?ref=sike" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_addons_cq'), $plugin_data['Name']).'</p>
    </div>';
  }
  if (!defined('ABSPATH')) die('-1');


  function vc_extensions_cqbundle_init(){
    if (!defined('WPB_VC_VERSION')) {add_action('admin_notices', 'vc_addons_cq_notice'); return;}
    if(!function_exists('aq_resize')) require_once('aq_resizer.php');
    wp_register_style( 'vc_extensions_cqbundle_adminicon', plugins_url('css/admin_icon.css', __FILE__) );
    wp_enqueue_style( 'vc_extensions_cqbundle_adminicon' );
    if(class_exists('VC_Extensions_CQBundle')) $vc_extensions_cqbundle = new VC_Extensions_CQBundle();
  }

  add_action('init', 'vc_extensions_cqbundle_init');


}

?>
