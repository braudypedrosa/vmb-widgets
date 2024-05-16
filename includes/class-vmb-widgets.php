<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://buildupbookings.com/
 * @since      1.0.0
 *
 * @package    Vmb_Widgets
 * @subpackage Vmb_Widgets/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Vmb_Widgets
 * @subpackage Vmb_Widgets/includes
 * @author     Braudy Pedrosa <braudy@buildupbookings.com>
 */
class Vmb_Widgets {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Vmb_Widgets_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'VMB_WIDGETS_VERSION' ) ) {
			$this->version = VMB_WIDGETS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'vmb-widgets';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Vmb_Widgets_Loader. Orchestrates the hooks of the plugin.
	 * - Vmb_Widgets_i18n. Defines internationalization functionality.
	 * - Vmb_Widgets_Admin. Defines all hooks for the admin area.
	 * - Vmb_Widgets_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vmb-widgets-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vmb-widgets-i18n.php';
		

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vmb-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vmb-api-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vmb-widgets-admin.php';


		// ACF load/save points

		if(class_exists('ACF')) {

			add_filter('acf/settings/save_json', 'vmb_widgets_json_save_point');
			function vmb_widgets_json_save_point( $path ) {
				$path = plugin_dir_path( dirname( __FILE__ ) ) . '/acf-json';
				return $path;
			}

			add_filter('acf/settings/load_json', 'vmb_widgets_json_load_point');
			function vmb_widgets_json_load_point( $paths ) {        
				unset($paths[0]);
				$paths[] = plugin_dir_path( dirname( __FILE__ ) ) . '/acf-json';
				return $paths;
			}
			
		}


		// function classes
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/functions/class-vmb-reviews-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/functions/class-vmb-specials-functions.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/helpers/shortcode-helpers.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-vmb-widgets-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/shortcodes/shortcodes.php';

		$this->loader = new Vmb_Widgets_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Vmb_Widgets_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Vmb_Widgets_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Vmb_Widgets_Admin( $this->get_plugin_name(), $this->get_version() );
		$reviews_functions = new Vmb_Reviews_Functions ( $this->get_plugin_name(), $this->get_version() );
		$specials_functions = new Vmb_Specials_Functions ( $this->get_plugin_name(), $this->get_version() );

		// initialization hooks
		$this->loader->add_filter( 'admin_menu', $plugin_admin, 'menu_init' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_vmb_post_types' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_vmb_taxonomy' );

		

		// custom columns
		$this->loader->add_filter('manage_vmb_reviews_posts_columns', $plugin_admin, 'set_vmb_reviews_custom_columns');

		$this->loader->add_filter('manage_vmb_reviews_posts_columns', $plugin_admin, 'set_vmb_custom_columns');
		$this->loader->add_filter('manage_vmb_specials_posts_columns', $plugin_admin, 'set_vmb_custom_columns');

		$this->loader->add_action('manage_vmb_reviews_posts_custom_column', $plugin_admin, 'vmb_custom_columns', 10, 2);
		$this->loader->add_action('manage_vmb_specials_posts_custom_column', $plugin_admin, 'vmb_custom_columns', 10, 2);

		// filter hooks
		$this->loader->add_action('restrict_manage_posts', $plugin_admin, 'connected_property_filter', 10, 2);
		$this->loader->add_action('pre_get_posts', $plugin_admin, 'filter_by_resort', 10, 2);

		// admin function hooks
		$this->loader->add_action( 'admin_post_save_vmb_settings', $plugin_admin, 'save_settings' );

		// reviews function hooks
		$this->loader->add_action( 'admin_post_sync_vmb_reviews', $reviews_functions, 'sync_reviews');
		$this->loader->add_action( 'admin_post_individual_sync_vmb_reviews', $reviews_functions, 'individual_sync_reviews');

		// specials function hooks
		$this->loader->add_action( 'admin_post_sync_vmb_specials', $specials_functions, 'sync_specials');

		// enqueue hooks
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Vmb_Widgets_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Vmb_Widgets_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
