<?php
/* Template Name: Special Code Template */

// Hide deprecated notices
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
@ini_set('display_errors', 0);


$vmb_settings = json_decode(get_option('vmb_settings'));

$elementor_header = $vmb_settings->elementor_header;
$elementor_footer = $vmb_settings->elementor_footer;

if( $elementor_header ) {
    echo do_shortcode('[elementor-template id="'.$elementor_header.'"]');
}

get_header();

$specialcode = get_query_var('specialcode');
?>

<div class="container specialcode-template">

    <div class="template-title"><h1><?= $specialcode; ?></h1></div>

    <div class="specials-container">

    <?= do_shortcode('[display_category category="'.$specialcode.'"]'); ?>    
        
    </div>
</div>


<?php

if( $elementor_footer ) {
    echo do_shortcode('[elementor-template id="'.$elementor_footer.'"]');
}

get_footer();

