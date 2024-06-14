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
    

}