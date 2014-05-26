<?php

namespace Mozart\RealEstate\PropertyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mozart\RealEstate\PropertyBundle\Submission\MetaBox as Submission_MetaBox;

class PropertyController extends Controller
{

    public function ajaxPropertyEnquireAction()
    {
        $result = array();
        $result['success'] = false;
        $result['message'] = 'Error! Please try again';
        if (is_array($_POST) && !empty($_POST['post_id'])) {
            $agents = get_post_meta($_POST['post_id'], '_property_agents', true);
            $author_email = get_the_author_meta('user_email');

            if (is_array($agents) || !empty($author_email)) {
                $message = '';

                if (!parameter('property', 'fields', 'hide_link')) {
                    $permalink = get_permalink($_POST['post_id']);

                    $message .= __('Link to property', 'mozart') . ': ' . $permalink . "\n\n";
                }

                if (!parameter('property', 'fields', 'hide_name')) {
                    $message .= __('Name', 'mozart') . ': ' . $_POST['name'] . "\n\n";
                }

                if (!parameter('property', 'fields', 'hide_phone')) {
                    $message .= __('Phone', 'mozart') . ': ' . $_POST['phone'] . "\n\n";
                }

                if (!parameter('property', 'fields', 'hide_date')) {
                    $message .= __('Date', 'mozart') . ': ' . $_POST['date'] . "\n\n";
                }

                if (!parameter('property', 'fields', 'hide_date_from')) {
                    $message .= __('Date From', 'mozart') . ': ' . $_POST['date_from'] . "\n\n";
                }

                if (!parameter('property', 'fields', 'hide_date_to')) {
                    $message .= __('Date To', 'mozart') . ': ' . $_POST['date_to'] . "\n\n";
                }

                if (!parameter('property', 'fields', 'hide_email')) {
                    $message .= __('E-mail', 'mozart') . ': ' . $_POST['email'] . "\n\n";
                }

                if (!parameter('property', 'fields', 'hide_message')) {
                    $message .= __('Message', 'mozart') . ': ' . $_POST['message'] . "\n\n";
                }

                $message .= __('Location', 'mozart') . ': ' . $_SERVER['HTTP_REFERER'];

                if (!empty($author_email)) {
                    $headers = 'From: ' . parameter('property', 'enquire_form', 'name') . ' <' . parameter('property', 'enquire_form', 'email') . '>' . "\r\n";
                    $is_sent = wp_mail($author_email, parameter('property', 'enquire_form', 'subject'), $message, $headers);
                }

                if (is_array($agents)) {
                    foreach ($agents as $agent_id) {
                        $email = get_post_meta($agent_id, '_agent_email', true);
                        $headers = 'From: ' . parameter('property', 'enquire_form', 'name') . ' <' . parameter('property', 'enquire_form', 'email') . '>' . "\r\n";
                        $is_sent = wp_mail($email, parameter('property', 'enquire_form', 'subject'), $message, $headers);
                    }
                }

                $result = array();

                if ($is_sent) {
                    $result['success'] = true;
                    $result['message'] = __('Your enquire was successfully sent.', 'mozart');
                } else {
                    $result['success'] = false;
                    $result['message'] = __('An error occured. Please try again.', 'mozart');
                }
            }
        }
        die(json_encode($result));
    }

    public function ajaxPropertiesMapFilterAction()
    {
        $data['properties'] = $this->getPropertyModel()
                ->getProperties($_GET);

        $data['property_types'] = array();

        foreach ($data['properties'] as $property) {
            $types = array_values(wp_get_object_terms($property->ID, 'property_types'));
            $type = array_shift($types);
            if ($type) {
                $data['property_types'][$property->ID] = $type->slug;
            }
        }

        wp_send_json(array(
            'count' => count($data['properties']),
            'contents' => twiggy(
                    'PropertyBundle:Property:sections/contents.html.twig', $data, false
            ),
            'locations' => twiggy(
                    'PropertyBundle:Property:sections/locations.html.twig', $data, false
            ),
            'types' => twiggy(
                    'PropertyBundle:Property:sections/types.html.twig', $data, false
            ),
        ));
    }

    /**
     * List of all user posts
     *
     * @return string
     */
    public function listUserSubmittedPropertiesAction()
    {
        if (!is_user_logged_in()) {
            $this->get('session')->getFlashBag()->add(
                    'error', __('You need to login to access this page.', 'mozart')
            );

            return wp_redirect(home_url('my-account'));
        }

        $query_args = array(
            'post_status' => isset($_REQUEST['status']) ? $_REQUEST['status'] : 'any',
            'posts_per_page' => parameter('submission', 'common', 'posts_per_page')
        );

        return twiggy(
                'PropertyBundle:Submission:index.html.twig', array(
            'property_statuses' => $this->getPropertyModel()->getStatuses(),
            'current_status' => isset($_REQUEST['status']) ? $_REQUEST['status'] : 'any',
            // get properties and override the wordpress default loop
            // so we can get a proper pagination for our properties
            'properties' => $this->getPropertyModel()
                    ->getUserSubmittedProperties($query_args, true),
            'counters' => $this->getPropertyModel()
                    ->getUserSubmittedPropertiesCounters()
        ));
    }

    /**
     * Edit post
     *
     * @param int
     *
     * @return string
     */
    public function editSubmittedPropertyAction($id)
    {
        global $current_user;

        $post = get_post($id);

        if (false == is_user_logged_in()) {
            $this->get('session')->getFlashBag()->add(
                    'error', __('You need to login to access this page.', 'mozart')
            );

            return wp_redirect(home_url('my-account'));
        }

        if ($current_user->ID != $post->post_author
                || false == current_user_can('edit_published_real_estate_properties')) {
            $this->get('session')->getFlashBag()->add(
                    'error', __('You don\'t have the permission to edit this property.', 'mozart')
            );

            return wp_redirect(home_url('my-account'));
        }

        $metabox = $this->getMetabox();

        $form = apply_filters('generate_submission_form', $form, 'property');

        return twiggy('PropertyBundle:Submission:add.html.twig', array(
            'post' => $post,
            'title' => __('Edit Property', 'mozart'),
            'form' => $this->generateForm($metabox, $id),
            'nonce' => wp_nonce_field('mozart-user-property-submit', "_nonce", true, false)
                ), false);
    }

    public function displaySubmitPropertyFormAction($display = true)
    {
        if (!is_user_logged_in()) {
            $this->get('session')->getFlashBag()->add(
                    'error', __('You need to login to access this page.', 'mozart')
            );

            // @todo: replace HTTP redirect with template redirect
            return wp_redirect(home_url('/my-account/?redirect_to=/my-properties/add/'));
        }

        $metabox = $this->getMetabox();

        $result = twiggy('PropertyBundle:Submission:add.html.twig', array(
            'form' => $this->generateForm($metabox),
            'nonce' => wp_nonce_field('mozart-user-property-submit', "_nonce", true, false)
                ), false);

        if (!$display) {
            return $result;
        }

        echo $result;
    }

    public function processSubmitPropertyFormAction()
    {
        if (!is_user_logged_in()) {
            $this->get('session')->getFlashBag()->add(
                    'error', __('You need to login to access this page.', 'mozart')
            );

            // @todo: replace HTTP redirect with template redirect
            return wp_redirect(home_url('/my-account/?redirect_to=/my-properties/add/'));
        }

        if (!wp_verify_nonce($_REQUEST['_nonce'], 'mozart-user-property-submit')) {

            $this->get('session')->getFlashBag()->add(
                    'error', __('Security error. Please try again.', 'mozart')
            );
        } else {
            $metabox = $this->getMetabox();

            $post_id = 0;

            if (isset($_POST['property-id'])) {
                $post_id = $_POST['property-id'];
            }

            if (isset($_POST['post_title'])) {
                $this->property_edit($post_id, $_POST, $metabox);
            }
        }
    }

    /**
     * Processing of submission page
     * Takes care of permission
     * Takes care of performing appropriate action
     * @return string: Redirect identifier
     */
    public function processAction()
    {
        // most basic security check
        if (!is_user_logged_in()) {
            $this->get('session')->getFlashBag()->add(
                    'error', __('You need to login to access this page.', 'mozart')
            );

            return wp_redirect(home_url());
        }

        if (isset($_GET['id'])) {
            // our precious permission check failed
            if (!$this->getPropertyModel()->action_access($_GET['id'], get_current_user_id(), $_GET['action'])) {
                $page = $this->getPropertyModel()->_get_submission_page();
                wp_redirect(get_permalink($page));

                return true;
            }
        }

        // Edit action
        if (isset($_GET['action'])) {
            $id = null;
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
            }
            switch ($_GET['action']) {
                case 'add':
                case 'edit':
                    if (isset($_POST['post_title'])) {
                        return $this->property_edit($id, $_POST);
                    }
                    break;
                case 'delete':
                    return $this->property_delete($id);
                    break;
                case 'delete-confirm':
                    return $this->property_delete_confirm($id);
                    break;
                case 'delete-thumbnail':
                    return $this->property_thumbnail_delete($id);
                    break;
                case 'unpublish':
                    return $this->changeStatus($id, 'unpublish');
                    break;
                case 'publish':
                    return $this->changeStatus($id, 'publish');
                    break;
                case 'pending':
                    return $this->changeStatus($id, 'pending');
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Output an unordered list of checkbox <input> elements labelled
     * with term names. Taxonomy independent version of wp_category_checklist().
     *
     * @since 3.0.0
     *
     * @param int   $post_id
     * @param array $args
     */
    public function terms_checklist($post_id = 0, $args = array())
    {
        $defaults = array(
            'descendants_and_self' => 0,
            'selected_cats' => false,
            'popular_cats' => false,
            'walker' => null,
            'taxonomy' => 'category',
            'checked_ontop' => true
        );
        $args = apply_filters('wp_terms_checklist_args', $args, $post_id);

        extract(wp_parse_args($args, $defaults), EXTR_SKIP);

        if (empty($walker) || !is_a($walker, 'Walker'))
            $walker = new \Walker_Category_Checklist;

        $descendants_and_self = (int) $descendants_and_self;

        $args = array('taxonomy' => $taxonomy);

        $tax = get_taxonomy($taxonomy);
//    $args['disabled'] = !current_user_can($tax->cap->assign_terms);

        if (is_array($selected_cats))
            $args['selected_cats'] = $selected_cats;
        elseif ($post_id)
            $args['selected_cats'] = wp_get_object_terms($post_id, $taxonomy, array_merge($args, array(
                'fields' => 'ids')));
        else
            $args['selected_cats'] = array();

        if (is_array($popular_cats))
            $args['popular_cats'] = $popular_cats;
        else
            $args['popular_cats'] = get_terms($taxonomy, array('fields' => 'ids',
                'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false));

        if ($descendants_and_self) {
            $categories = (array) get_terms($taxonomy, array('child_of' => $descendants_and_self,
                        'hierarchical' => 0, 'hide_empty' => 0));
            $self = get_term($descendants_and_self, $taxonomy);
            array_unshift($categories, $self);
        } else {
            $categories = (array) get_terms($taxonomy, array('get' => 'all'));
        }

        if ($checked_ontop) {
// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
            $checked_categories = array();
            $keys = array_keys($categories);

            foreach ($keys as $k) {
                if (in_array($categories[$k]->term_id, $args['selected_cats'])) {
                    $checked_categories[] = $categories[$k];
                    unset($categories[$k]);
                }
            }

            // Put checked cats on top

            echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories,
                0, $args));
        }
        // Then the rest of them
        echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0,
            $args));
    }

    /**
     * Access callback for actions related to frontend submission of properties
     * @param $post_id
     * @param $user_id
     * @param $action
     * @return bool
     */
    public function action_access($post_id, $user_id, $action)
    {
        if (!empty($post_id)) {
            $post = get_post($post_id);
            if ($post->post_author != $user_id) {
                $this->get('session')->getFlashBag()->add(
                        'error', __('You are not post owner.', 'mozart')
                );
                $page = self::_get_submission_page();
                wp_redirect(get_permalink($page));

                return false;
            }

            $paypal_condition = parameter('submission', 'common', 'payment_gateway') == 'paypal';
            $is_paid = self::submission_is_paid($user_id, $post_id);

            switch ($action) {
                case 'publish':
                    /**
                     * Who can publish posts
                     * 1. User who paid for it - Paypal
                     * 2. User who submitted it - No approval required
                     */
                    if ($paypal_condition && !$is_paid) {
                        $this->get('session')->getFlashBag()->add(
                                'error', __('You need to pay for submission item'
                                        . ' in order to publish it.', 'mozart')
                        );
                        $page = self::_get_submission_page();
                        wp_redirect(get_permalink($page));

                        return false;
                    }

                    $bool = parameter('property', 'common', 'frontend_needs_submission');
                    if ($bool) {
                        $this->get('session')->getFlashBag()->add(
                                'error', __('Administrator approval is required'
                                        . ' to publish the post', 'mozart')
                        );
                    }
                    break;
                case 'unpublish':
                    /**
                     * Anyone can unpublish his own item
                     */
                    break;
                case 'pending':
                    break;
                default:
                    break;
            }
        }

        // we do have access, what a glorious success!
        return true;
    }

    /**
     * Delete thumbnail on property
     * @param $id
     */
    public function thumbnailDelete($id)
    {
        update_post_meta($id, '_thumbnail_id', '');

        $this->get('session')->getFlashBag()->add(
                'success', __('Post\'s thumbnail has been successfully removed.', 'mozart')
        );

        $query = http_build_query(array('action' => 'edit', 'id' => $id));
        wp_redirect(get_permalink(get_the_ID()) . '/?' . $query);

        return true;
    }

    /**
     * Change status for post
     * @param $id
     * @param $status
     */
    public function changeStatus($id, $status)
    {
        $post = get_post($id);

        if ($status == 'unpublish') {
            $post->post_status = 'draft';
            $this->get('session')->getFlashBag()->add(
                    'success', __('Post has been successfully unpublished.', 'mozart')
            );
        }
        if ($status == 'pending') {
            $post->post_status = 'pending';
            $this->get('session')->getFlashBag()->add(
                    'success', __('Post is pending admin review.', 'mozart')
            );
        }
        if ($status == 'publish') {
            $post->post_status = 'publish';
            $this->get('session')->getFlashBag()->add(
                    'success', __('Post has been successfully published.', 'mozart')
            );
        }

        wp_update_post($post);

        $submission_page = self::_get_submission_page();
        wp_redirect(get_permalink($submission_page->ID));

        return true;
    }

    public function generateForm($metabox, $object_id = null)
    {
        global $post;

        // Check if we are editing already existing post or adding new one
        if ($object_id) {
            $post = get_post($object_id);
        } else {
            $post = new \stdClass();
            $post->ID = 0;
        }

        // Include file rendering checboxes and combo boxes
        if (!function_exists('wp_terms_checklist')) {
            require_once ABSPATH . 'wp-admin/includes/template.php';
        }

        // Property contracts
        $property_contracts_selected_terms = '';
        if (!empty($post->ID)) {
            $property_contracts_terms = wp_get_post_terms($post->ID, 'property_contracts');
            $property_contracts_selected_terms = $property_contracts_terms[0]->term_id;
        }

        $property_contracts = wp_dropdown_categories(array(
            'id' => 'property_contracts',
            'name' => 'property_contracts',
            'taxonomy' => 'property_contracts',
            'echo' => 0,
            'hide_empty' => 0,
            'selected' => $property_contracts_selected_terms,
        ));

        // Property types
        $property_types_selected_terms = '';
        if (!empty($post->ID)) {
            $property_types_terms = wp_get_post_terms($post->ID, 'property_types');
            $property_types_selected_terms = $property_types_terms[0]->term_id;
        }

        $property_types = wp_dropdown_categories(array(
            'id' => 'property_types',
            'name' => 'property_types',
            'taxonomy' => 'property_types',
            'echo' => 0,
            'hide_empty' => 0,
            'selected' => $property_types_selected_terms,
        ));

        // Property locations
        $property_locations_selected_terms = '';
        if (!empty($post->ID)) {
            $property_locations_terms = wp_get_post_terms($post->ID, 'locations');
            $property_locations_selected_terms = $property_locations_terms[0]->term_id;
        }

        $property_locations = wp_dropdown_categories(array(
            'id' => 'property_locations',
            'name' => 'property_locations',
            'taxonomy' => 'locations',
            'echo' => 0,
            'hide_empty' => 0,
            'selected' => $property_locations_selected_terms,
        ));

        ob_start();

        $this->terms_checklist($post->ID, array(
            'taxonomy' => 'amenities',
        ));
        $amenities = ob_get_clean();

        $slides = get_post_meta($post->ID, '_property_slides');
        if (is_array($slides) && count($slides)) {
            $slides = reset($slides);
        } else {
            $slides = array();
        }

        return array(
            'content' => twiggy('PropertyBundle:Property:form-content.html.twig', array(
                'slides' => $slides,
                'post' => $post,
                'amenities' => $amenities,
                'property_types' => $property_types,
                'property_locations' => $property_locations,
                'property_contracts' => $property_contracts,
                    ), false),
            'metabox' => $metabox
        );
    }

    public function property_edit($id = 0, $values, $metabox)
    {
        $post = array();
        if ($id) {
            $post = get_post($id, ARRAY_A);
        }

        $post['post_type'] = 'property';
        $post['post_title'] = $values['post_title'];
        $post['post_content'] = $values['content'];

        // Only if creating
        // it should go to pending approval if set so
        // free submission might require review
        if ($id && $post['post_status'] == 'publish') {
            $post['post_status'] = 'publish';
        } else {
            $post['post_status'] = 'draft';
            if (isset($values['submit-for-review'])) {
                $post['post_status'] = 'pending';
            }
        }

        // If already existing
        // Don't change the status
        // save
        if ($id) {
            wp_update_post($post);

            $this->get('session')->getFlashBag()->add(
                    'success', __('Property has been successfully updated.', 'mozart')
            );
        } else {
            $id = wp_insert_post($post);
            $preview_link = '';
            if (current_user_can('edit_real_estate_properties', $id)) {

                /** This filter is documented in wp-admin/includes/meta-boxes.php */
                $preview_link = '<a href="' . esc_url(apply_filters('preview_post_link', set_url_scheme(add_query_arg('preview', 'true', get_permalink($id))))) . '" title="" rel="permalink">' . __('Preview Property') . '</a>';
            }
            $this->get('session')->getFlashBag()->add(
                    'success', sprintf(__('Property has been successfully submitted. %s', 'mozart'), $preview_link)
            );
        }

        // set post terms
        wp_set_post_terms($id, $values['property_types'], 'property_types');
        wp_set_post_terms($id, $values['property_locations'], 'locations');
        wp_set_post_terms($id, $values['property_contracts'], 'property_contracts');

        if (isset($values['tax_input']) && is_array($values['tax_input']) && is_array($values['tax_input']['amenities'])) {
            $tags = '';
            foreach ($values['tax_input']['amenities'] as $amenity) {
                $tags .= $amenity . ',';
            }
            wp_set_post_terms($id, $tags, 'amenities');
        }

        // save meta
        $metabox->force_save($id);

        // featured image was error
        if (!empty($_FILES['featured_image']['name'])) {
            if ($_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
                if (!empty($_FILES['featured_image'])) {
                    $this->get('session')->getFlashBag()->add(
                            'error', __('Image can not be uploaded', 'mozart')
                    );

                    return;
                }
            } else {
                $thumbnail_id = get_post_thumbnail_id($id);
                if ($thumbnail_id) {
                    update_post_meta($id, '_thumbnail_id', '');
                }

                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $attach_id = media_handle_upload('featured_image', $id);
                update_post_meta($id, '_thumbnail_id', $attach_id);
            }
        }


        $slides = get_post_meta($id, '_property_slides');
        $slides = reset($slides);

        // parse out remaining / already existing slides
        $existing_slides = array();
        foreach ($values as $key => $field) {
            if (!is_array($field)) {
                if (strpos($key, '_property_meta_slides_id_') !== FALSE) {
                    $slide_id = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
                    if (isset($values['_property_meta_slides_weight_' . $slide_id])) {
                        $existing_slides[$values['_property_meta_slides_weight_' . $slide_id]] = $field;
                    }
                }
            }
        }

        $new_slides = array();
        // remove old slides
        if (is_array($existing_slides) && count($existing_slides)) {
            foreach ($slides as $slide) {
                $bool = true;

                foreach ($existing_slides as $weight => $existing_slide) {
                    if ($slide['imgurl'] == $existing_slide) {
                        // we are preserving this
                        $bool = FALSE;
                        $new_slides[$weight] = $slide;
                        break;
                    }
                }

                if ($bool) {
                    global $wpdb;
                    // @TODO - HERE IS RESIZE - remove it ?
                    $guid = $slide['imgurl']; // str_replace('-150x150', '', $slide['imgurl']);
                    // select according to guid
                    $result = $wpdb->get_row($wpdb->prepare("SELECT ID "
                                    . "FROM $wpdb->posts "
                                    . "WHERE guid = \"$guid\""));
                    // delete old slide
                    if ($result->id) {
                        wp_delete_attachment($result->id);
                    }
                }
            }
        }

        foreach ($_FILES as $key => $file) {
            if (strpos($key, '_property_meta_slides') !== FALSE) {
                if (!empty($_FILES[$key]['name']) && $_FILES[$key]['error'] !== UPLOAD_ERR_OK) {

                    $this->get('session')->getFlashBag()->add(
                            'error', __('Image can not be uploaded', 'mozart')
                    );

                    return;
                } else {
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    require_once(ABSPATH . 'wp-admin/includes/media.php');

                    $attach_id = media_handle_upload($key, $id);
                    $weight = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
                    if (isset($values['_property_meta_slides_weight_' . $weight])) {
                        $result = wp_get_attachment_image_src($attach_id);
                        $new_slides[$values['_property_meta_slides_weight_' . $weight]]['imgurl'] = $result[0];
                    }
                }
            }
        }

        $results = get_post_meta($id);
        $meta_fields = unserialize($results['_property_meta_fields']);
        $meta_fields[] = '_property_slides';

        ksort($new_slides);

        update_post_meta($id, '_property_meta_fields', array(
            '_property_id', '_property_title', '_property_landlord', '_property_agencies',
            '_property_agents', '_property_custom_text',
            '_property_price', '_property_price_suffix', '_property_bathrooms', '_property_hide_baths',
            '_property_hide_beds', '_property_area',
            '_property_latitude', '_property_longitude', '_property_featured', '_property_reduced',
            '_property_slider_image', '_property_slides'
        ));
        update_post_meta($id, '_property_slides', $new_slides);
    }

    /**
     * Delete property
     * @param $id
     */
    public function property_delete($id)
    {
        $link = $this->getPropertyModel()
                ->submission_create_link(array('id' => $id, 'action' => 'delete-confirm'));
        $this->get('session')->getFlashBag()->add(
                'notice', __("Are you sure you want to delete this post? "
                        . "<a href=\"$link\" class=\"btn\">Yes, delete</a>", 'mozart')
        );
    }

    public function property_delete_confirm($id)
    {
        $page = self::_get_submission_page();
        $this->get('session')->getFlashBag()->add(
                'success', __("Post has been successfully deleted.", 'mozart')
        );

        wp_delete_post($id);
        wp_redirect(get_permalink($page));

        return true;
    }

    public function getMetabox()
    {
        return new Submission_MetaBox(array(
            'id' => '_property_meta',
            'title' => __('Property Options', 'mozart'),
            'template' => __DIR__ . '/../meta-submission.php',
            'types' => array('property'),
            'prefix' => '_property_',
            'mode' => WPALCHEMY_MODE_EXTRACT,
        ));
    }

    protected function getPropertyModel()
    {
        return $this->get('realestate.property.model');
    }

}
