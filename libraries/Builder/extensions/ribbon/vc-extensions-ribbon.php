<?php
if (!class_exists('VC_Extensions_Ribbon')) {

    class VC_Extensions_Ribbon {
        function VC_Extensions_Ribbon() {
          wpb_map( array(
            "name" => __("Ribbon", 'vc_ribbon_cq'),
            "base" => "cq_vc_ribbon",
            "class" => "wpb_cq_vc_extension",
            "controls" => "full",
            "icon" => "icon-wpb-vc_extension_cq",
            "category" => __('Sike Extensions', 'js_composer'),
            'description' => __('Image with ribbon', 'js_composer' ),
            "params" => array(
              array(
                "type" => "attach_image",
                "heading" => __("Image", "vc_ribbon_cq"),
                "param_name" => "image",
                "value" => "",
                "description" => __("Select image from media library.", "vc_ribbon_cq")
              ),
              array(
                "type" => "textarea_html",
                "holder" => "div",
                "heading" => __("Content text", "vc_ribbon_cq"),
                "param_name" => "content",
                "value" => __("Here is the optional ribbon content, you can customize it in the backend editor. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ratione, vel commodi neque.", "vc_ribbon_cq"),
                "description" => __("Enter content for each block here. Divide each with paragraph (Enter).", "vc_ribbon_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Ribbon text", "vc_ribbon_cq"),
                "param_name" => "label",
                "value" => "label",
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "dropdown",
                "holder" => "",
                "class" => "vc_ribbon_cq",
                "heading" => __("Ribbon position", "vc_ribbon_cq"),
                "param_name" => "position",
                "value" => array(__("left (rotate)", "vc_ribbon_cq") => "left", __("right (rotate)", "vc_ribbon_cq") => "right", __("left (no rotate)", "vc_ribbon_cq") => "left1", __("right (no rotate)", "vc_ribbon_cq") => "right1"),
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "dropdown",
                "holder" => "",
                "class" => "vc_ribbon_cq",
                "heading" => __("Open image as:", "vc_ribbon_cq"),
                "param_name" => "openimageas",
                "value" => array(__("fluidbox", "vc_ribbon_cq") => "fluidbox", __("link", "vc_ribbon_cq") => "link", __("none", "vc_ribbon_cq") => "none"),
                "description" => __("", "vc_ribbon_cq")
              ),

              array(
                "type" => "textfield",
                "heading" => __("Image link", "vc_ribbon_cq"),
                "param_name" => "imagelink",
                "value" => "",
                "dependency" => Array('element' => "openimageas", 'value' => array('link')),
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "dropdown",
                "heading" => __("Image link target", "vc_ribbon_cq"),
                "param_name" => "image_link_target",
                "description" => __('Select where to open image link.', 'vc_ribbon_cq'),
                "dependency" => Array('element' => "openimageas", 'value' => array('link')),
                'value' => array(__("Same window", "vc_ribbon_cq") => "_self", __("New window", "vc_ribbon_cq") => "_blank")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Ribbon link", "vc_ribbon_cq"),
                "param_name" => "ribbonlink",
                "value" => "",
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "dropdown",
                "heading" => __("Ribbon link target", "vc_ribbon_cq"),
                "param_name" => "ribbon_link_target",
                "description" => __('Select where to open ribbon link.', 'vc_ribbon_cq'),
                'value' => array(__("Same window", "vc_ribbon_cq") => "_self", __("New window", "vc_ribbon_cq") => "_blank")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Ribbon width", "vc_ribbon_cq"),
                "param_name" => "ribbonwidth",
                "value" => "",
                "dependency" => Array('element' => "position", 'value' => array('left', 'right')),
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Ribbon top", "vc_ribbon_cq"),
                "param_name" => "ribbontop_norotate",
                "value" => "10px",
                "dependency" => Array('element' => "position", 'value' => array('left1', 'right1')),
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Ribbon bottom", "vc_ribbon_cq"),
                "param_name" => "ribbonbottom_norotate",
                "value" => "auto",
                "dependency" => Array('element' => "position", 'value' => array('left1', 'right1')),
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Ribbon top", "vc_ribbon_cq"),
                "param_name" => "ribbontop1",
                "value" => "15px",
                "dependency" => Array('element' => "position", 'value' => array('left')),
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Ribbon left", "vc_ribbon_cq"),
                "param_name" => "ribbonleft1",
                "value" => "-30px",
                "dependency" => Array('element' => "position", 'value' => array('left')),
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Ribbon top", "vc_ribbon_cq"),
                "param_name" => "ribbontop2",
                "value" => "16px",
                "dependency" => Array('element' => "position", 'value' => array('right')),
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Ribbon left", "vc_ribbon_cq"),
                "param_name" => "ribbonleft2",
                "value" => "10px",
                "dependency" => Array('element' => "position", 'value' => array('right')),
                "description" => __("", "vc_ribbon_cq")
              ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Ribbon background color", 'vc_ribbon_cq'),
                "param_name" => "ribbonbg",
                "value" => "#f04256",
                "description" => __("Specify the color of the date label.", 'vc_ribbon_cq')
              ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Ribbon font color", 'vc_ribbon_cq'),
                "param_name" => "ribboncolor",
                "value" => "",
                "description" => __("Specify the color of the date label.", 'vc_ribbon_cq')
              ),
              // array(
              //   "type" => "colorpicker",
              //   "holder" => "div",
              //   "class" => "",
              //   "heading" => __("Ribbon background color start", 'vc_ribbon_cq'),
              //   "param_name" => "colorstart",
              //   "value" => "#f04256",
              //   "description" => __("Specify the color of the date label.", 'vc_ribbon_cq')
              // ),
              // array(
              //   "type" => "colorpicker",
              //   "holder" => "div",
              //   "class" => "",
              //   "heading" => __("Ribbon background color end", 'vc_ribbon_cq'),
              //   "param_name" => "colorend",
              //   "value" => "#bd0f23",
              //   "description" => __("Specify the color of the date label.", 'vc_ribbon_cq')
              // ),
              array(
                "type" => "textfield",
                "heading" => __("Container max width", "vc_ribbon_cq"),
                "param_name" => "width",
                "value" => "",
                "description" => __("The container is 100% by default, you can specify a max-width for it here.", "vc_ribbon_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Extra class name for the container", "vc_ribbon_cq"),
                "param_name" => "extra_class",
                "description" => __("You can append extra class to the container.", "vc_ribbon_cq")
              )

            )
        ));

        function cq_vc_ribbon_func($atts, $content=null) {
          extract( shortcode_atts( array(
            'image' => '',
            'width' => '',
            'label' => 'label',
            'ribbonwidth' => '',
            'position' => '',
            'ribbontop1' => '',
            'ribbonleft1' => '',
            'ribbontop2' => '',
            'ribbonleft2' => '',
            'ribbontop_norotate' => '',
            'ribbonbottom_norotate' => '',
            // 'colorstart' => '',
            // 'colorend' => '',
            'ribbonbg' => '',
            'ribboncolor' => '',
            'openimageas' => 'fluidbox',
            'imagelink' => '',
            'ribbonlink' => '',
            'image_link_target' => '',
            'ribbon_link_target' => '',
            'extra_class' => ''
          ), $atts ) );

          // if($avatarformat=="icon"){
          //   wp_register_style( 'font-awesome', plugins_url('../faanimation/css/font-awesome.min.css', __FILE__) );
          //   wp_enqueue_style( 'font-awesome' );
          // }

          wp_register_style( 'vc_ribbon_cq_style', plugins_url('css/style.css', __FILE__) );
          wp_enqueue_style( 'vc_ribbon_cq_style' );

          wp_register_script('vc_ribbon_cq_script', plugins_url('js/jquery.ribbon.min.js', __FILE__), array("jquery"));
          wp_enqueue_script('vc_ribbon_cq_script');

          if($openimageas=="fluidbox"){
              wp_register_script('fluidbox', plugins_url('../mediumgallery/js/jquery.fluidbox.min.js', __FILE__), array('jquery'));
              wp_enqueue_script('fluidbox');
              wp_enqueue_script('ribbonimage_init', plugins_url('js/ribbonimage_init.js', __FILE__), array('jquery'));
              wp_register_style( 'fluidbox', plugins_url('../mediumgallery/css/fluidbox.css', __FILE__) );
              wp_enqueue_style( 'fluidbox' );
          }

          // $aligncenter = $aligncenter == 'center' ? 'center' : '';
          $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content
          $imageurl = wp_get_attachment_image_src($image, 'full');
          $output = '';
          if($position=="left"){
             $ribbontop = $ribbontop1;
             $ribbonleft = $ribbonleft1;
          }else{
             $ribbontop = $ribbontop2;
             $ribbonleft = $ribbonleft2;
          }

          $output .= '<div class="cq-ribbon-container '.$extra_class.'" style="max-width:'.$width.';" data-ribbonwidth="'.$ribbonwidth.'" data-ribbontop="'.$ribbontop.'" data-ribbonleft="'.$ribbonleft.'"  data-ribboncolor="'.$ribboncolor.'">';
          if($position=="left"||$position=="right"){
              if($ribbonlink!=""){
                $output .= '<div class="cq-ribbon '.$position.'"><div class="cq-ribbon-bg" style="background:'.$ribbonbg.';color:'.$ribboncolor.';"><a href="'.$ribbonlink.'" target="'.$ribbon_link_target.'">'.$label.'</a></div></div>';
              }else{
                $output .= '<div class="cq-ribbon '.$position.'"><div class="cq-ribbon-bg" style="background:'.$ribbonbg.';color:'.$ribboncolor.';">'.$label.'</div></div>';
              }
          }else{
            if($position=="left1"){
              if($ribbonlink!=""){
                $output .= '<div style="background:'.$ribbonbg.';color:'.$ribboncolor.';
background:'.$ribbonbg.';color:'.$ribboncolor.';top:'.$ribbontop_norotate.';bottom:'.$ribbonbottom_norotate.'" class="cq-ribbon3"><a href="'.$ribbonlink.'" target="'.$ribbon_link_target.'">'.$label.'</a><div class="arrow" style="border-color:transparent '.$ribbonbg.'"></div></div>';
              }else{
                $output .= '<div style="background:'.$ribbonbg.';color:'.$ribboncolor.';
background:'.$ribbonbg.';color:'.$ribboncolor.';top:'.$ribbontop_norotate.';bottom:'.$ribbonbottom_norotate.'" class="cq-ribbon3">'.$label.'<div class="arrow" style="border-color:transparent '.$ribbonbg.'"></div></div>';
              }
            }else{
              if($ribbonlink!=""){
                $output .= '<div style="background:'.$ribbonbg.';color:'.$ribboncolor.';
background:'.$ribbonbg.';color:'.$ribboncolor.';top:'.$ribbontop_norotate.';bottom:'.$ribbonbottom_norotate.'" class="cq-ribbon4"><a href="'.$ribbonlink.'" target="'.$ribbon_link_target.'">'.$label.'</a><div class="arrow" style="border-color:transparent '.$ribbonbg.'"></div></div>';
              }else{
                $output .= '<div style="background:'.$ribbonbg.';color:'.$ribboncolor.';
background:'.$ribbonbg.';color:'.$ribboncolor.';top:'.$ribbontop_norotate.';bottom:'.$ribbonbottom_norotate.'" class="cq-ribbon4">'.$label.'<div class="arrow" style="border-color:transparent '.$ribbonbg.'"></div></div>';
              }
            }
          }
          $output .= '<div class="cq-ribbon-content">';
          if($imageurl[0]!='') {
            if($openimageas=="fluidbox"){
              $output .= '<a href="'.$imageurl[0].'" class="ribbon-image">';
              $output .= '<img src="'.$imageurl[0].'" alt="image" />';
              $output .= '</a>';
            }else if($openimageas=="link"){
              if($imagelink!=""){
                $output .= '<a href="'.$imagelink.'" target="'.$image_link_target.'">';
                $output .= '<img src="'.$imageurl[0].'" alt="image" />';
                $output .= '</a>';
              }
            }else{
              $output .= '<img src="'.$imageurl[0].'" alt="image" />';
            }
          }
          if($content!="")$output .= '<p>'.$content.'</p>';
          $output .= '</div>';
          $output .= '</div>';

          // $output .= '<div class="cq-ribbon2"> <strong class="ribbon-content">Everybody loves ribbons</strong> </div>';

          return $output;

        }

        add_shortcode('cq_vc_ribbon', 'cq_vc_ribbon_func');

      }
  }

}

?>
