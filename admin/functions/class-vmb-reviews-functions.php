<?php 


class Vmb_Reviews_Functions {


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

    public function sync_reviews() {

        $helper = new VMB_API_HELPER();
        $vmb_settings = json_decode(get_option('vmb_settings'));


        $resorts = get_posts([
            'post_type' => 'resort',
            'post_status' => 'publish',
            'numberposts' => -1
        ]);

        foreach($resorts as $resort) {

            $resortID = get_field('site_id', $resort->ID);
            $connectedProperty = $resort->post_title;

            $reviewFieldId = get_field('review_field_id', $resort->ID);

            $endpoint = 'https://api.alchemer.com/v5/survey/'.$resortID.'/surveyresponse';

            $params = array (
                'api_token' => $vmb_settings->alchemer_token,
                'api_token_secret' => $vmb_settings->alchemer_secret,
                'resultsperpage' => 20,
                'filter[field][0]' => '[question('.$reviewFieldId.')]',
                'filter[operator][0]' => 'IS NOT NULL',
                'filter[field][1]' => '[question('.$reviewFieldId.')]',
                'filter[operator][1]' => '>=',
                'filter[value][1]' => '5',
                'order_by' => '-date_submitted'
            );

            if($reviewFieldId != null) {

                $results = $helper->AlchemerApiRequest($endpoint, 'GET', $params, array(), 'Reviews synced successfully!');
            
                if($results['code'] == 'success') {
                    update_post_meta( $resort->ID, 'reviews_' .$resortID, $results['response']);

                    $helper->generateVMBReview($results['response'], $reviewFieldId, $connectedProperty);
                }

            } 

        }

        header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=".$results['code']."&msg=".$results['message']);
        exit;

    }



    public function individual_sync_reviews() {

        $helper = new VMB_API_HELPER();
        $vmb_settings = json_decode(get_option('vmb_settings'));


        $resortID = $_POST['individual_sync_resort_selector'] ;
        $minimum_rating = $_POST['individual_minimum_rating'] ? $_POST['individual_minimum_rating'] : 5;
        $limit = $_POST['individual_reviews_to_pull'] ? $_POST['individual_reviews_to_pull'] : 10;


        $siteID = get_field('site_id', $resortID);
        $connectedProperty = get_the_title($resortID);

        $reviewFieldId = get_field('review_field_id', $resortID);

        $endpoint = 'https://api.alchemer.com/v5/survey/'.$siteID.'/surveyresponse';

        $params = array (
            'api_token' => $vmb_settings->alchemer_token,
            'api_token_secret' => $vmb_settings->alchemer_secret,
            'resultsperpage' => $limit,
            'filter[field][0]' => '[question('.$reviewFieldId.')]',
            'filter[operator][0]' => 'IS NOT NULL',
            'filter[field][1]' => '[question('.$reviewFieldId.')]',
            'filter[operator][1]' => '>=',
            'filter[value][1]' => $minimum_rating,
            'order_by' => '-date_submitted'
        );

        if($reviewFieldId != null) {

            $results = $helper->AlchemerApiRequest($endpoint, 'GET', $params, array(), 'Reviews for '.$connectedProperty.' synced successfully!');
        
            if($results['code'] == 'success') {
                update_post_meta( $resortID, 'reviews_' .$siteID, $results['response']);

                $helper->generateVMBReview($results['response'], $reviewFieldId, $connectedProperty);
            }
            
        } 


        header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=".$results['code']."&msg=".$results['message']."&resortID=".$resortID);
        exit;

    }

}