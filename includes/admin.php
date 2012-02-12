<?php
/**
 * Infinite Scroll Administrative Backend
 * @subpackage Admin
 * @author Benjamin J. Balter <ben@balter.com>
 * @package Infinite_Scroll
 */

class Infinite_Scroll_Admin {

	private $parent;

	/**
	 * Register hooks with WordPress API
	 * @param class $parent (reference) the Parent Class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		add_action( 'admin_menu', array( &$this, 'options_menu_init' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue' ) );

		//upload helers
		add_filter( 'get_media_item_args', array( &$this, 'send_to_editor'), 10, 1);

	}

	/**
	 * Register our menu with WordPress
	 */
	function options_menu_init() {
		add_options_page( __( 'Infinite Scroll Options', 'infinite-scroll' ), __( 'Infinite Scroll', 'infinite-scroll' ), 'manage_options', 'infinite_scroll_options', array( &$this, 'options' ) );
	}


	/**
	 * Callback to load options template
	 */
	function options() {

		//toggle presets page
		$file = isset( $_GET['manage-presets'] ) ? 'manage-presets' : 'options';

		require dirname( $this->parent->file ) . '/templates/' . $file . '.php';
	}

	/**
	 * Enqueue admin JS on options page
	 */
	function admin_enqueue() {

		if ( get_current_screen()->id != 'settings_page_infinite_scroll_options' && !defined( 'IFRAME_REQUEST' ) )
			return;
		
		$suffix = ( WP_DEBUG || SCRIPT_DEBUG ) ? '.dev' : '';
		$file = "/js/admin/infinite-scroll{$suffix}.js";

		wp_enqueue_script( $this->parent->slug, plugins_url( $file, $this->parent->file ), array( 'jquery', 'media-upload', 'thickbox' ), $this->parent->version, true );
		wp_enqueue_style('thickbox');

		wp_localize_script( $this->parent->slug, $this->parent->slug_, array( 'confirm' => __( 'Are you sure you want to delete the preset "%s"?', 'infinite-scroll' ) ) );

	}
	
	/**
	 * If image is sucessfully uploaded, automatically close the editor 
	 * and store the image URL in the image input
	 * @param array $args the default args
	 * @return array the original args, unmodified
	 * @uses media_send_to_editor()
	 & @uses send_to_editor() (javascript)
	 */
	function send_to_editor( $args ) {
		
		if ( $args['errors'] !== null )
			return $args;
		
		//rely on WordPress's internal function to output script tags and call send_to_editor()
		media_send_to_editor( wp_get_attachment_url( $_GET['attachment_id'] ) );
		
		return $args;
		
	}


}