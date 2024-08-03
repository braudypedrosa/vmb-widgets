<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://https://buildupbookings.com/
 * @since      1.0.0
 *
 * @package    Vmb_Widgets
 * @subpackage Vmb_Widgets/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Vmb_Widgets
 * @subpackage Vmb_Widgets/public
 * @author     Braudy Pedrosa <braudy@buildupbookings.com>
 */
class Vmb_Widgets_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */



	// register custom endpoints
	function register_special_endpoints() {

		$json_data = get_option('vmb_specials_category', '[]');
    	$categories = json_decode($json_data, true);

		foreach ($categories as $category) {
			add_rewrite_rule(
				'^specialcode/' . $category['slug'] . '/?$',
				'index.php?specialcode=' . $category['name'],
				'top'
			);
		}
	}

	function add_specialcode_body_class($classes) {
		if ($specialcode = get_query_var('specialcode')) {
			$classes[] = 'specialcode-' . sanitize_html_class(vmb_slugify($specialcode));
			$classes[] = 'specialcode-page';

			$classes = array_diff($classes, array('home', 'blog'));
		}
		return $classes;
	}

	function add_specialcode_query_var($vars) {
		$vars[] = 'specialcode';
		return $vars;
	}

	function load_specialcode_template($template) {
		$specialcode = get_query_var('specialcode');
		if ($specialcode) {
			$plugin_template = plugin_dir_path(__FILE__) . 'partials/custom/specialcode-template.php';

			error_log('specialcode template: ' . $plugin_template );
			if (file_exists($plugin_template)) {
				return $plugin_template;
			}
		}
		return $template;
	}


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
		 
		wp_enqueue_style( $this->plugin_name .'_slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
		wp_enqueue_style( $this->plugin_name .'_slick_theme', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
		wp_enqueue_style( $this->plugin_name .'_font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');


		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/css/public-main.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name .'_slick', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array( 'jquery' ), $this->version, false );


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/js/public-main.js', array( 'jquery' ), $this->version, false );
	
	}

}
