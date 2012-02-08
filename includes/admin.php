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
		add_action( 'admin_footer', array( &$this, 'bind_upload_cb' ) );
		add_filter( 'media_meta', array( &$this, 'media_meta_hack'), 10, 1);
		add_filter( 'media_upload_form_url', array( &$this, 'post_upload_handler' ) );

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
	 * Callback to inject JavaScript in page after upload is complete (pre 3.3)
	 *
	 * Adapted from WP Document Revisions
	 *
	 * @param int $id the ID of the attachment
	 * @return string the JS to insert
	 */
	function post_upload_js( $id ) {

		if ( !isset( $_GET[ $this->parent->slug_ ] ) )
			return;
		
		$url = wp_get_attachment_url( $id );
		return "<script>var url = $url; jQuery(document).ready(function($) { postImageUpload( url ) });</script>";

	}


	/**
	 * Binds our post-upload javascript callback to the plupload event
	 * Note: in footer because it has to be called after handler.js is loaded and initialized
	 *
	 * Adapted from WP Document Revisions
	 */
	function bind_upload_cb() {

		global $pagenow;
		if ( $pagenow != 'media-upload.php' )
			return;

		?><script>jQuery(document).ready(function(){bindPostImageUploadCB()});</script><?php

	}


	/**
	 * Ugly, Ugly hack to sneak post-upload JS into the iframe *pre 3.3*
	 * If there was a hook there, I wouldn't have to do this
	 *
	 * Adapted from WP Document Revisions
	 *
	 * @param string $meta dimensions / post meta
	 * @returns string meta + js to process post
	 */
	function media_meta_hack( $meta ) {

		if ( !isset( $_GET[ $this->parent->slug_ ] ) )
			return $meta;

		$meta .= $this->post_upload_js( );

		return $meta;

	}


	/**
	 * Hook to follow file uploads to automatically close when sucessfull
	 *
	 * Adapted from WP Document Revisions
	 *
	 * @todo verify proper parent page
	 * @param string $filter whatever we really should be filtering
	 * @returns string the same stuff they gave us, like we were never here
	 */
	function post_upload_handler( $filter ) {

		//if we're not posting this is the initial form load, kick
		if ( !$_POST )
			return $filter;

		echo $this->post_upload_js( $latest->ID );

		//prevent hook from fireing a 2nd time
		remove_filter( 'media_meta', array( &$this, 'media_meta_hack'), 10, 1);

		//should probably give this back...
		return $filter;

	}


}