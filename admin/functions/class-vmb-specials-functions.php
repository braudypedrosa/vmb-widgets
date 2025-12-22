<?php 


class Vmb_Specials_Functions {


    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $vmb_widgets    The ID of this plugin.
	 */
	private $vmb_widgets;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    private $helper;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $vmb_widgets       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $vmb_widgets, $version ) {

		$this->plugin_name = $vmb_widgets;
		$this->version = $version;
        $this->helper = new VMB_HELPER();

	}

    public function individual_sync_special() {

        $api_helper = new VMB_API_HELPER();
        $vmb_settings = json_decode(get_option('vmb_settings'));
    
        $endpoint = 'https://external.guestdesk.com/partner/v1/System/Packages';
        $message = $code = '';

        $resortID = $_POST['individual_sync_resort_selector'] ;
    
        $resort = get_post($resortID);

        error_log('Resort ID: ' . $resortID);

        if (!$resort || $resort->post_type !== 'resort' || $resort->post_status !== 'publish') {
            $message = 'Invalid resort ID!';
            header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=error&msg=" . $message );
            exit;
        }
    
        $auth = base64_encode($vmb_settings->guestdesk_username . ':' . $vmb_settings->guestdesk_password);
    
        $headers = array(
            'Authorization: Basic ' . $auth,
            'Content-Type: application/json',
            'Accept: application/json'
        );

        $cached_specials = array();
    
        $siteName = get_field('site_name', $resort->ID);
        $resortName = get_the_title($resort->ID);

        $sanitizedArray = array();

        $existingSpecials = json_decode(get_post_meta($resortID, 'vmb_resort_specials', true), true) ?: [];

        // error_log('Existing Specials: ' . print_r($existingSpecials, true));

        $newSpecialsIDs = array();

        $params = array(
            "language" => "",
            "requestId" => "",
            "requestTime" => gmdate('Y-m-d\TH:i:s.v\Z'),
            "sites" => array(
                array('siteName' => $siteName)
            )
        );

        $results = $api_helper->GuestDeskApiRequest($endpoint, 'POST', $params, $headers);

        if ($results['code'] !== 'success') {
            $message = 'Sync error!';
            header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=error&msg=" . $message );
            exit;
        } else {
            $message = "Specials synced successfully!";
        }

        $code = $results['code'];

        $response = $results['response'][$siteName] ?? [];

        foreach ($response['Packages'] as $package) {
            $packageID = $package['PackageId'];
            $newSpecialsIDs[] = $packageID;

            $active = $package['Active'];
            $promote = $package['Promote'];
            $bookable = $package['Bookable'];

            if ($active && $bookable && $promote) {
                $sanitized = array(
                    'id' => $packageID,
                    'resort_id' => $resortID,
                    'resort' => $resortName,
                    'name' => $package['PackageDisplayName'],
                    'description' => $package['PackageShortDescription'],
                    'start' => $package['CalendarStartDate'],
                    'expiration' => $package['CalendarEndDate'],
                    'category' => $package['AvailablePromoCodes'],
                    'modified' => false,
                    'disable' => false
                );

                // Check if this special exists in the current cache and if it's marked as modified
                $existingSpecialKey = array_search($packageID, array_column($existingSpecials, 'id'));

                if ($existingSpecialKey === false || !isset($existingSpecials[$existingSpecialKey]['modified']) || !$existingSpecials[$existingSpecialKey]['modified']) {
                    $sanitizedArray[] = $sanitized;
                } else {
                    
                    // always update promo code regardless if the special is modified or not
                    $existingSpecials[$existingSpecialKey]['promo_code'];

                    // Keep the existing special if it's modified
                    $sanitizedArray[] = $existingSpecials[$existingSpecialKey];
                }
            }

            // generate promo codes
            $this->create_promo($package['AvailablePromoCodes']);
        }

        // Filter the sanitized array to remove specials not in the new specials IDs
        $sanitizedArray = array_filter($sanitizedArray, function ($special) use ($newSpecialsIDs) {
            return in_array($special['id'], $newSpecialsIDs);
        });
    
        // Update the option with the combined array
        update_post_meta($resortID, 'vmb_resort_specials', json_encode(array_values($sanitizedArray), JSON_UNESCAPED_UNICODE)); // Ensure array is reindexed
        $cached_specials = array_merge($cached_specials, array_values($sanitizedArray));

        update_option('vmb_api_cached_specials', json_encode($cached_specials));
    
        header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=" . $code . "&msg=" . $message );
        exit;
    }

    public function sync_specials() {

        $api_helper = new VMB_API_HELPER();
        $vmb_settings = json_decode(get_option('vmb_settings'));
    
        $endpoint = 'https://external.guestdesk.com/partner/v1/System/Packages';
        $message = $code = '';
    
        $resorts = get_posts([
            'post_type' => 'resort',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);
    
        $auth = base64_encode($vmb_settings->guestdesk_username . ':' . $vmb_settings->guestdesk_password);
    
        $headers = array(
            'Authorization: Basic ' . $auth,
            'Content-Type: application/json',
            'Accept: application/json'
        );

        $cached_specials = array();
    
        
        foreach ($resorts as $resort) {
            $siteName = get_field('site_name', $resort->ID);
            $resortID = $resort->ID;
            $resortName = get_the_title($resort->ID);

            $sanitizedArray = array();
            $existingSpecials = json_decode(get_post_meta($resortID, 'vmb_resort_specials', true), true) ?: [];
            $newSpecialsIDs = array();

            $params = array(
                "language" => "",
                "requestId" => "",
                "requestTime" => gmdate('Y-m-d\TH:i:s.v\Z'),
                "sites" => array(
                    array('siteName' => $siteName)
                )
            );

            $results = $api_helper->GuestDeskApiRequest($endpoint, 'POST', $params, $headers);

            if ($results['code'] !== 'success') {
                $message = 'Sync error!';
                continue; // Skip to the next resort if there's an API error
            } else {
                $message = "Specials synced successfully!";
            }

            $code = $results['code'];

            $response = $results['response'][$siteName] ?? [];

            foreach ($response['Packages'] as $package) {
                $packageID = $package['PackageId'];
                $newSpecialsIDs[] = $packageID;

                $active = $package['Active'];
                $promote = $package['Promote'];
                $bookable = $package['Bookable'];

                if ($active && $bookable && $promote) {
                    $sanitized = array(
                        'id' => $packageID,
                        'resort_id' => $resortID,
                        'resort' => $resortName,
                        'name' => $package['PackageDisplayName'],
                        'description' => $package['PackageShortDescription'],
                        'start' => $package['CalendarStartDate'],
                        'expiration' => $package['CalendarEndDate'],
                        'category' => $package['AvailablePromoCodes'],
                        'modified' => false,
                        'disable' => false
                    );

                    // Check if this special exists in the current cache and if it's marked as modified
                    $existingSpecialKey = array_search($packageID, array_column($existingSpecials, 'id'));

                    if ($existingSpecialKey === false || !isset($existingSpecials[$existingSpecialKey]['modified']) || !$existingSpecials[$existingSpecialKey]['modified']) {
                        $sanitizedArray[] = $sanitized;
                    } else {
                        
                        // always update promo code regardless if the special is modified or not
                        $existingSpecials[$existingSpecialKey]['promo_code'];

                        // Keep the existing special if it's modified
                        $sanitizedArray[] = $existingSpecials[$existingSpecialKey];
                    }
                }

                // generate promo codes
                $this->create_promo($package['AvailablePromoCodes']);
            }

            // Filter the sanitized array to remove specials not in the new specials IDs
            $sanitizedArray = array_filter($sanitizedArray, function ($special) use ($newSpecialsIDs) {
                return in_array($special['id'], $newSpecialsIDs);
            });
        
            // Update the option with the combined array
            update_post_meta($resortID, 'vmb_resort_specials', json_encode(array_values($sanitizedArray), JSON_UNESCAPED_UNICODE)); // Ensure array is reindexed
            $cached_specials = array_merge($cached_specials, array_values($sanitizedArray));
        }

        update_option('vmb_api_cached_specials', json_encode($cached_specials));
    
        header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=" . $code . "&msg=" . $message );
        exit;
    }

    private function create_promo($promoCode) {

        // Get all existing promo codes
        $currentPromos = get_option('vmb_specials_category') ? json_decode(get_option('vmb_specials_category', true)) : [];
        $newPromos = [];
    
        foreach($promoCode as $code) {
            // Slugify the promo code
            $promoSlug = strtolower($this->helper->slugify($code));
    
            // Check if the current promo code already exists
            $existingPromo = false;
            foreach($currentPromos as $promo) {
                if ($promo->slug === $promoSlug) {
                    $existingPromo = true;
                    break;
                }
            }
    
            // If the promo code does not exist, add it to the new promos array
            if(!$existingPromo) {
                $newPromos[] = array(
                    "name" => $promoSlug,
                    "slug" => $promoSlug
                );
            }
        }
    
        // Merge the new promos with the existing ones
        if (!empty($newPromos)) {
            $updatedPromos = array_merge($currentPromos, $newPromos);
            update_option('vmb_specials_category', json_encode(array_values($updatedPromos)));
            // error_log('Updated Promo Stack: ' . print_r($updatedPromos, true));
        } else {
            // error_log('No new promos to add.');
        }
    }
 
}