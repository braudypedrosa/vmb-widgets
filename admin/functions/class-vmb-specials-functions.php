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


    public function sync_specials() {

        $helper = new VMB_API_HELPER();
        $vmb_settings = json_decode(get_option('vmb_settings'));


        $resorts = get_posts([
            'post_type' => 'resort',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);

        

        $endpoint = 'https://external.guestdesk.com/partner/v1/System/Packages';

        foreach($resorts as $resort) {

            // $resortID = get_field('site_id', $resort->ID);
            $resortName = get_field('site_name', $resort->ID);
            $connectedProperty = $resort->post_title;

            

            $params = array(
                "language" => "",
                "requestId" => "",
                "requestTime" => gmdate('Y-m-d\TH:i:s.v\Z'),
                "sites" => array(
                    array(
                        "siteName" => $resortName
                    )
                )
            );

            $auth = base64_encode($vmb_settings->guestdesk_username.':'.$vmb_settings->guestdesk_password);

            $headers = array(
                'Authorization: Basic '.$auth,
                'Content-Type: application/json',
                'Accept: application/json'
            );

            $results = $helper->GuestDeskApiRequest($endpoint, 'POST', $params, $headers, 'Specials synced successfully!');
            
            if($results['code'] == 'success') {
                update_post_meta($resort->ID, 'specials_' . $resortName, $results['response']);
                $helper->generateVMBSpecials($results['response'][$resortName]['Packages'], $connectedProperty, $resort->ID);
            }

        }


        
        header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=".$results['code']."&msg=".$results['message']);
        exit;
    } 

}