<?php
if (!class_exists('VC_Extensions_Timeline')) {

    class VC_Extensions_Timeline {
        function VC_Extensions_Timeline() {
          wpb_map( array(
            "name" => __("Timeline", 'vc_timeline_cq'),
            "base" => "cq_vc_timeline",
            "class" => "wpb_cq_vc_extension",
            "controls" => "full",
            "icon" => "icon-wpb-vc_extension_cq",
            "category" => __('Sike Extensions', 'js_composer'),
            'description' => __('Vertical timeline', 'js_composer' ),
            "params" => array(
              array(
                "type" => "textarea_html",
                "holder" => "div",
                "heading" => __("Content text", "vc_timeline_cq"),
                "param_name" => "content",
                "value" => __("Here is the timeline content, you can customize it in the backend editor, you can put other Visual Composer element here too, like a button.[vc_button2 title='Text on the button' style='rounded' color='blue' size='md' link='url:http%3A%2F%2Fcodecanyon.net%2Fuser%2Fsike%3Fref%3Dsike||'] \n\n The timeline avatar support font awesome icon or custom image. It's responsive and retina ready.[vc_button2 title='My Profile' style='outlined' color='pink' size='md' link='url:http%3A%2F%2Fcodecanyon.net%2Fuser%2Fsike%3Fref%3Dsike||'] \n\n Title is optional. You can select the background color, font color, customize the border color, timeline pattern background etc in the backend.[vc_button2 title='More' style='square_outlined' color='white' size='md' link='url:http%3A%2F%2Fcodecanyon.net%2Fuser%2Fsike%3Fref%3Dsike||'] \n\n You can specify background color for each block, support green, blue, purple, brown, maroon, lavender, black and white(leave it to blank).", "vc_timeline_cq"),
                "description" => __("Enter content for each block here. Divide each with paragraph (Enter).", "vc_timeline_cq")
              ),
              array(
                "type" => "exploded_textarea",
                "holder" => "",
                "class" => "vc_timeline_cq",
                "heading" => __("Title for each block", 'vc_timeline_cq'),
                "param_name" => "titles",
                "value" => __("Responsive,Retina Ready,,Avatar support icon or image", 'vc_timeline_cq'),
                "description" => __("Enter title for each block here. Divide each with linebreaks (Enter), leave it to blank if you do not want it.", 'vc_timeline_cq')
              ),
              array(
                "type" => "dropdown",
                "holder" => "",
                "class" => "vc_timeline_cq",
                "heading" => __("Display the timeline avatar in:", "vc_timeline_cq"),
                "param_name" => "avatarformat",
                "value" => array(__("icon", "vc_timeline_cq") => "icon", __("image", "vc_timeline_cq") => "image"),
                "description" => __("", "vc_timeline_cq")
              ),
              array(
                "type" => "attach_images",
                "heading" => __("Timeline avatar", "vc_timeline_cq"),
                "param_name" => "avatarimgs",
                "value" => "",
                "description" => __("Select images from media library.", "vc_timeline_cq"),
                "dependency" => Array('element' => "avatarformat", 'value' => array('image'))
              ),
              array(
                "type" => "exploded_textarea",
                "holder" => "",
                "class" => "vc_timeline_cq",
                "heading" => __("Icon for timeline avatar", 'vc_timeline_cq'),
                "param_name" => "avataricons",
                "value" => __("fa-twitter,fa-map-marker,fa-dribbble,fa-microphone", 'vc_timeline_cq'),
                "description" => __("Enter (<a href='http://fortawesome.github.io/Font-Awesome/icons/' target='_blank'>font-awesome</a>) icon for each block here. Divide each with linebreaks (Enter).", 'vc_timeline_cq'),
                "dependency" => Array('element' => "avatarformat", 'value' => array('icon'))
              ),
              array(
                "type" => "exploded_textarea",
                "holder" => "",
                "class" => "vc_timeline_cq",
                "heading" => __("Background color for each avatar", 'vc_timeline_cq'),
                "param_name" => "avatarbackgrounds",
                "value" => __("#00ACED,#3B5998,#E14782,#E14107", 'vc_timeline_cq'),
                "description" => __("Enter background color for each avatar here. Divide each with linebreaks (Enter), default (blank) is #999.", 'vc_timeline_cq')
              ),
              array(
                "type" => "exploded_textarea",
                "holder" => "",
                "class" => "vc_timeline_cq",
                "heading" => __("Date label for each block", 'vc_timeline_cq'),
                "param_name" => "dates",
                "value" => __("Jan 14 2013,Feb 14,Join Dribbble 2014,Apr 18 1988", 'vc_timeline_cq'),
                "description" => __("Enter date for each block here. Divide each with linebreaks (Enter).", 'vc_timeline_cq')
              ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Container background color", 'vc_timeline_cq'),
                "param_name" => "backgroundcolor",
                "value" => '#EFEFEF',
                "description" => __("Specify the background color of container here, default is #EFEFEF.", 'vc_timeline_cq')
              ),
              array(
                "type" => "attach_image",
                "heading" => __("Container background image", "vc_timeline_cq"),
                "param_name" => "backgroundimg",
                "value" => "",
                "description" => __("Select background from media library.", "vc_timeline_cq")
              ),
              array(
                "type" => "dropdown",
                "holder" => "",
                "class" => "vc_timeline_cq",
                "heading" => __("Container background image repeat", "vc_timeline_cq"),
                "param_name" => "repeat",
                "value" => array(__("repeat", "vc_timeline_cq") => "repeat", __("no-repeat", "vc_timeline_cq") => "no-repeat", __("repeat-x", "vc_timeline_cq") => "repeat-x", __("repeat-y", "vc_timeline_cq") => "repeat-y"),
                "description" => __("", "vc_timeline_cq")
              ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Block background color", 'vc_timeline_cq'),
                "param_name" => "itembackground",
                "value" => '',
                "description" => __("Specify the default background color every block here, default is transparent.", 'vc_timeline_cq')
              ),
              array(
                "type" => "exploded_textarea",
                "holder" => "",
                "class" => "vc_timeline_cq",
                "heading" => __("Background color for each block", 'vc_timeline_cq'),
                "param_name" => "blockcolors",
                "value" => __("lavender,,maroon,,", 'vc_timeline_cq'),
                "description" => __("Specify background color for each block here, now only support <strong>green,blue,purple,brown,maroon,lavender,black</strong>. Leave it to blank will be white. Divide each with linebreaks (Enter).", 'vc_timeline_cq')
              ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Content font color", 'vc_timeline_cq'),
                "param_name" => "itemfontcolor",
                "value" => '',
                "description" => __("Specify the font color of the content.", 'vc_timeline_cq')
              ),
              // array(
              //   "type" => "attach_image",
              //   "heading" => __("Block background image", "vc_timeline_cq"),
              //   "param_name" => "itembgimage",
              //   "value" => "",
              //   "description" => __("Select background from media library.", "vc_timeline_cq")
              // ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Default avatar background color", 'vc_timeline_cq'),
                "param_name" => "defaultavatarbg",
                "value" => '',
                "description" => __("Specify the default background color of container here, default is #999.", 'vc_timeline_cq')
              ),
             array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Timeline content font color", 'vc_timeline_cq'),
                "param_name" => "contentcolor",
                "value" => '',
                "description" => __("Specify the color of the timeline content.", 'vc_timeline_cq')
              ),
             array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Date label color", 'vc_timeline_cq'),
                "param_name" => "datecolor",
                "value" => '',
                "description" => __("Specify the color of the date label.", 'vc_timeline_cq')
              ),
             array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Timeline border color", 'vc_timeline_cq'),
                "param_name" => "bordercolor",
                "value" => '',
                "description" => __("Specify the color of the date label.", 'vc_timeline_cq')
              ),
             array(
                "type" => "textfield",
                "heading" => __("Responsive trigger at width:", "vc_timeline_cq"),
                "param_name" => "responsivewidth",
                "value" => "768",
                "description" => __("Trigger the responsive if the screen is less than this width.", "vc_timeline_cq")
              ),
             array(
                "type" => "textfield",
                "heading" => __("Padding of the container:", "vc_timeline_cq"),
                "param_name" => "padding",
                "value" => "",
                "description" => __("Specify the padding of the container, default is 2em.", "vc_timeline_cq")
              ),
             array(
                "type" => "textfield",
                "heading" => __("Margin of the container:", "vc_timeline_cq"),
                "param_name" => "margin",
                "value" => "",
                "description" => __("Specify the margin of the container, default is 2em auto.", "vc_timeline_cq")
              ),
             array(
                "type" => "textfield",
                "heading" => __("Width of the container:", "vc_timeline_cq"),
                "param_name" => "containerwidth",
                "value" => "",
                "description" => __("Specify the width of the container, default is 100%.", "vc_timeline_cq")
              ),
             array(
                "type" => "textfield",
                "heading" => __("Extra class name for the thumbnail", "vc_timeline_cq"),
                "param_name" => "extra_class",
                "description" => __("You can append extra class to the container.", "vc_timeline_cq")
              )

            )
        ));

        function cq_vc_timeline_func($atts, $content=null) {
          extract( shortcode_atts( array(
            'titles' => '',
            'dates' => '',
            'buttons' => '',
            'avatarformat' => '',
            'avatarimgs' => '',
            'avataricons' => '',
            'backgroundimg' => '',
            'backgroundcolor' => '',
            'repeat' => 'repeat',
            'datecolor' => '',
            'contentcolor' => '',
            'blockcolors' => '',
            'avatarbackgrounds' => '',
            'bordercolor' => '',
            'defaultavatarbg' => '',
            'responsivewidth' => '768',
            'extra_class' => '',
            'margin' => '',
            'padding' => '',
            'containerwidth' => '',
            'string' => 'off'
          ), $atts ) );

          if($avatarformat=="icon"){
            wp_register_style( 'font-awesome', plugins_url('../faanimation/css/font-awesome.min.css', __FILE__) );
            wp_enqueue_style( 'font-awesome' );
          }

          wp_register_style( 'vc_timeline_cq_style', plugins_url('css/style.css', __FILE__) );
          wp_enqueue_style( 'vc_timeline_cq_style' );

          wp_register_script('vc_timeline_cq_script', plugins_url('js/jquery.timeline.min.js', __FILE__), array("jquery"));
          wp_enqueue_script('vc_timeline_cq_script');


          // $aligncenter = $aligncenter == 'center' ? 'center' : '';
          $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content
          $output = '';
          $content = str_replace('</p>', '', trim($content));
          $contentarr = explode('<p>', $content);
          $titlearr = explode(',', $titles);
          $datearr = explode(',', $dates);
          $buttonarr = explode(',', $buttons);
          $avatariconarr = explode(',', $avataricons);
          $avatarimgarr = explode(',', $avatarimgs);
          $blockcolorarr = explode(',', $blockcolors);
          $avatarbackgroundarr = explode(',', $avatarbackgrounds);

          $backgroundimgurl = wp_get_attachment_image_src($backgroundimg, 'full');


          $output .= '<section data-datecolor="'.$datecolor.'" data-responsivewidth="'.$responsivewidth.'" style="width:'.$containerwidth.';margin:'.$margin.';padding:'.$padding.';background:'.$backgroundcolor.'  url('.$backgroundimgurl[0].') '.$repeat.'" class="cq-timeline-container '.$extra_class.'">';
          $output .= '<div class="cq-timeline-border" style="background:'.$bordercolor.';"></div>';
          $i = -1;
          foreach ($contentarr as $key => $value) {
              $i++;
              if(!isset($contentarr[$i])) $contentarr[$i] = '';
              if(!isset($titlearr[$i])) $titlearr[$i] = '';
              if(!isset($buttonarr[$i])) $buttonarr[$i] = '';
              if(!isset($datearr[$i])) $datearr[$i] = '';
              if(!isset($avatariconarr[$i])) $avatariconarr[$i] = '';
              if(!isset($avatarimgarr[$i])) $avatarimgarr[$i] = '';
              if(!isset($blockcolorarr[$i])) $blockcolorarr[$i] = '';
              if(!isset($avatarbackgroundarr[$i])) $avatarbackgroundarr[$i] = $defaultavatarbg;

              $output .= '<div class="cd-timeline-block">';
              $output .= '<div class="cd-timeline-img" style="background:'.$avatarbackgroundarr[$i].';">';
              if($avatarformat=="icon"){
                $output .= '<i class="fa fa-lg '.$avatariconarr[$i].'"></i>';
              }else{
                $return_img_arr = wp_get_attachment_image_src(trim($avatarimgarr[$i]), 'full');
                $output .= '<img src="'.aq_resize($return_img_arr[0], 120, 120, true, true, true).'" alt="">';
              }
              $output .= '</div>';
              // var_dump($i, $buttonarr[$i]);
              $output .= '<div class="cd-timeline-content" data-color="'.$blockcolorarr[$i].'">';
              if($titlearr[$i]!=' '&&$titlearr[$i]!='') $output .= '<h4>'.$titlearr[$i].'</h4>';
              if($contentarr[$i]!=' '&&$contentarr[$i]!='') $output .= '<p>'.$contentarr[$i].'</p>';
              if($datearr[$i]!=' '&&$datearr[$i]!='') $output .= '<span style="color:'.$datecolor.';" class="cd-date">'.$datearr[$i].'</span>';
              $output .= '</div>';

              // <span class="cd-read-more">'.$buttonarr[$i].'</span>

              $output .= '</div>';
          }
          $output .= '</section>';

          return $output;

        }

        add_shortcode('cq_vc_timeline', 'cq_vc_timeline_func');

      }
  }

}

?>
