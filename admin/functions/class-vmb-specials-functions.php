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

	}


    // public function sync_specials() {

    //     $helper = new VMB_API_HELPER();
    //     $vmb_settings = json_decode(get_option('vmb_settings'));


    //     $resorts = get_posts([
    //         'post_type' => 'resort',
    //         'post_status' => 'publish',
    //         'numberposts' => -1
    //     ]);

        

    //     $endpoint = 'https://external.guestdesk.com/partner/v1/System/Packages';

    //     foreach($resorts as $resort) {

    //         // $resortID = get_field('site_id', $resort->ID);
    //         $resortName = get_field('site_name', $resort->ID);
    //         $connectedProperty = $resort->post_title;

            

    //         $params = array(
    //             "language" => "",
    //             "requestId" => "",
    //             "requestTime" => gmdate('Y-m-d\TH:i:s.v\Z'),
    //             "sites" => array(
    //                 array(
    //                     "siteName" => $resortName
    //                 )
    //             )
    //         );

    //         $auth = base64_encode($vmb_settings->guestdesk_username.':'.$vmb_settings->guestdesk_password);

    //         $headers = array(
    //             'Authorization: Basic '.$auth,
    //             'Content-Type: application/json',
    //             'Accept: application/json'
    //         );

    //         $results = $helper->GuestDeskApiRequest($endpoint, 'POST', $params, $headers, 'Specials synced successfully!');
            
    //         if($results['code'] == 'success') {
    //             update_post_meta($resort->ID, 'specials_' . $resortName, $results['response']);
    //             $helper->generateVMBSpecials($results['response'][$resortName]['Packages'], $connectedProperty, $resort->ID);
    //         }

    //     }


        
    //     header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=".$results['code']."&msg=".$results['message']);
    //     exit;
    // }

    public function sync_specials() {

        $api_helper = new VMB_API_HELPER();
        $vmb_settings = json_decode(get_option('vmb_settings'));
        $synced = (get_option('vmb_api_specials_synced') != '') ? get_option('vmb_api_specials_synced') : false;
    
        $endpoint = 'https://external.guestdesk.com/partner/v1/System/Packages';
        $message = '';
    
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
    
        $sanitizedArray = array();
        $existingSpecials = json_decode(get_option('vmb_api_cached_specials'), true) ?: [];
        $newSpecialsIDs = array();
    
        if (!$synced) {
            foreach ($resorts as $resort) {
                $siteName = get_field('site_name', $resort->ID);
                $resortID = $resort->ID;
                $resortName = get_the_title($resort->ID);
    
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
                    $api_helper->displayResponseMessage(['code' => 'fail', 'message' => 'API error!', 'response' => null]);
                    continue; // Skip to the next resort if there's an API error
                } else {
                    $message = "Specials synced successfully!";
                }
    
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
                            'expiration' => $package['CalendarEndDate'],
                            'modified' => false
                        );
    
                        // Check if this special exists in the current cache and if it's marked as modified
                        $existingSpecialKey = array_search($packageID, array_column($existingSpecials, 'id'));
    
                        if ($existingSpecialKey === false || !isset($existingSpecials[$existingSpecialKey]['modified']) || !$existingSpecials[$existingSpecialKey]['modified']) {
                            $sanitizedArray[] = $sanitized;
                        } else {
                            // Keep the existing special if it's modified
                            $sanitizedArray[] = $existingSpecials[$existingSpecialKey];
                        }
                    }
                }
            }
    
            // Filter the sanitized array to remove specials not in the new specials IDs
            $sanitizedArray = array_filter($sanitizedArray, function ($special) use ($newSpecialsIDs) {
                return in_array($special['id'], $newSpecialsIDs);
            });
    
            // Update the option with the combined array
            update_option('vmb_api_cached_specials', json_encode(array_values($sanitizedArray))); // Ensure array is reindexed
            update_option('vmb_api_specials_synced', true);
        }
    
        header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=" . $results['code'] . "&msg=" . $message );
        exit;
    }
    
    

}