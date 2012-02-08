<?php
/**
 * Provides interface to store and retrieve plugin and user options
 * @subpackage Infinite_Scroll_Options
 * @author Benjamin J. Balter
 * @package Infinite_Scroll
 */
class Infinite_Scroll_Options {

	//default scope for options when called directly,
	//choices: site, user, or global (user option across sites)
	public $defaults = array();
	private $parent;

	/**
	 * Stores parent class as static
	 * @param class $parent (reference) the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		add_action( 'admin_init', array( &$this, 'options_init' ) );
		add_action( $this->parent->prefix . 'options', array( &$this, 'default_options_filter' ), 20 );

	}


	/**
	 * Tells WP that we're using a custom settings field
	 */
	function options_init() {

		register_setting( $this->parent->slug_, $this->parent->slug_, array( &$this, 'validate' ) );

	}


	/**
	 * Runs options through filter prior to saving
	 * @param array $options the options array
	 * @return array sanitized options array
	 */
	function validate( $options ) {

		//add slashes to JS selectors
		$js = array ( 'nextSelector', 'navSelector', 'itemSelector', 'contentSelector', 'callback' );
		foreach ( $js as $field ) {

			if ( !isset( $options[$field] ) )
				continue;

			$options[$field] = addslashes( $options[ $field ] );

		}

		//force post-style kses on messages
		foreach ( array( 'finishedMsg', 'msgText' ) as $field ) {

			if ( !isset( $options['loading'][$field] ) )
				continue;

			$options['loading'][$field] = wp_filter_post_kses( $options['loading'][$field] );

		}
		
		return apply_filters( $this->parent->prefix . 'options_validate', $options );

	}


	/**
	 * Allows overloading to get option value
	 * Usage: $value = $object->{option name}
	 * @param string $name the option name
	 * @return mixed the option value
	 */
	function __get( $name ) {

		return $this->get_option( $name );

	}


	/**
	 * Allows overloading to set option value
	 * Usage: $object->{option name} = $value
	 * @param string $name unique option key
	 * @param mixed $value the value to store
	 * @return bool success/fail
	 */
	function __set( $name, $value ) {

		return $this->set_option( $name, $value );

	}


	/**
	 * Retreive the options array
	 * @return array the options
	 */
	function get_options( ) {

		if ( !$options = wp_cache_get( 'options', $this->parent->slug ) ) {
			$options = get_option( $this->parent->slug_ );
			wp_cache_set( 'options', $options, $this->parent->slug );
		}

		return apply_filters( $this->parent->prefix . 'options', $options );

	}


	/**
	 * If any options are not set, merge with defaults
	 * @param array $options the saved options
	 * @return array the merged options with defaults
	 */
	function default_options_filter( $options ) {

		$this->defaults[ 'db_version' ] = $this->parent->version;
		$options = wp_parse_args( $options, $this->defaults );
		wp_cache_set( 'options', $options, $this->parent->slug );
		return $options;

	}


	/**
	 * Retreives a specific option
	 * @param string $option the unique option key
	 * @return mixed the value
	 */
	function get_option( $option ) {
		$options = $this->get_options( );
		$value = ( isset( $options[ $option ] ) ) ? $options[ $option ] : false;
		return apply_filters( $this->parent->prefix . $option, $value );
	}


	/**
	 * Sets a specific option
	 * @return bool success/fail
	 * @param string $key the unique option key
	 * @param mixed $value the value
	 */
	function set_option( $key, $value ) {
		$options = array( $key => $value );
		$this->set_options( $options );
	}


	/**
	 * Sets all plugin options
	 * @param array $options the options array
	 * @param bool $merge (optional) whether or not to merge options arrays or overwrite
	 * @return bool success/fail
	 */
	function set_options( $options, $merge = true ) {

		if ( $merge ) {
			$defaults = $this->get_options();
			$options = wp_parse_args( $options, $defaults );
		}

		wp_cache_set( 'options', $options, $this->parent->slug );

		return update_option( $this->parent->slug_, $options );

	}


}