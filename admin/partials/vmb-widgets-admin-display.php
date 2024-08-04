<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://https://buildupbookings.com/
 * @since      1.0.0
 *
 * @package    Vmb_Widgets
 * @subpackage Vmb_Widgets/admin/partials
 */


$vmb_settings = get_option('vmb_settings') ? json_decode(get_option('vmb_settings')) : '';

?>

<div class="vmb-widgets-container">
    <div class="notification-section">

    <?php if(isset($_GET['status'])) { ?>

        <?php 
            switch($_GET['status']) {
                case 'success': 
                    $notice_class = 'notice-success';
                    break;
                case 'fail': 
                    $notice_class = 'notice-error';
                    break;
            }
        ?>
        
        <div class="notice <?= $notice_class; ?>">
            <p><?php echo $_GET['msg']; ?></p>
        </div>
        
    <?php } ?>
    </div>
    <div class="vmb-widgets-section">

        <form class="vmb-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="save_vmb_settings" />

            <h3 class="vmb-title">Alchemer API (for Reviews)</h3>
            <!-- 3577a1b61ad7ef19043038e6c3ae21d085dbc0d72a33c0b2ca -->
            <div class="vmb-input-wrapper input-text">
                <label for="alchemer-token">API Token</label>
                <input class="vmb-input" required type="text" name="alchemer-token" id="alchemer-token" value="<?= (($vmb_settings != '') ? $vmb_settings->alchemer_token : ''); ?>">
            </div>
            <!-- A9J/CA2zvJRcQ -->
            <div class="vmb-input-wrapper input-text">
                <label for="alchemer-secret">API Secret</label>
                <input class="vmb-input" required type="text" name="alchemer-secret" id="alchemer-secret" value="<?= (($vmb_settings != '') ? $vmb_settings->alchemer_secret : ''); ?>">
            </div>

            <h3 class="vmb-title">Guestdesk API Logins (for Specials)</h3>
            <!-- buildupbookings -->
            <div class="vmb-input-wrapper input-text">
                <label for="guestdesk-username">Username</label>
                <input class="vmb-input" required type="text" name="guestdesk-username" id="guestdesk-username" value="<?= (($vmb_settings != '') ? $vmb_settings->guestdesk_username : ''); ?>">
            </div>
            <!-- Rv3Vb5LgPrQYkEh7 -->
            <div class="vmb-input-wrapper input-text">
                <label for="guestdesk-password">Password</label>
                <input class="vmb-input" required type="text" name="guestdesk-password" id="guestdesk-password" value="<?= (($vmb_settings != '') ? $vmb_settings->guestdesk_password : ''); ?>">
            </div>

            <div class="vmb-input-wrapper input-text">
                <label for="category-slug">Category Slug (Default: specialcode)</label>
                <input class="vmb-input" type="text" name="category-slug" id="category-slug" value="<?= (($vmb_settings->category_slug != '' && isset($vmb_settings->category_slug)) ? $vmb_settings->category_slug : 'specialcode'); ?>">
            </div>
            
            <button class="vmb-button" type="submit">Save Settings</button> 
        </form>

        <h3 class="vmb-title">Syncing</h3>
        
        <h4>Individual Sync</h4>
        <form class="vmb-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="individual_sync_vmb_reviews" />

            <div class="vmb-input-wrapper input-number">
                <label for="individual_minimum_rating">Minimum rating to pull: (defaults to 5)</label>
                <input class="vmb-input" type="number" min="1" max="5" step="1" name="individual_minimum_rating" id="individual_minimum_rating">
            </div>

            <div class="vmb-input-wrapper input-number">
                <label for="individual_reviews_to_pull">Reviews to Pull: (defaults to 10)</label>
                <input class="vmb-input" type="text" name="individual_reviews_to_pull" id="individual_reviews_to_pull">
            </div>
            
            <div class="vmb-input-wrapper input-select">
            <label for="individual_sync_resort_selector">Select Resort:</label>
            <?php 

                $resorts = get_posts(array('post_type' => 'resort', 'posts_per_page' => -1));
                        
                echo '<select name="individual_sync_resort_selector" required>';
                foreach ($resorts as $resort) {
                    echo '<option value="' . $resort->ID . '">' . $resort->post_title . '</option>';
                }
                echo '</select>';
            
            ?>
            </div>
            <button class="vmb-button sync-button" type="submit">Sync Reviews</button> 
        </form>

       


        <h4>Bulk Sync</h4>
        <form class="vmb-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        
            <input type="hidden" name="action" value="sync_vmb_reviews" />
            
            <button class="vmb-button sync-button" type="submit">Sync Reviews</button> 
        </form>

        <form class="vmb-form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="sync_vmb_specials" />
            
            <button class="vmb-button sync-button" type="submit">Sync Specials</button> 
        </form>
    </div>
</div>


<style>
input, select, textarea {
    display: block;
    margin-bottom: 10px;
    margin-top: 5px;
    min-width: 200px;
    padding: 5px 10px!important;
}
</style>