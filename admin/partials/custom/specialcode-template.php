<?php
/* Template Name: Special Code Template */

get_header();

$specialcode = get_query_var('specialcode');

if ($specialcode) {
    echo '<h1>' . esc_html($specialcode) . '</h1>';
    // Add your custom content for this category here
} else {
    echo '<h1>Category not found</h1>';
}

?>

<div class="container specialcode-template">
    <div class="specials-container">
        <img src="https://via.placeholder.com/300x200" alt="Resort Image">
        <div class="specials-content">
            <div class="specials-title">Beach Colony Resort</div>
            <div class="specials-subtitle">2 FREE Nights!</div>
            <div class="specials-description">
                Enjoy 7 nights at the beach but only pay for 5!
            </div>
            <div class="specials-buttons mt-3">
                <a href="#" class="btn">View Deal Info</a>
                <a href="#" class="btn">View Resort Info</a>
            </div>
        </div>
    </div>
</div>


<?php
get_footer();
