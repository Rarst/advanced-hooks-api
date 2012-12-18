<?php

/**
 * Handler class for events in hooks.
 */
class R_Hook_Handler {

	private $data;

	/**
	 * @var callback
	 */
	private $callback;

	/**
	 * @param mixed         $data
	 * @param null|callback $callback
	 */
	function __construct( $data, $callback = null ) {

		$this->data     = $data;
		$this->callback = $callback;
	}

	/**
	 * Executes callback with custom arguments, ignores what hook passes.
	 *
	 * @return mixed|null
	 */
	function action() {

		call_user_func_array( $this->callback, $this->data );

		// compatibility with filters
		if ( func_num_args() )
			return func_get_arg( 0 );

		return null;
	}

	/**
	 * Returns custom data to filter.
	 *
	 * @return mixed
	 */
	function filter() {

		return $this->data;
	}

	/**
	 * Searches and replaces data as array or substring, depending on passed filter value.
	 *
	 * @param mixed $input
	 *
	 * @return array|mixed
	 */
	function replace( $input ) {

		if ( is_array( $input ) )
			$input[$this->data['search']] = $this->data['replace'];
		else
			$input = str_replace( $this->data['search'], $this->data['replace'], $input );

		return $input;
	}

	/**
	 * Prepends data to array or string, passed by filter.
	 *
	 * @param mixed $input
	 *
	 * @return array|string
	 */
	function prepend( $input ) {

		if ( is_array( $input ) ) {

			if ( is_array( $this->data ) ) {

				foreach ( array_reverse( $this->data ) as $value ) {

					array_unshift( $input, $value );
				}
			}
			else {

				array_unshift( $input, $this->data );
			}
		}
		else {

			$input = $this->data . $input;
		}

		return $input;
	}


	/**
	 * Appends data to array or string, passed by filter.
	 *
	 * @param mixed $input
	 *
	 * @return array|string
	 */
	function append( $input ) {

		if ( is_array( $input ) ) {

			if ( is_array( $this->data ) ) {

				foreach ( $this->data as $value ) {

					$input[] = $value;
				}
			}
			else {

				$input[] = $this->data;
			}
		}
		else {

			$input .= $this->data;
		}

		return $input;
	}

	/**
	 * Executes callback once and removes it from hook.
	 *
	 * @return mixed
	 */
	function once() {

		remove_filter( current_filter(), array( $this, __FUNCTION__ ), $this->data['priority'] );

		return call_user_func_array( $this->callback, array_slice( func_get_args(), 0, $this->data['accepted_args'] ) );
	}

	/**
	 * Remove matching handlers from specified hook and priority.
	 *
	 * @param string      $tag
	 * @param int         $priority
	 * @param mixed       $data
	 * @param string      $method of handler class
	 * @param string|null $callback
	 */
	static function remove_action( $tag, $priority, $data, $method, $callback = null ) {
		
		global $wp_filter;
		
		foreach ( $wp_filter[$tag][$priority] as $event ) {

			$function = $event['function'];

			/**
			 * @var R_Hook_Handler $object
			 */
			if ( is_array( $function ) && is_a( $object = $function[0], __CLASS__ ) )
					$object->remove_if_match( $tag, $priority, $event['accepted_args'], $data, $method, $callback );
		}
	}

	/**
	 * Unhook this instance from hook if data matches.
	 *
	 * @param string      $tag
	 * @param int         $priority
	 * @param int         $accepted_args
	 * @param mixed       $data
	 * @param string      $method of handler class
	 * @param string|null $callback
	 */
	function remove_if_match( $tag, $priority, $accepted_args, $data, $method, $callback = null ) {

		if ( $data === $this->data && $callback === $this->callback )
			remove_action( $tag, array( $this, $method ), $priority, $accepted_args );
	}
}