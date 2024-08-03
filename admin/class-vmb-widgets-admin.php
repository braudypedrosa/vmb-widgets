<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://buildupbookings.com/
 * @since      1.0.0
 *
 * @package    Vmb_Widgets
 * @subpackage Vmb_Widgets/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vmb_Widgets
 * @subpackage Vmb_Widgets/admin
 * @author     Braudy Pedrosa <braudy@buildupbookings.com>
 */
class Vmb_Widgets_Admin {

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

	// register main plugin menu
	public function menu_init() {
        add_menu_page(
            __( 'VMB Settings', 'textdomain' ),
            'VMB Settings',
            'manage_options',
            'vmb_settings',
            function(){
                include_once(plugin_dir_path(dirname(__FILE__)) . 'admin/partials/vmb-widgets-admin-display.php');
            },
			'dashicons-store'
        ); 

		// Add submenu page
		add_submenu_page(
			'vmb_settings', 
			__( 'Manage Categories', 'textdomain' ),
			'Manage Categories', 
			'manage_options', 
			'manage_categories', 
			function() {
				include_once(plugin_dir_path(dirname(__FILE__)) . 'admin/partials/vmb-widgets-specials-category-admin-display.php');
			}
		);

		// Add submenu page
		add_submenu_page(
			'vmb_settings', 
			__( 'Manage Specials', 'textdomain' ),
			'Manage Specials', 
			'manage_options', 
			'manage_specials', 
			function() {
				include_once(plugin_dir_path(dirname(__FILE__)) . 'admin/partials/vmb-widgets-specials-admin-display.php');
			}
		);
	}

	// save settings
	public function save_settings(){

		$vmb_settings = array();

		if(isset($_POST['alchemer-token'])) {
			$vmb_settings['alchemer_token'] = $_POST['alchemer-token'];
		}

		if(isset($_POST['alchemer-secret'])) {
			$vmb_settings['alchemer_secret'] = $_POST['alchemer-secret'];
		}

		if(isset($_POST['guestdesk-username'])) {
			$vmb_settings['guestdesk_username'] = $_POST['guestdesk-username'];
		}

		if(isset($_POST['guestdesk-password'])) {
			$vmb_settings['guestdesk_password'] = $_POST['guestdesk-password'];
		}

		update_option('vmb_settings', json_encode($vmb_settings));


		$response = [
			'code' => 'success',
			'message' => 'Settings saved successfully!'
		];
	

		header("Location: " . get_bloginfo("url") . "/wp-admin/admin.php?page=vmb_settings&status=".$response['code']."&msg=".$response['message']);
        exit;
	}

	// register custom post type
	public function register_vmb_post_types() {
		$labels_review = [
			"name" => esc_html__( "VMB Reviews", "vmb_widgets" ),
			"singular_name" => esc_html__( "VMB Review", "vmb_widgets" ),
			"menu_name" => esc_html__( "VMB Reviews", "vmb_widgets" ),
			"all_items" => esc_html__( "All VMB Reviews", "vmb_widgets" ),
			"add_new" => esc_html__( "Add new", "vmb_widgets" ),
			"add_new_item" => esc_html__( "Add new VMB Review", "vmb_widgets" ),
			"edit_item" => esc_html__( "Edit VMB Review", "vmb_widgets" ),
			"new_item" => esc_html__( "New VMB Review", "vmb_widgets" ),
			"view_item" => esc_html__( "View VMB Review", "vmb_widgets" ),
			"view_items" => esc_html__( "View VMB Reviews", "vmb_widgets" ),
			"search_items" => esc_html__( "Search VMB Reviews", "vmb_widgets" ),
			"not_found" => esc_html__( "No VMB Reviews found", "vmb_widgets" ),
			"not_found_in_trash" => esc_html__( "No VMB Reviews found in trash", "vmb_widgets" ),
			"parent" => esc_html__( "Parent VMB Review:", "vmb_widgets" ),
			"featured_image" => esc_html__( "Featured image for this VMB Review", "vmb_widgets" ),
			"set_featured_image" => esc_html__( "Set featured image for this VMB Review", "vmb_widgets" ),
			"remove_featured_image" => esc_html__( "Remove featured image for this VMB Review", "vmb_widgets" ),
			"use_featured_image" => esc_html__( "Use as featured image for this VMB Review", "vmb_widgets" ),
			"archives" => esc_html__( "VMB Review archives", "vmb_widgets" ),
			"insert_into_item" => esc_html__( "Insert into VMB Review", "vmb_widgets" ),
			"uploaded_to_this_item" => esc_html__( "Upload to this VMB Review", "vmb_widgets" ),
			"filter_items_list" => esc_html__( "Filter VMB Reviews list", "vmb_widgets" ),
			"items_list_navigation" => esc_html__( "VMB Reviews list navigation", "vmb_widgets" ),
			"items_list" => esc_html__( "VMB Reviews list", "vmb_widgets" ),
			"attributes" => esc_html__( "VMB Reviews attributes", "vmb_widgets" ),
			"name_admin_bar" => esc_html__( "VMB Review", "vmb_widgets" ),
			"item_published" => esc_html__( "VMB Review published", "vmb_widgets" ),
			"item_published_privately" => esc_html__( "VMB Review published privately.", "vmb_widgets" ),
			"item_reverted_to_draft" => esc_html__( "VMB Review reverted to draft.", "vmb_widgets" ),
			"item_trashed" => esc_html__( "VMB Review trashed.", "vmb_widgets" ),
			"item_scheduled" => esc_html__( "VMB Review scheduled", "vmb_widgets" ),
			"item_updated" => esc_html__( "VMB Review updated.", "vmb_widgets" ),
			"parent_item_colon" => esc_html__( "Parent VMB Review:", "vmb_widgets" ),
		];
	
		$args_review = [
			"label" => esc_html__( "VMB Reviews", "vmb_widgets" ),
			"labels" => $labels_review,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"rest_namespace" => "wp/v2",
			"has_archive" => false,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"delete_with_user" => false,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"can_export" => false,
			"rewrite" => [ "slug" => "reviews", "with_front" => false ],
			"query_var" => true,
			"menu_icon" => "dashicons-format-quote",
			"supports" => [ "title", "custom-fields", "editor" ],
			"show_in_graphql" => false,
		];
	
		/**
		 * Post Type: VMB Specials.
		 */
	
		$labels_special = [
			"name" => esc_html__( "VMB Specials", "vmb_widgets" ),
			"singular_name" => esc_html__( "VMB Special", "vmb_widgets" ),
			"menu_name" => esc_html__( "VMB Specials", "vmb_widgets" ),
			"all_items" => esc_html__( "All VMB Specials", "vmb_widgets" ),
			"add_new" => esc_html__( "Add new", "vmb_widgets" ),
			"add_new_item" => esc_html__( "Add new VMB Special", "vmb_widgets" ),
			"edit_item" => esc_html__( "Edit VMB Special", "vmb_widgets" ),
			"new_item" => esc_html__( "New VMB Special", "vmb_widgets" ),
			"view_item" => esc_html__( "View VMB Special", "vmb_widgets" ),
			"view_items" => esc_html__( "View VMB Specials", "vmb_widgets" ),
			"search_items" => esc_html__( "Search VMB Specials", "vmb_widgets" ),
			"not_found" => esc_html__( "No VMB Specials found", "vmb_widgets" ),
			"not_found_in_trash" => esc_html__( "No VMB Specials found in trash", "vmb_widgets" ),
			"parent" => esc_html__( "Parent VMB Special:", "vmb_widgets" ),
			"featured_image" => esc_html__( "Featured image for this VMB Special", "vmb_widgets" ),
			"set_featured_image" => esc_html__( "Set featured image for this VMB Special", "vmb_widgets" ),
			"remove_featured_image" => esc_html__( "Remove featured image for this VMB Special", "vmb_widgets" ),
			"use_featured_image" => esc_html__( "Use as featured image for this VMB Special", "vmb_widgets" ),
			"archives" => esc_html__( "VMB Special archives", "vmb_widgets" ),
			"insert_into_item" => esc_html__( "Insert into VMB Special", "vmb_widgets" ),
			"uploaded_to_this_item" => esc_html__( "Upload to this VMB Special", "vmb_widgets" ),
			"filter_items_list" => esc_html__( "Filter VMB Specials list", "vmb_widgets" ),
			"items_list_navigation" => esc_html__( "VMB Specials list navigation", "vmb_widgets" ),
			"items_list" => esc_html__( "VMB Specials list", "vmb_widgets" ),
			"attributes" => esc_html__( "VMB Specials attributes", "vmb_widgets" ),
			"name_admin_bar" => esc_html__( "VMB Special", "vmb_widgets" ),
			"item_published" => esc_html__( "VMB Special published", "vmb_widgets" ),
			"item_published_privately" => esc_html__( "VMB Special published privately.", "vmb_widgets" ),
			"item_reverted_to_draft" => esc_html__( "VMB Special reverted to draft.", "vmb_widgets" ),
			"item_trashed" => esc_html__( "VMB Special trashed.", "vmb_widgets" ),
			"item_scheduled" => esc_html__( "VMB Special scheduled", "vmb_widgets" ),
			"item_updated" => esc_html__( "VMB Special updated.", "vmb_widgets" ),
			"parent_item_colon" => esc_html__( "Parent VMB Special:", "vmb_widgets" ),
		];
	
		$args_special = [
			"label" => esc_html__( "VMB Specials", "vmb_widgets" ),
			"labels" => $labels_special,
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => true,
			"rest_base" => "",
			"rest_controller_class" => "WP_REST_Posts_Controller",
			"rest_namespace" => "wp/v2",
			"has_archive" => false,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"delete_with_user" => false,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"can_export" => false,
			"rewrite" => [ "slug" => "specials", "with_front" => true ],
			"query_var" => true,
			"menu_icon" => 'dashicons-art',
			"supports" => [ "title", "custom-fields", "excerpt", 'editor' ],
			"show_in_graphql" => false,
		];
		
	
		register_post_type( "vmb_reviews", $args_review );
		// register_post_type( "vmb_specials", $args_special );
	}

	function register_vmb_taxonomy() {
	
		$labels = [
			"name" => esc_html__( "Categories", "vmb_widgets" ),
			"singular_name" => esc_html__( "Category", "vmb_widgets" ),
			"menu_name" => esc_html__( "Categories", "vmb_widgets" ),
			"all_items" => esc_html__( "All Categories", "vmb_widgets" ),
			"edit_item" => esc_html__( "Edit Category", "vmb_widgets" ),
			"view_item" => esc_html__( "View Category", "vmb_widgets" ),
			"update_item" => esc_html__( "Update Category name", "vmb_widgets" ),
			"add_new_item" => esc_html__( "Add new Category", "vmb_widgets" ),
			"new_item_name" => esc_html__( "New Category name", "vmb_widgets" ),
			"parent_item" => esc_html__( "Parent Category", "vmb_widgets" ),
			"parent_item_colon" => esc_html__( "Parent Category:", "vmb_widgets" ),
			"search_items" => esc_html__( "Search Categories", "vmb_widgets" ),
			"popular_items" => esc_html__( "Popular Categories", "vmb_widgets" ),
			"separate_items_with_commas" => esc_html__( "Separate Categories with commas", "vmb_widgets" ),
			"add_or_remove_items" => esc_html__( "Add or remove Categories", "vmb_widgets" ),
			"choose_from_most_used" => esc_html__( "Choose from the most used Categories", "vmb_widgets" ),
			"not_found" => esc_html__( "No Categories found", "vmb_widgets" ),
			"no_terms" => esc_html__( "No Categories", "vmb_widgets" ),
			"items_list_navigation" => esc_html__( "Categories list navigation", "vmb_widgets" ),
			"items_list" => esc_html__( "Categories list", "vmb_widgets" ),
			"back_to_items" => esc_html__( "Back to Categories", "vmb_widgets" ),
			"name_field_description" => esc_html__( "The name is how it appears on your site.", "vmb_widgets" ),
			"parent_field_description" => esc_html__( "Assign a parent term to create a hierarchy. The term Jazz, for example, would be the parent of Bebop and Big Band.", "vmb_widgets" ),
			"slug_field_description" => esc_html__( "The slug is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.", "vmb_widgets" ),
			"desc_field_description" => esc_html__( "The description is not prominent by default; however, some themes may show it.", "vmb_widgets" ),
		];
	
		
		$args = [
			"label" => esc_html__( "Categories", "vmb_widgets" ),
			"labels" => $labels,
			"public" => true,
			"publicly_queryable" => true,
			"hierarchical" => true,
			"show_ui" => true,
			"show_in_menu" => true,
			"show_in_nav_menus" => true,
			"query_var" => true,
			"rewrite" => [ 'slug' => 'specials_category', 'with_front' => true, ],
			"show_admin_column" => true,
			"show_in_rest" => true,
			"show_tagcloud" => false,
			"rest_base" => "specials_category",
			"rest_controller_class" => "WP_REST_Terms_Controller",
			"rest_namespace" => "wp/v2",
			"show_in_quick_edit" => true,
			"sort" => false,
			"show_in_graphql" => false,
		];

		register_taxonomy( "specials_category", [ "vmb_specials" ], $args );
	}


	// register custom columns
	public function set_vmb_custom_columns($columns) {

		$columns['connected-property'] = __( 'Connected Property', 'VMB' );
		$columns['visibility'] = __( 'Visibility', 'VMB' );
		return $columns;
		
	}

	// register exclusive columns for reviews
	public function set_vmb_reviews_custom_columns($columns) {
		$columns['review'] = __( 'Review', 'VMB' );
		return $columns;
	}

	// add column action
	public function vmb_custom_columns( $column, $post_id ) {

		if($column == 'connected-property') {
			echo get_post_meta($post_id, 'connected_property', true);
		}

		if($column == 'visibility') {
			echo (get_field('hide_from_query', $post_id) ? 'Hidden' : 'Visible');
		}

		if($column == 'review') {
			$comment = (get_the_content($post_id) != '') ? get_the_content($post_id) : get_post_meta($post_id, 'vmb_review_comment', true);
			echo $comment;
		}

	}

	// add connected property filter
	public function connected_property_filter() {
		global $typenow;
		$helper = new VMB_HELPER();

		if($typenow == 'vmb_reviews' || $typenow == 'vmb_specials') {

			$resorts = get_posts(array('post_type' => 'resort', 'posts_per_page' => -1));
		
			echo '<select name="resort_filter">';
			echo '<option value="">Select a Resort</option>';
			foreach ($resorts as $resort) {
				$selected = (isset($_GET['resort_filter']) && $_GET['resort_filter'] == $helper->slugify($resort->post_title)) ? ' selected="selected"' : '';
				echo '<option value="' . $helper->slugify($resort->post_title) . '"' . $selected . '>' . $resort->post_title . '</option>';
			}
			echo '</select>';

		}
	}


	public function filter_by_resort($query) {
		global $pagenow;

		$helper = new VMB_HELPER();

		if ($pagenow == 'edit.php' && isset($_GET['resort_filter']) && $_GET['resort_filter'] != '' && ($query->query_vars['post_type'] == 'vmb_reviews' || $query->query_vars['post_type'] == 'vmb_specials')) {
			$query->set('meta_query', array(
				array(
					'key' => 'connected_property',
					'value' => $helper->unslugify($_GET['resort_filter']),
					'compare' => '='
				)
			));
		}
	}

	function save_table() {
		$jsonString = isset($_POST['jsonData']) ? $_POST['jsonData'] : '';
    
		// Remove backslashes
		$cleanedJsonString = stripslashes($jsonString);

		error_log( 'Table Data: ' . $cleanedJsonString );

		update_option('vmb_api_cached_specials', $cleanedJsonString);
		update_option('vmb_api_specials_synced', false);
	
		wp_send_json_success(array('message' => 'Specials stored in cache!'));
	}

	function get_specials_meta() {
		if(!isset($_POST['option'])) {
			wp_send_json_error('Option not found');
		}
		
		$option = $_POST['option'];

		$data = get_option($option, '[]');
    	wp_send_json_success(json_decode($data));
	}

	function save_specials_meta() {
		if (!current_user_can('manage_options')) {
			wp_send_json_error();
		}
	
		$categories = json_decode(stripslashes($_POST['data']), true);
		$option = sanitize_text_field($_POST['option']);
	
		// Get the existing categories
		$existing_categories = get_option($option, array());
	
		// Ensure $existing_categories is always an array
		if (!is_array($existing_categories)) {
			$existing_categories = array();
		}
	
		// Validate categories and check for duplicates
		$slugs = array_map(function($category) {
			return $category['slug'];
		}, $existing_categories);
	
		foreach ($categories as $category) {
			if (empty($category['name']) || empty($category['slug'])) {
				wp_send_json_error(array('message' => 'Category name and slug are required.'));
			}
	
			if (in_array($category['slug'], $slugs)) {
				wp_send_json_error(array('message' => 'Duplicate category found: ' . $category['slug']));
			}
	
			// Add the new slug to the array for checking against subsequent categories
			$slugs[] = $category['slug'];
		}
	
		// Merge the existing categories with the new ones
		$updated_categories = array_merge($existing_categories, $categories);
	
		if (update_option($option, json_encode($updated_categories))) {
			// Set a transient to indicate that rewrite rules need to be flushed
			set_transient('flush_rewrite_rules_needed', true, 60); // Valid for 1 minute
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	function delete_specials_category() {
		if (!isset($_POST['index'])) {
			wp_send_json_error('No index provided');
		}
	
		$index = intval($_POST['index']);
		$categories = get_option('vmb_specials_category', '[]');
		$categories = json_decode($categories, true);
	
		if (!isset($categories[$index])) {
			wp_send_json_error('Index out of bounds');
		}
	
		array_splice($categories, $index, 1);
		update_option('vmb_specials_category', json_encode($categories));
		wp_send_json_success();
	}



	function conditionally_flush_rewrite_rules() {
		if (get_transient('flush_rewrite_rules_needed')) {
			flush_rewrite_rules();
			delete_transient('flush_rewrite_rules_needed');
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vmb_Widgets_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vmb_Widgets_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$screen = get_current_screen();

		error_log('Current screen ID: ' . $screen->id); // Log the screen ID

		if( ($screen->id === 'toplevel_page_vmb_settings') || ($screen->id === 'vmb-settings_page_manage_categories') || ($screen->id === 'vmb-settings_page_manage_specials') ) {

			// Data tables
			wp_enqueue_style( $this->plugin_name .'_datatables', 'https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.min.css');

			// Bootstrap
			wp_enqueue_style( $this->plugin_name .'_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');

		}


		wp_enqueue_style( $this->plugin_name . '_admin_styles', plugin_dir_url( __FILE__ ) . 'dist/css/admin-main.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vmb_Widgets_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vmb_Widgets_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		

		$screen = get_current_screen();

		error_log('Current screen ID: ' . $screen->id); // Log the screen ID

		if( ($screen->id === 'toplevel_page_vmb_settings') || ($screen->id === 'vmb-settings_page_manage_categories') || ($screen->id === 'vmb-settings_page_manage_specials') ) {
			
			// Bootstrap
			wp_enqueue_script( $this->plugin_name .'_bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, false );

			// Data tables
			wp_enqueue_script( $this->plugin_name .'_datatables', 'https://cdn.datatables.net/2.1.3/js/dataTables.min.js', array( 'jquery' ), $this->version, false );
			
			// Sweet Alert
			wp_enqueue_script( $this->plugin_name . '_sweetalert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', null, $this->version, false );

		}


		wp_enqueue_script( $this->plugin_name . '_admin_scripts', plugin_dir_url( __FILE__ ) . 'dist/js/admin-main.js', array( 'jquery' ), $this->version, false );
		
		$cached_specials = get_option('vmb_api_cached_specials', true);
		$cached_specials_category = get_option('vmb_specials_category', true);
		$api_synced = get_option('vmb_api_specials_synced', true);

		wp_localize_script( $this->plugin_name . '_admin_scripts', 'vmb_ajax',  
			array(
				'cached_specials' => $cached_specials,
				'cached_special_categories' => $cached_specials_category,
				'ajax_url' => admin_url('admin-ajax.php'),
        		'nonce' => wp_create_nonce('specials_nonce')
			)
		);

		

	}

}
