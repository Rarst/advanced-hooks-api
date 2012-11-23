<?php
/*
Plugin Name: Advanced Hooks API
Plugin URI: http://wordpress.org/extend/plugins/advanced-hooks-api/
Description: Set of (experimental) wrappers that allow to hook more elaborate events without coding intermediary functions.
Author: Andrey "Rarst" Savchenko
Author URI: http://www.rarst.net/
Version: 0.5
License Notes: GPLv2 or later
*/

if( !class_exists('R_Hook_Handler') ) {

	/**
	 * @param string $tag
	 * @param callback $callback
	 * @param int $priority
	 * @param null $args one or more arguments to pass to hooked function.
	 *
	 * @return bool
	 */
	function add_action_with_args($tag, $callback, $priority = 10, $args = null) {

		$args = array_slice(func_get_args(), 3);
		
		return add_action($tag, array(new R_Hook_Handler($args, $callback), 'action'), $priority, 1);
	}

	/**
	 * @param string $tag
	 * @param int $priority
	 * @param mixed $return value to override filter with
	 *
	 * @return bool
	 */
	function add_filter_return($tag, $priority = 10, $return = false ) {

		return add_filter($tag, array(new R_Hook_Handler($return), 'filter'), $priority, 0);
	}

	/**
	 * @param string $tag
	 * @param int $priority
	 * @param mixed $prepend value to concatenate at start of filtered string or unshift to start of filtered array
	 *
	 * @return bool
	 */
	function add_filter_prepend( $tag, $priority = 10, $prepend = false ) {

		return add_filter($tag, array(new R_Hook_Handler($prepend), 'prepend'), $priority, 1);
	}

	/**
	 * @param string $tag
	 * @param int $priority
	 * @param mixed $append value to concatenate at end of filtered string or append to end of filtered array
	 *
	 * @return bool
	 */
	function add_filter_append( $tag, $priority = 10, $append = false ) {

		return add_filter($tag, array(new R_Hook_Handler($append), 'append'), $priority, 1);
	}

	/**
	 * @param string $tag
	 * @param string $search substring to search or array key
	 * @param string $replace string to replace substring or array value with
	 * @param int $priority
	 *
	 * @return bool
	 */
	function add_filter_replace( $tag, $search, $replace, $priority = 10 ) {

		return add_filter( $tag, array(new R_Hook_Handler( compact('search','replace') ), 'replace'), $priority );
	};

	/**
	 * @param array|string $tags hook and method name or array of names
	 * @param int $priority
	 * @param int $accepted_args
	 * @param bool|string|object $class false for auto, class name or object
	 *
	 * @return bool|void
	 */
	function add_method( $tags, $priority = 10, $accepted_args = 1, $class = false ) {

		if( empty($class) ) {

			list( , $caller) = debug_backtrace();

			if( empty($caller['class']) )
				return false;

			$class = ( '->' == $caller['type'] ) ? $caller['object'] : $caller['class'];
		}

		if( !is_array($tags) )
			$tags = array($tags);

		foreach( $tags as $tag )
			if( method_exists($class, $tag) )
				add_action($tag, array($class, $tag), $priority, $accepted_args);

		return true;
	}

	class R_Hook_Handler {

		private $data;

		/**
		 * @var callback
		 */
		private $callback;

		function __construct($data, $callback = null) {

			$this->data = $data;
			$this->callback = $callback;
		}

		function action() {

			call_user_func_array($this->callback, $this->data);

			if( func_num_args() )
				return func_get_arg(0);

			return null;
		}

		function filter() {

			return $this->data;
		}

		function replace( $input ) {

			if( is_array($input) )
				$input[$this->data['search']] = $this->data['replace'];
			else
				$input = str_replace($this->data['search'], $this->data['replace'], $input);

			return $input;
		}

		function prepend( $input ) {

			if (is_array($input))
				if (is_array($this->data))
					foreach (array_reverse($this->data) as $value)
						array_unshift($input, $value);
				else
					array_unshift($input, $this->data);
			else
				$input = $this->data . $input;

			return $input;
		}

		function append( $input ) {

			if (is_array($input))
				if (is_array($this->data))
					foreach ($this->data as $value)
						$input[] = $value;
				else
					$input[] = $this->data;
			else
				$input .= $this->data;

			return $input;
		}
	}
}