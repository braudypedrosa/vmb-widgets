<?php 

add_shortcode('vmb_reviews', 'display_vmb_reviews');
add_shortcode('vmb_specials', 'display_vmb_specials');
add_shortcode('related_specials', 'display_related_specials');
add_shortcode('display_special', 'display_special_shortcode');
add_shortcode('display_category', 'display_special_category');

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

    if(get_post_type( $post_id ) != 'resort') {
        return 'Resort ID is required if used outside resort pages.';
	}
	
	if( $atts['resort_id'] != '') {
		$post_id = $atts['resort_id'];
	}

    $name = get_the_title( $post_id );

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

}

function display_vmb_specials($atts) {

    $output = '';
    $helper = new VMB_HELPER();
    
    $atts = shortcode_atts(
		array(
			'id' => '',
		), $atts, 
        'specials' );

        $cached_specials = get_option('vmb_api_cached_specials', true);

        $specials = $helper->get_specials($cached_specials, 'resort_id', $atts['id']);
        
        foreach($specials as $special) {
           
            if( !$special['disable'] ) {

				$packageID = $special['id'];
				$name = $special['name'];
				$reservationURL = get_field('reservation_url', $special['resort_id']);

				$description = $special['description'];
                $start = $special['start'] ?? date('n/j/Y');
				$expiration = $special['expiration'] ?? date('n/j/Y');


				$output .= '<div class="vmb-special" id="special-'.$packageID.'">
								<img src="'.plugin_dir_url(__DIR__).'/assets/specials-icon.png">
								<div class="special-details">
									<h3 class="package-name">'.$name.'</h3>
									<p class="description">'.$description.'</p>
									<div class="validity">
										<span>Valid:</span>
										<span>'.date_format(date_create($start), 'n/j/Y').' - '.date_format(date_create($expiration), 'n/j/Y').'</span>
									</div>
									<a class="theme-button" href="'.$reservationURL.'?packageId='.$packageID.'" tabindex="0">Book Now</a>
								</div>
							</div>';
			}
        }
    
        // pretty_print_array($specials);
        return '<div class="vmb-widget vmb-specials">'.$output.'</div>';

}

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

function display_special_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'id' => '',
            'property' => '',
        ),
        $atts,
        'display_special'
    );

    $specials_json = get_option('vmb_api_cached_specials');
    $specials = json_decode($specials_json, true);

    $output = '';

    if (!empty($atts['id']) && !empty($specials)) {
        if (empty($atts['property'])) {
            return 'The property attribute is required.';
        }

        foreach ($specials as $special) {
            if ($special['id'] == $atts['id']) {
                if (isset($special[$atts['property']])) {
                    $output = $special[$atts['property']];
                } else {
                    $output = 'Attribute not found.';
                }
                break;
            }
        }

        if (empty($output)) {
            $output = 'Special not found.';
        }
    } else {
        $output = 'No specials available or ID not provided.';
    }

    return $output;
}

function display_special_category($atts) {
    $atts = shortcode_atts(
        array(
            'category' => '',
        ),
        $atts,
        'display_category'
    );

    $helper = new VMB_HELPER();

    $specials = $helper->filter_specials_by_category($atts['category']);

    $output = '';

    if(count($specials) > 0) {

        foreach($specials as $special) {
            
            $post_id = $special['resort_id'];

            if($post_id) {

                $permalink = get_the_permalink($post_id);
                $image = get_field('preview_image', $post_id);
                $reservationURL = get_field('reservation_url', $post_id);

                $output .= '<div class="specialcode-item" id="special-'.$post_id.'">
                    <img src="'. (($image) ? $image['url'] : 'https://via.placeholder.com/600x400') .'" alt="'.$special['resort'].'">
                    <div class="specials-content">
                        <div class="specials-title">'.$special['resort'].'</div>
                        <div class="specials-subtitle">'.$special['name'].'</div>
                        '. (($special['description']) ? '<div class="specials-description">'. $special['description'] .'</div>' : '') .'
                        <div class="specials-buttons mt-3">
                            <a href="'.$reservationURL.'?packageId='.$special['id'].'" class="btn">View Deal Info</a>
                            <a href="'.$permalink.'" class="btn">View Resort Info</a>
                        </div>
                    </div>
                </div>';

            }
        }

        return $output;

    } else {
        return $helper->displayMessage(['code' => 'error', 'message' => 'No specials found under category: ' . $atts['category']]);
    }
}



function test_shortcode_func() {

    // pull 5 star reviews
    $api_helper = new VMB_API_HELPER();
    $helper = new VMB_HELPER();
    $vmb_settings = json_decode(get_option('vmb_settings'));

    $endpoint = 'https://external.guestdesk.com/partner/v1/System/Packages';

    $auth = base64_encode($vmb_settings->guestdesk_username.':'.$vmb_settings->guestdesk_password);

    $headers = array(
        'Authorization: Basic '.$auth,
        'Content-Type: application/json',
        'Accept: application/json'
    );

    $cached_specials = get_option('vmb_api_cached_specials', true);
    $specials = $helper->get_specials($cached_specials, 'resort', 'Beach Colony Resort');
    
    foreach($specials as $special) {
       
        $packageID = $special['id'];
        $name = $special['name'];
        $reservationURL = get_field('reservation_url', $special['resort_id']);
        
        $description = $special['description'];
        $expiration = $special['expiration'];


        $output .= '<div class="vmb-special" id="special-'.$packageID.'">
                        <img src="'.plugin_dir_url(__DIR__).'/assets/specials-icon.png">
                        <div class="special-details">
                            <h3 class="package-name">'.$name.'</h3>
                            <p class="description">'.$description.'</p>
                            <div class="validity">
                                <span>Valid:</span>
                                <span>'.date('n/j/Y').' - '.date_format(date_create($expiration), 'n/j/Y').'</span>
                            </div>
                            <a class="theme-button" href="'.$reservationURL.'?packageId='.$packageID.'" tabindex="0">Book Now</a>
                        </div>
                    </div>';
    }

    // pretty_print_array($specials);
    return '<div class="vmb-widget vmb-specials">'.$output.'</div>';
}

add_shortcode('test_shortcode', 'test_shortcode_func');