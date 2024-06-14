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

        $resorts = get_posts([
            'post_type' => 'resort',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);

        $auth = base64_encode($vmb_settings->guestdesk_username.':'.$vmb_settings->guestdesk_password);

        $headers = array(
            'Authorization: Basic '.$auth,
            'Content-Type: application/json',
            'Accept: application/json'
        );

        $sanitizedArray = array();

        if(!$synced) {
            foreach($resorts as $resort) {
                $siteName = get_field('site_name', $resort->ID);
                $resortID = $resort->ID;
                $resortName = get_the_title($resort->ID);
                
                $params = array(
                    "language" => "",
                    "requestId" => "",
                    "requestTime" => gmdate('Y-m-d\TH:i:s.v\Z'),
                    "sites" => array(
                        array(
                            'siteName' => $siteName
                        )
                    )
                );
    
                $results = $api_helper->GuestDeskApiRequest($endpoint, 'POST', $params, $headers);
    
                if($results['code'] == 'success') {
                    $response = $results['response'][$siteName];
                } else {
                    $api_helper->displayResponseMessage(['code' => 'fail', 'message' => 'API error!', 'response' => null]);
                }
    
                if(!empty($response)) {
    
                    foreach($response['Packages'] as $package) {
    
                        $packageID = $package['PackageId'];
                        $connectedProperty = $resortName;
    
                        $active = $package['Active'];
                        $promote = $package['Promote'];
                        $bookable = $package['Bookable'];
    
                        $startDate = $package['CalendarStartDate'];
                        $endDate = $package['CalendarEndDate'];
                        $displayName = $package['PackageDisplayName'];
                        $shortDescription = $package['PackageShortDescription'];
            
                        if($active && $bookable && $promote) {
                            $sanitized = array(
                                'id' => $packageID,
                                'resort_id' => $resortID,
                                'resort' => $connectedProperty,
                                'name' => $displayName,
                                'description' => $shortDescription,
                                'expiration' => $endDate
                            );
            
                            array_push($sanitizedArray, $sanitized);
                        }
    
                    }
    
                }
            }
        }

        update_option('vmb_api_cached_specials', json_encode($sanitizedArray));
        update_option('vmb_api_specials_synced', true);

        header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=".$results['code']."&msg=".(get_option('vmb_api_specials_synced', true) === null));
        exit;

    }

}