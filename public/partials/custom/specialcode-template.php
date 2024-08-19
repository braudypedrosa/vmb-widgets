<?php
/* Template Name: Special Code Template */

// Hide deprecated notices
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
@ini_set('display_errors', 0);


$vmb_settings = json_decode(get_option('vmb_settings'));

get_header();

$specialcode = get_query_var('specialcode');
?>

<div class="container specialcode-template">

    <div class="template-title"><h1><?= $specialcode; ?></h1></div>

    <div class="specials-container">

    <?= do_shortcode('[display_category category="'.strtolower($specialcode).'"]'); ?>    
        
    </div>
</div>


<?php

get_footer();

