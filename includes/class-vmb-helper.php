<?php

class VMB_HELPER {

    public function __contruct() {

    }

    public function slugify($text) {
        return strtolower(trim(preg_replace('~[^\\pL\d]+~u', '-', iconv('utf-8', 'us-ascii//TRANSLIT', $text)), '-'));
    }

    function unslugify($slug) {
        return ucwords(str_replace('-', ' ', $slug));
    }

    function is_past_date($date) {
  
        $current_date = new DateTime();
        $date = new DateTime($date);
        
        return $current_date > $date;
    }

    function filter_specials_by_category($category) {
        // Get specials
        $specials = get_option('vmb_api_cached_specials') ? get_option('vmb_api_cached_specials') : array();
    
        $data = json_decode($specials, true);
        
        // Initialize an array to hold the filtered results
        $filtered_specials = array();
        
        // Iterate through the data and filter based on category and disable status
        foreach ($data as $special) {
            if ($special['category'] == $category && !$special['disable']) {
                error_log('Matched category: ' . $special['category']);
                $filtered_specials[] = $special;
            }
        }
    
        // Debugging: Log the filtered results
        error_log('Filtered results: ' . print_r($filtered_specials, true));
    
        return $filtered_specials;
    }

    function get_specials($json, $key, $value) {
    
        $data = json_decode($json, true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            return "Failed to decode JSON: " . json_last_error_msg();
        }
    
        
        $filteredData = array_filter($data, function($entry) use ($key, $value) {
            return isset($entry[$key]) && $entry[$key] == $value;
        });
    
        return $filteredData;
    }

    function get_resort_id_by_name($name) {
        // Arguments for the query
        $args = array(
            'post_type' => 'resort', // Custom post type
            'title' => $name, // Post title to search for
            'posts_per_page' => 1, // Only need one result
            'fields' => 'ids' // Only return the IDs
        );
    
        // Execute the query and get the posts
        $posts = get_posts($args);
    
        // Return the first post ID if available, otherwise null
        return !empty($posts) ? $posts[0] : null;
    }

    
    public function displayMessage($response) {
        return '<div class="response-notice '.$response['code'].'">'.$response['message'].'</div>';
    }

}