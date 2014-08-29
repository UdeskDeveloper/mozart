<?php
if (!class_exists('VC_Extensions_Parallax')) {

    class VC_Extensions_Parallax {
        function VC_Extensions_Parallax() {
          wpb_map( array(
            "name" => __("Parallax", 'vc_parallax_cq'),
            "base" => "cq_vc_parallax",
            "class" => "wpb_cq_vc_extension",
            "controls" => "full",
            "icon" => "icon-wpb-vc_extension_cq",
            "category" => __('Sike Extensions', 'js_composer'),
            'description' => __('Parallax image and text', 'js_composer' ),
            "params" => array(
              array(
                "type" => "attach_images",
                "heading" => __("Parallax Images:", "vc_parallax_cq"),
                "param_name" => "images",
                "value" => "",
                "description" => __("Select images from media library.", "vc_parallax_cq")
              ),
              array(
                "type" => "textarea_html",
                "holder" => "div",
                "heading" => __("Content text, divide each block with &lt;div class=&#039;parallax-conent&#039;&gt;&lt;/div&gt;:", "vc_parallax_cq"),
                "param_name" => "content",
                "value" => __("<div class='parallax-content'>You have to wrap each text block in a div with class <strong>parallax-conent</strong>. Something like:
                &lt;div class='parallax-content'&gt;content here...&lt;/div&gt;
                You can check to display the image or the text content first in the backend.
                You can customize the text color, background, container width etc in the backend.
                The parallax is disable in mobile, and keep all the image and text readable.
                <a href='http://http://codecanyon.net/user/sike?ref=sike'>Visit my profile</a> for more works. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                <div class='parallax-content'>
                <h4>Text block 2</h4>
                Ecepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </div>
                <div class='parallax-content'>
                <h4>Text block 3</h4>
                qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </div>", "vc_parallax_cq"), "description" => __("Enter content for each block here. Divide each with paragraph (Enter).", "vc_parallax_cq") ),
              array(
                "type" => "textfield",
                "heading" => __("Resize images to this width:", "vc_parallax_cq"),
                "param_name" => "imagewidth",
                "value" => "1280",
                "description" => __("Leave it to be blank if you want to use the original image.", "vc_parallax_cq")
              ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Text color", 'vc_parallax_cq'),
                "param_name" => "textcolor",
                "value" => '',
                "description" => __("", 'vc_parallax_cq')
              ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Text background color", 'vc_parallax_cq'),
                "param_name" => "textbackground",
                "value" => '',
                "description" => __("", 'vc_parallax_cq')
              ),
              array(
                "type" => "textfield",
                "heading" => __("Padding of the content:", "vc_parallax_cq"),
                "param_name" => "padding",
                "value" => "2% 5%",
                "description" => __("The CSS padding for the text content, default is 2% 5%.", "vc_parallax_cq")
              ),
              // array(
              //   "type" => "textfield",
              //   "heading" => __("Margin of thumbnails", "vc_parallax_cq"),
              //   "param_name" => "thumbmargin",
              //   "value" => "",
              //   "description" => __("The CSS margin of the thumbnails, default is 0. You can use it to customize the position of the thumbnails sometime.", "vc_parallax_cq")
              // ),
              array(
                "type" => "dropdown",
                "holder" => "",
                "class" => "vc_parallax_cq",
                "heading" => __("Image on click", "vc_parallax_cq"),
                "param_name" => "onclick",
                "value" => array(__("Do nothing", "vc_parallax_cq") => "link_no", __("Open custom link", "vc_parallax_cq") => "custom_link"),
                "description" => __("Define action for onclick event if needed.", "vc_parallax_cq")
              ),
              array(
                "type" => "exploded_textarea",
                "heading" => __("Custom link for each image", "vc_parallax_cq"),
                "param_name" => "custom_links",
                "description" => __('Enter links for each slide here. Divide links with linebreaks (Enter).', 'vc_parallax_cq'),
                "dependency" => Array('element' => "onclick", 'value' => array('custom_link'))
              ),
              array(
                "type" => "dropdown",
                "heading" => __("How to open the custom link", "vc_parallax_cq"),
                "param_name" => "custom_links_target",
                "description" => __('Select how to open custom links.', 'vc_parallax_cq'),
                "dependency" => Array('element' => "onclick", 'value' => array('custom_link')),
                'value' => array(__("Same window", "vc_parallax_cq") => "_self", __("New window", "vc_parallax_cq") => "_blank")
              ),
              array(
                "type" => "checkbox",
                "holder" => "",
                "class" => "vc_parallax_cq",
                "heading" => __("Display text content first?", 'vc_parallax_cq'),
                "param_name" => "textfirst",
                "value" => array(__("Yes", "vc_parallax_cq") => 'on'),
                "description" => __("You can check this if you want to display the text content in the beginning, otherwise the image will be displayed first.", 'vc_parallax_cq')
              ),
              array(
                "type" => "textfield",
                "heading" => __("Width of the container", "vc_parallax_cq"),
                "param_name" => "containerwidth",
                "value" => "100%",
                "description" => __("The width of the whole container, default is 100%. You can specify it with a smaller value, like 80%, and it will be align center.", "vc_parallax_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Resize images to this width in mobile view:", "vc_parallax_cq"),
                "param_name" => "mobilewidth",
                "value" => "640",
                "description" => __("In mobile view, the parallax is disabled, and we will embed the images in this width. Default is 640.", "vc_parallax_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Extra class name for the container", "vc_parallax_cq"),
                "param_name" => "extra_class",
                "description" => __("You can append extra class to the container.", "vc_parallax_cq")
              )

            )
        ));

        function cq_vc_parallax_func($atts, $content=null) {
          extract( shortcode_atts( array(
            'images' => '',
            'imagewidth' => '',
            'padding' => '',
            'textfirst' => '',
            'textcolor' => '',
            'textbackground' => '',
            'containerwidth' => '',
            'mobilewidth' => '',
            'onclick' => '',
            'custom_links' => '',
            'custom_links_target' => '',
            'extra_class' => ''
          ), $atts ) );


          wp_register_style( 'vc_parallax_cq_style', plugins_url('css/style.css', __FILE__));
          wp_enqueue_style( 'vc_parallax_cq_style' );

          wp_register_script('modernizr', plugins_url('js/modernizr.js', __FILE__));
          wp_enqueue_script('modernizr');
          wp_register_script('imagescroll', plugins_url('js/jquery.imagescroll.min.js', __FILE__), array('jquery'));
          wp_enqueue_script('imagescroll');
          wp_register_script('vc_parallax_cq_script', plugins_url('js/init.min.js', __FILE__), array('jquery', 'modernizr', 'imagescroll'));
          wp_enqueue_script('vc_parallax_cq_script');


          $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content
          $content = str_replace('</div>', '', trim($content));
          $contentarr = explode('<div class="parallax-content">', $content);
          $imagesarr = explode(',', $images);
          $customlinkarr  = explode(',', $custom_links);
          $output = '';
          $output .= '<div class="cq-parallaxcontainer '.$extra_class.'" style="width:'.$containerwidth.';">';
          $i = -1;
          foreach ($imagesarr as $key => $image) {
              $i++;
              if(!isset($contentarr[$i+1])) $contentarr[$i+1] = '';
              if(!isset($customlinkarr[$i])) $customlinkarr[$i] = '';
              if(wp_get_attachment_image_src(trim($image), 'full')){
                  $return_img_arr = wp_get_attachment_image_src(trim($image), 'full');
                  $_height = aq_resize($return_img_arr[0], $imagewidth, null, true, false, true);
                  if($textfirst=="on"){
                      if($contentarr[$i+1]!=""){
                          $output .= '<section class="cq-parallaxsection" style="color:'.$textcolor.';background:'.$textbackground.';padding:'.$padding.';">';
                          $output .= $contentarr[$i+1];
                          $output .= '</section>';
                      }
                      $output .= '<div class="cq-parallaximage" data-image="'.aq_resize($return_img_arr[0], $imagewidth, null, true, true, true).'" data-width="'.$imagewidth.'" data-height="'.$_height[2].'" data-image-mobile="'.aq_resize($return_img_arr[0], $mobilewidth, null, true, true, true).'" data-link="'.$customlinkarr[$i].'" data-target="'.$custom_links_target.'"></div>';
                  }else{
                      $output .= '<div class="cq-parallaximage" data-image="'.aq_resize($return_img_arr[0], $imagewidth, null, true, true, true).'" data-width="'.$imagewidth.'" data-height="'.$_height[2].'" data-image-mobile="'.aq_resize($return_img_arr[0], $mobilewidth, null, true, true, true).'" data-link="'.$customlinkarr[$i].'" data-target="'.$custom_links_target.'"></div>';
                      if($contentarr[$i+1]!=""){
                          $output .= '<section class="cq-parallaxsection" style="color:'.$textcolor.';background:'.$textbackground.';padding:'.$padding.';">';
                          $output .= $contentarr[$i+1];
                          $output .= '</section>';
                      }
                  }

              }
          }
          $output .= '</div>';
          return $output;

        }

        add_shortcode('cq_vc_parallax', 'cq_vc_parallax_func');

      }
  }


}

?>
