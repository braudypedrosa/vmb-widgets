<?php

class VMB_API_HELPER {


    public function __contruct() {
        
    }

    public function AlchemerApiRequest($endpoint, $method, $parameter_array = array(), $header_array = array(), $custom_success_message = '') {
        
        $curl = curl_init();

        $url = $endpoint.''.(!empty($parameter_array) ? '?'.http_build_query($parameter_array) : '');

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $header_array
        ));

        $response = curl_exec($curl);
        $result = json_decode($response, true);

        if(!$result['result_ok']) {
            return ['code' => 'fail', 'message' => $result['message'], 'response' => null];
        } else {
            if($result['total_count'] == 0) {
                return ['code' => 'fail', 'message' => 'No results found!', 'response' => null];
            }
            return ['code' => 'success', 'message' => ($custom_success_message != '' ? $custom_success_message : 'API call successful!'), 'response' => $result['data']];
        }

        curl_close($curl);
    }

    public function GuestDeskApiRequest($endpoint, $method, $parameter_array = array(), $header_array = array(), $custom_success_message = '') {
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($parameter_array),
            CURLOPT_HTTPHEADER => array(
              'Authorization: Basic YnVpbGR1cGJvb2tpbmdzOlJ2M1ZiNUxnUHJRWWtFaDc=',
              'Content-Type: application/json',
              'Accept: application/json'
            ),
        ));


        $response = curl_exec($curl);
        $result = json_decode($response, true);

        if(!empty($response['Error'])) {
            return ['code' => 'fail', 'message' => $result['Error'], 'response' => null];
        } else {
            return ['code' => 'success', 'message' => ($custom_success_message != '' ? $custom_success_message : 'API call successful!'), 'response' => $result['Sites']];
        }

        curl_close($curl);
        
        // echo $response;
        // var_dump($header_array);

    }

    public function generateVMBReview($data, $fieldID, $connectedProperty = '') {

        global $wpdb;

        if(!empty($data)) {

            foreach($data as $review) {

                if(!empty($review['survey_data'][$fieldID]['comments'])) {

                    $helper = new VMB_HELPER();

                    $id = $review['id'];
                    $uniqueID = $helper->slugify($connectedProperty) . '-' . $id;
                    
                    $firstname = $review['url_variables']['firstname']['value'];
                    $lastname = $review['url_variables']['lastname']['value'];
                    $comment = $review['survey_data'][$fieldID]['comments'];
                    $rating = $review['survey_data'][$fieldID]['answer'];
    
    
                    $sql = "SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'vmb_review_id' AND meta_value='".$uniqueID."'";
            
                    $result = $wpdb->get_results($sql,ARRAY_A);
                    $post_id = isset($result[0]['post_id']) ? $result[0]['post_id'] : '';
    
                    // add review if search returns null
                    if($post_id == null) {
                        $post_id = wp_insert_post(array(
                            'post_title'=> $firstname .' '. $lastname,
                            'post_type'=> 'vmb_reviews',
                            'post_content' => $comment,
                            'post_status'=> 'publish'
                        ));
                    } else { // update logic here

                        if( get_post_meta( $post_id, 'review_modified', true) && get_field( 'include_in_sync', $post_id ) ) {
                            wp_update_post(array(
                                'ID' => $post_id,
                                'post_title' => $firstname .' '. $lastname,
                                'post_content' => $comment
                            ));

                            delete_post_meta($post_id, 'review_modified');
                        }
                    }
    
                    update_post_meta($post_id, 'vmb_review_id', $uniqueID);
                    update_post_meta($post_id, 'vmb_review_firstname', $firstname);
                    update_post_meta($post_id, 'vmb_review_lastname', $lastname);
                    update_post_meta($post_id, 'vmb_review_comment', $comment);
                    update_post_meta($post_id, 'vmb_review_rating', $rating);
                    update_post_meta($post_id, 'connected_property', $connectedProperty);
                }
            }
        }

    }


    public function generateVMBSpecials($data, $connectedProperty = '', $parent_post_id = '') {

        global $wpdb;
        $helper = new VMB_HELPER();

        if(!empty($data)) {

            foreach($data as $specials) {

                $packageID = $specials['PackageId'];
                $uniqueID = $helper->slugify($connectedProperty) . '-' . $packageID;

                $active = $specials['Active'];
                $promote = $specials['Promote'];
                $bookable = $specials['Bookable'];

                $startDate = $specials['CalendarStartDate'];
                $endDate = $specials['CalendarEndDate'];
                $displayName = $specials['PackageDisplayName'];
                $shortDescription = $specials['PackageShortDescription'];
                $disclaimer = $specials['Disclaimer'];
                $noNotice = $specials['Products']['Room']['RoomResultsNoResultsMessage'];
                $longDescription = $specials['PackageDescriptionLargeScreen'];

    
                $sql = "SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = 'vmb_special_unique_id' AND meta_value='".$uniqueID."'";
    
                $result = $wpdb->get_results($sql,ARRAY_A);
                $post_id = isset($result[0]['post_id']) ? $result[0]['post_id'] : '';
    
                
                // only add active package
                if($active && $bookable && $promote) {

                    // add special if search returns null
                    if($post_id == null) {
                        $post_id = wp_insert_post(array(
                            'post_title'=> $displayName,
                            'post_type'=> 'vmb_specials',
                            'post_status'=> 'publish',
                            'post_content'=> strip_tags($longDescription, ['p', 'a', 'img', 'strong']),
                            'post_name' => $displayName .' - '. $connectedProperty
                        )); 
                    } else { // update logic here
                        
                        wp_update_post(array(
                            'ID' => $post_id,
                            'post_title' => $displayName,
                            'post_content'=> strip_tags($longDescription, ['p', 'a', 'img', 'strong']),
                            'post_name' => $displayName .' - '. $connectedProperty
                        ));
                    }
                    
                    update_post_meta($post_id, 'vmb_special_unique_id', $uniqueID);
                    update_post_meta($post_id, 'vmb_special_package_id', $packageID);
                    update_post_meta($post_id, 'vmb_special_start_date', $startDate);
                    update_post_meta($post_id, 'vmb_special_end_date', $endDate);
                    update_post_meta($post_id, 'vmb_special_shortDescription', $shortDescription);
                    update_post_meta($post_id, 'vmb_special_disclaimer', $disclaimer);
                    update_post_meta($post_id, 'vmb_special_longDescription', $longDescription);
                    update_post_meta($post_id, 'vmb_special_notice', $noNotice);
                    update_post_meta($post_id, 'connected_property', $connectedProperty);
                    update_post_meta($post_id, 'vmb_special_package_url', get_field('reservation_url', $parent_post_id).'?packageId='.$packageID);
                    update_post_meta($post_id, 'connected_property_permalink', get_the_permalink($parent_post_id));
                    update_post_meta($post_id, 'connected_property_featured_image', get_the_post_thumbnail_url($parent_post_id, 'full'));
                }
                
            }
        }

        return $data;
    }
    


    public function displayResponseMessage($response) {
        return '<div class="response-notice '.$response['code'].'">'.$response['message'].'</div>';
    }

}