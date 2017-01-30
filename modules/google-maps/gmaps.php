<?php
/**
 * WP Google Maps API
 *
 * @package WP-Google-Maps-API
 */
 /*
* Plugin Name: WP Google Maps API
* Plugin URI: https://github.com/wp-api-libraries/wp-google-maps-api
* Description: Perform API requests to Google Maps API.
* Author: WP API Libraries, sgarza
* Version: 1.0.0
* Author URI: https://wp-api-libraries.com
* GitHub Plugin URI: https://github.com/wp-api-libraries/wp-google-maps-api
* GitHub Branch: master
*/
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }
if ( ! class_exists( 'GoogleMaps' ) ) {
	include_once('maps-widget.php');

	/**
	 * GreatSchools API Class.
	 */
	class GoogleMaps {

		/**
		 * API Key.
		 *
		 * @var string
		 */
		static private $api_key;

		/**
		 * Output Type.
		 *
		 * @var string
		 */
		static private $output;

		/**
		 * __construct function.
		 *
		 * @access public
		 * @param mixed $api_key API Key
		 * @param string $output (default: 'json') Output.
		 * @return void
		 */
		public function __construct( $api_key) {
			static::$api_key = $api_key;

			add_action( 'wp_footer', array($this, 'footer'),  11);
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
			add_shortcode( 'wp_google_maps', array( $this, 'shortcode' ) );
		}

		public function enqueue(){
			wp_enqueue_script( 'wpapi-google-maps',plugins_url( 'google-maps.js', __FILE__ ), array( 'jquery' ), null, true );
		}

		public function footer(){
			// Only enqueue google maps API if yoast hasnt enqueued it already.
			if( ! wp_script_is( 'maps-geocoder') ){
				wp_enqueue_script( 'google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . static::$api_key, array(), null );
			}
		}

		public static function print_map( $width, $height, $map_data ){
      $default = array(
        'lat' => '',
        'lng' => '',
				'map_info_content' => '',
        'style' => '[]',
      );

			$map_data = apply_filters( 'wpapi_google_map_data', wp_parse_args( $map_data, $default ) );
			wp_localize_script( 'wpapi-google-maps', 'idxfListing', $map_data );

			// Print Map
			echo '<div id="listing-map"><div id="map-canvas" style="width: ' . $width . '; height: ' . $height . '"></div></div><!-- .listing-map -->';
		}

		public function shortcode( $atts ) {
			// Set widget info.
			$atts = shortcode_atts(
				array(
					'width' => '100%',
					'height' => '300px',
					'info' => '',
					'lat' => '',
					'lng' => '',
				),
				$atts, 'wp_google_maps'
			);

			$map_data = array(
				'map_info_content' => $atts['info'],
				'lat' => $atts['lat'],
				'lng' => $atts['lng'],
			);

			static::print_map( $atts['width'], $atts['height'], $map_data);
		}

		/**
		 * Register idxFORZA Widgets.
		 *
		 * @access public
		 * @return void
		 */
		public function register_widgets() {
			register_widget( 'WP_API_MAPS_WIDGET' );
		}
	}

}

new GoogleMaps( 'AIzaSyBxEwigXKSxW53_7efZPagQ3uML-vX8rPY' );
