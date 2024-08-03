<?php 

function print_star_rating($rating) {
    $rating = intval($rating); // Ensure rating is an integer

    // Validate rating (between 0 and 5)
    if ($rating < 0) {
        $rating = 0;
    } elseif ($rating > 5) {
        $rating = 5;
    }

    $output = '<div class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
        $output .= '<span class="fa fa-star';
        if ($i <= $rating) {
            $output .= ' filled';
        }
        $output .= '"></span>';
    }
    $output .= '</div>';

    return $output;
}

function pretty_print_array($array) {
    echo "<pre>";
        print_r($array);
    echo "</pre>";
}

function vmb_slugify($string) {
    return trim(preg_replace('/[\s-]+/', '-', preg_replace('/[^a-z0-9\s-]/', '', strtolower($string))), '-');
}