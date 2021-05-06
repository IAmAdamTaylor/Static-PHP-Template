<?php

/**
 * Class wrapper for adding meta information to page.
 */
class Page_Meta {

	/**
	 * The meta data store.
	 * Stores meta information by key.
	 * @var array
	 */
	private $_store = array();
	
	public function __construct() {
		
	}

	/**
	 * Get a property from the store.
	 * $meta->property
	 * @param  string $name The property name to get.
	 * @return mixed       
	 */
	public function __get( $name ) {
		if ( isset( $this->_store[ $name ] ) ) {
			return $this->_store[ $name ];
		} else {
			return null;
		}
	}

	/**
	 * Store a value against a string key.
	 * @param string $name  The property name.
	 * @param mixed  $value The property value
	 */
	public function __set( $name, $value ) {
		$this->_store[ $name ] = $value;
	}

	/**
	 * Check if a property exists in the store.
	 * @param  string  $name The property name.
	 * @return boolean
	 */
	public function __isset( $name ) {
		return isset( $this->_store[ $name ] );
	}

	/**
	 * Remove a property from the store completely.
	 * @param  string  $name The property name.
	 */
	public function __unset( $name ) {
		unset( $this->_store[ $name ] );
	}
}
