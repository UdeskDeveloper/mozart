<?php
if (!class_exists('VC_Extensions_MediumGallery')) {

    class VC_Extensions_MediumGallery {
        function VC_Extensions_MediumGallery() {
          wpb_map( array(
            "name" => __("Medium Gallery", 'vc_mediumgallery_cq'),
            "base" => "cq_vc_mediumgallery",
            "class" => "wpb_cq_vc_extension",
            "controls" => "full",
            "icon" => "icon-wpb-vc_extension_cq",
            "category" => __('Sike Extensions', 'js_composer'),
            'description' => __('Smooth lightbox', 'js_composer' ),
            "params" => array(
              array(
                "type" => "attach_images",
                "heading" => __("Gallery Images:", "vc_mediumgallery_cq"),
                "param_name" => "images",
                "value" => "",
                "description" => __("Select images from media library.", "vc_mediumgallery_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Layout numbers", "vc_mediumgallery_cq"),
                "param_name" => "layoutno",
                "value" => "21343522341234232233",
                "description" => __("Manually set a string of numbers to specify the number of images each row contains (max is 5 in a row). For example, 213 stand for there are 2, 1 and 3 images in each row. Default is a long string with a lot of numbers which suppose you'll add a lot of images, you can customize each number here.", "vc_mediumgallery_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Gutter of each image", "vc_mediumgallery_cq"),
                "param_name" => "gutter",
                "value" => "10px",
                "description" => __("The space between the rows / columns. Default is 10px.", "vc_mediumgallery_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Gallery width", "vc_mediumgallery_cq"),
                "param_name" => "gallerywidth",
                "value" => "",
                "description" => __("Default is 100%.", "vc_mediumgallery_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Resize image to this width in the thumbnail (except the container is larger than the minimal width):", "vc_mediumgallery_cq"),
                "param_name" => "thumbwidth",
                "value" => "",
                "description" => __("Default is displaying the original image. Specify the value only, for example 640.", "vc_mediumgallery_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Minimal width of the thumbnail:", "vc_mediumgallery_cq"),
                "param_name" => "lowreswidth",
                "value" => "",
                "description" => __("Threshold for the low resolution image, if container is larger, swap the high resolution image. Default is 500.", "vc_mediumgallery_cq")
              ),
              array(
                "type" => "textfield",
                "heading" => __("Overlay background", "vc_mediumgallery_cq"),
                "param_name" => "background",
                "value" => "",
                "description" => __("Default is rgba(255,255,255,.85), white transparent overlay. You can change it to other value, like rgba(0,0,0,.85), black transparent overlay. Note it will effect globally in same page.", "vc_mediumgallery_cq")
              ),
              array(
                "type" => "exploded_textarea",
                "holder" => "",
                "class" => "vc_mediumgallery_cq",
                "heading" => __("Title for each image", 'vc_mediumgallery_cq'),
                "param_name" => "titles",
                "value" => __("", 'vc_mediumgallery_cq'),
                "description" => __("Enter title for each image here. Divide each with linebreaks (Enter), leave it to blank if you do not want it.", 'vc_mediumgallery_cq')
              ),
              array(
                "type" => "exploded_textarea",
                "holder" => "",
                "class" => "vc_mediumgallery_cq",
                "heading" => __("Alt for each image", 'vc_mediumgallery_cq'),
                "param_name" => "alts",
                "value" => __("", 'vc_mediumgallery_cq'),
                "description" => __("Enter alt for each image here. Divide each with linebreaks (Enter), leave it to blank if you do not want it.", 'vc_mediumgallery_cq')
              ),
              array(
                "type" => "textfield",
                "heading" => __("Extra class name for the thumbnail", "vc_mediumgallery_cq"),
                "param_name" => "extra_class",
                "description" => __("You can append extra class to the container.", "vc_mediumgallery_cq")
              )

            )
        ));

        function cq_vc_mediumgallery_func($atts, $content=null) {
          extract( shortcode_atts( array(
            'images' => '',
            'gallerywidth' => '',
            'thumbwidth' => '',
            'lowreswidth' => '',
            'background' => '',
            'gutter' => '',
            'extra_class' => '',
            'titles' => '',
            'alts' => '',
            'layoutno' => '21343522341234232233'
          ), $atts ) );


          wp_register_style( 'vc_mediumgallery_cq_style', plugins_url('css/style.css', __FILE__) );
          wp_enqueue_style( 'vc_mediumgallery_cq_style' );

          wp_register_script('photosetgrid', plugins_url('js/jquery.photosetgrid.min.js', __FILE__), array("jquery"));
          wp_enqueue_script('photosetgrid');

          wp_register_script('fluidbox', plugins_url('js/jquery.fluidbox.min.js', __FILE__), array('jquery'));
          wp_enqueue_script('fluidbox');
          wp_register_style( 'fluidbox', plugins_url('css/fluidbox.css', __FILE__) );
          wp_enqueue_style( 'fluidbox' );

          wp_register_script('photosetgrid_init', plugins_url('js/init.min.js', __FILE__), array("jquery", "photosetgrid", "fluidbox"));
          wp_enqueue_script('photosetgrid_init');

          // $aligncenter = $aligncenter == 'center' ? 'center' : '';
          $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content
          $imagesarr = explode(',', $images);
          $titlearr = explode(',', $titles);
          $altarr = explode(',', $alts);
          $i = -1;
          $output = '';
          $output .= '<div class="cq-medium-gallery" data-gallerywidth="'.$gallerywidth.'" data-background="'.$background.'" data-layoutno="'.$layoutno.'" data-lowreswidth="'.$lowreswidth.'" data-gutter="'.$gutter.'" style="margin:0 auto;">';
              foreach ($imagesarr as $key => $image) {
                $i++;
                if(!isset($titlearr[$i])) $titlearr[$i] = '';
                if(!isset($altarr[$i])) $altarr[$i] = '';
                if(wp_get_attachment_image_src(trim($image), 'full')){
                    $return_img_arr = wp_get_attachment_image_src(trim($image), 'full');
                    if($thumbwidth!=""){
                      $output .= '<img src="'.aq_resize($return_img_arr[0], $thumbwidth, null, true, true, true).'" data-highres="'.$return_img_arr[0].'" class="mediumgallery-img '.$extra_class.'" title="'.$titlearr[$i].'" alt="'.$altarr[$i].'" />';
                    }else{
                      $output .= '<img src="'.$return_img_arr[0].'" data-highres="'.$return_img_arr[0].'" class="mediumgallery-img '.$extra_class.'" title="'.$titlearr[$i].'" alt="'.$altarr[$i].'" />';
                    }
                }
              }
          $output .= '</div>';

          return $output;

        }

        add_shortcode('cq_vc_mediumgallery', 'cq_vc_mediumgallery_func');

      }
  }

}

?>
