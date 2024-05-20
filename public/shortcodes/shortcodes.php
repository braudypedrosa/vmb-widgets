<?php 

function display_vmb_reviews($atts) {

    global $post;
    $post_id = $post->ID;
    $output = '';
    

    $atts = shortcode_atts(
		array(
			'resort_id' => '',
            'limit' => -1,
		), $atts, 
        'reviews' );

    // check if post type exists
    if(!post_type_exists('vmb_reviews')) {
        return 'VMB Reviews post type is required to use this plugin!';
    }

    if(get_post_type( $post_id ) != 'resort' && $atts['resort_id'] == '') {
        return 'Resort ID is required if used outside resort pages.';
    }else {
        $atts['resort_id'] = $post_id;
    }

    $name = get_the_title( $atts['resort_id'] );

    $reviews = get_posts(
        array(
            'numberposts' => $atts['limit'],
            'post_type' => 'vmb_reviews',
            'meta_key' => 'connected_property',
            'meta_value' => $name
        )
    );

    
    foreach($reviews as $review) {
        $id = $review->ID;
        $hide = get_field('hide_from_query', $id);

        if(!$hide) {
            $firstname = get_post_meta($id, 'vmb_review_firstname', true);
            $rating = get_post_meta($id, 'vmb_review_rating', true);
            $comment = (get_post_field('post_content', $id) != '') ? get_post_field('post_content', $id) : get_post_meta($id, 'vmb_review_comment', true);


            $output .= '<div class="vmb-review" id="review-'.$id.'">
                            <i class="fa fa-solid fa-quote-left"></i>
                            <div class="review-details">
                                <div class="comment">
                                    '.$comment.'
                                </div>
                                <div class="rating">'.print_star_rating($rating).'</div>
                                <span class="author">'.$firstname.'</span>
                            </div>
                        </div>';

        }
    }

    return '<div class="vmb-widget vmb-reviews">'.$output.'</div>';

    
    // pretty_print_array($reviews);

}

add_shortcode('vmb_reviews', 'display_vmb_reviews');


function display_vmb_specials($atts) {

    global $post;
    $post_id = $post->ID;
    $output = '';
    

    $atts = shortcode_atts(
		array(
			'resort_id' => '',
            'limit' => -1,
		), $atts, 
        'specials' );

    // check if post type exists
    if(!post_type_exists('vmb_specials')) {
        return 'VMB Specials post type is required to use this plugin!';
    }

    if(get_post_type( $post_id ) != 'resort' && $atts['resort_id'] == '') {
        return 'Resort ID is required if used outside resort pages.';
    } else {
        $atts['resort_id'] = $post_id;
    }

    

    $name = get_the_title( $atts['resort_id'] );

    $specials = get_posts(
        array(
            'numberposts' => $atts['limit'],
            'post_type' => 'vmb_specials',
            'meta_key' => 'connected_property',
            'meta_value' => $name
        )
    );

    
    foreach($specials as $special) {
       
        $id = $special->ID;
        $name = $special->post_title;
        $hide = get_field('hide_from_query', $id);
        $reservationURL = get_field('reservation_url', $atts['resort_id']);

        if(!$hide) {
            $description = get_post_meta($id, 'vmb_special_shortDescription', true);

            if(get_the_excerpt($id) != '') {
                $description = get_the_excerpt($id);
            }

            $packageID = get_post_meta($id, 'vmb_special_package_id', true);
            $startDate = date_create(get_post_meta($id, 'vmb_special_start_date', true));
            $endDate = date_create(get_post_meta($id, 'vmb_special_end_date', true));


            $output .= '<div class="vmb-special" id="special-'.$id.'">
                            <img src="'.plugin_dir_url(__DIR__).'/assets/specials-icon.png">
                            <div class="special-details">
                                <h3 class="package-name">'.$name.'</h3>
                                <p class="description">'.$description.'</p>
                                <div class="validity">
                                    <span>Valid:</span>
                                    <span>'.date('n/j/Y').' - '.date_format($endDate, 'n/j/Y').'</span>
                                </div>
                                <a class="theme-button" href="'.$reservationURL.'?packageId='.$packageID.'" tabindex="0">Book Now</a>
                            </div>
                        </div>';
        }
    }

    // pretty_print_array($specials);
    return '<div class="vmb-widget vmb-specials">'.$output.'</div>';

    

}

add_shortcode('vmb_specials', 'display_vmb_specials');

function display_related_specials() {
    global $post;
    $post_id = $post->ID;

    $connected_property = get_post_meta($post_id, 'connected_property', true);
    $output = '';

    $specials = get_posts(
        array(
            'numberposts' => -1,
            'post_type' => 'vmb_specials',
            'meta_key' => 'connected_property',
            'meta_value' => $connected_property
        )
    );

    foreach($specials as $special) {
        if($special->ID != $post_id) {
            $output .= '<li class="vmb-specials-list-item"><a href="'.get_the_permalink($special->ID).'">'.$special->post_title.'</a></li>';
        }
    }

    return '<h4 class="vmb-specials-list-heading">Related deals from '.$connected_property.'</h4><ul class="vmb-specials-list">'.$output.'</ul>';
}

add_shortcode('related_specials', 'display_related_specials');

function display_specials_preview($atts) {

    global $post;
    $post_id = $post->ID;

    $connected_property = get_post_meta($post_id, 'connected_property', true);
    $output = '';

    $atts = shortcode_atts(
		array(
			'resort_id' => '',
            'limit' => 4,
		), $atts, 
    'specials_preview' );

    // check if post type exists
    if(!post_type_exists('vmb_specials')) {
        return 'VMB Specials post type is required to use this plugin!';
    }

    if(get_post_type( $post_id ) != 'resort' && $atts['resort_id'] == '') {
        return 'Resort ID is required if used outside resort pages.';
    }

    if(get_post_type( $post_id ) == 'resort') {
        $atts['resort_id'] = $post_id;
    }

    
    

    $specials = get_posts(
        array(
            'numberposts' => $atts['limit'],
            'post_type' => 'vmb_specials',
            'meta_key' => 'connected_property',
            'meta_value' => $connected_property
        )
    );


    if(count($specials) >= 1) {
        
        $output .= '<div id="resort-'.$atts['resort_id'].'" class="specials-preview"><ul style="display: none;" class="specials-list">';

        foreach($specials as $special) {
            $id = $special->ID;
            $name = $special->post_title;
            $permalink = get_the_permalink($id);
            $reservationURL = get_field('reservation_url', $atts['resort_id']);
            

            $output .= '<li class="special-preview" id="sp-'.$id.'">
                    <a href="'.$permalink.'">'.$name.'</a>
                </li>';
        }

        $output .= '</ul><a href="#" class="preview-btn show-trigger" data-href="'.$reservationURL.'">View Specials</a></div>';

    }

    return $output;
}

add_shortcode('specials_preview', 'display_specials_preview');

function test_shortcode_func() {

    // pull 5 star reviews
    $helper = new VMB_API_HELPER();
    $vmb_settings = json_decode(get_option('vmb_settings'));


    $resorts = get_posts([
        'post_type' => 'resort',
        'post_status' => 'publish',
        'numberposts' => -1
    ]);

    foreach($resorts as $resort) {

        $resortID = get_field('site_id', $resort->ID);
        $resortName = get_field('site_name', $resort->ID);

        // $endpoint = 'https://api.alchemer.com/v5/survey/'.$resortID.'/surveyresponse';

        // $params = array (
        //     'api_token' => $vmb_settings->alchemer_token,
        //     'api_token_secret' => $vmb_settings->alchemer_secret,
        //     'resultsperpage' => 5,
        //     'filter[field][0]' => '[question(89)]',
        //     'filter[operator][0]' => 'IS NOT NULL',
        //     'filter[field][1]' => '[question(89)]',
        //     'filter[operator][1]' => '>=',
        //     'filter[value][1]' => '5',
        //     'order_by' => '-date_submitted'
        // );

        // $results = $helper->AlchemerApiRequest($endpoint, 'GET', $params);

        $endpoint = 'https://external.guestdesk.com/partner/v1/System/Packages';

        $params = array(
            "language" => "",
            "requestId" => "",
            "requestTime" => gmdate('Y-m-d\TH:i:s.v\Z'),
            "sites" => array(
                array(
                    "siteName" => $resortName
                )
            )
        );

        $auth = base64_encode($vmb_settings->guestdesk_username.':'.$vmb_settings->guestdesk_password);

        $headers = array(
            'Authorization: Basic '.$auth,
            'Content-Type: application/json',
            'Accept: application/json'
        );

        $results = $helper->GuestDeskApiRequest($endpoint, 'POST', $params, $headers);
        
        if($results['code'] == 'success') {
            update_post_meta($resort->ID, 'specials_' . $resortName, $results['response']);

            $data = $helper->generateVMBSpecials($results['response'][$resortName]['Packages']);
        }

        // if($results['code'] == 'success') {
        //     update_post_meta( $resort->ID, 'reviews_' .$resortID, $results['response']);

        //     $data = $helper->generateVMBReview($results['response']);
        // } 

        // var_dump($endpoint . '?' . http_build_query($params));
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        

        return;
    }

}

add_shortcode('test_shortcode', 'test_shortcode_func');