<?php
/*
Plugin Name: Advanced Hooks API
Plugin URI: https://github.com/Rarst/advanced-hooks-api
Description: Set of (experimental) wrappers that allow to hook more elaborate events without coding intermediary functions.
Author: Andrey "Rarst" Savchenko
Author URI: http://www.rarst.net/
Version:
License Notes: MIT

Copyright (c) 2012 Andrey "Rarst" Savchenko

Permission is hereby granted, free of charge, to any person obtaining a copy of this
software and associated documentation files (the "Software"), to deal in the Software
without restriction, including without limitation the rights to use, copy, modify, merge,
publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons
to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies
or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
*/

if ( ! class_exists( 'R_Hook_Handler' ) ) {

	require dirname( __FILE__ ) . '/class-r-hook-handler.php';

	/**
	 * @param string   $tag
	 * @param callback $callback
	 * @param int      $priority
	 * @param null     $args one or more arguments to pass to hooked function.
	 *
	 * @return bool
	 */
	function add_action_with_args( $tag, $callback, $priority = 10, $args = null ) {

		$args = array_slice( func_get_args(), 3 );

		return add_action( $tag, array( new R_Hook_Handler( $args, $callback ), 'action' ), $priority, 1 );
	}

	/**
	 * @param string       $tag
	 * @param callback     $callback
	 * @param int          $priority
	 * @param null         $args
	 */
	function remove_action_with_args( $tag, $callback, $priority = 10, $args = null ) {

		$args = array_slice( func_get_args(), 3 );

		R_Hook_Handler::remove_action( $tag, $priority, $args, 'action', $callback );
	}

	/**
	 * @param string $tag
	 * @param mixed  $return value to override filter with
	 * @param int    $priority
	 *
	 * @return bool
	 */
	function add_filter_return( $tag, $return, $priority = 10 ) {

		return add_filter( $tag, array( new R_Hook_Handler( $return ), 'filter' ), $priority, 0 );
	}

	/**
	 * @param string $tag
	 * @param mixed  $return
	 * @param int    $priority
	 */
	function remove_filter_return( $tag, $return, $priority = 10 ) {

		R_Hook_Handler::remove_action( $tag, $priority, $return, 'filter' );
	}

	/**
	 * @param string $tag
	 * @param mixed  $prepend value to concatenate at start of filtered string or unshift to start of filtered array
	 * @param int    $priority
	 *
	 * @return bool
	 */
	function add_filter_prepend( $tag, $prepend, $priority = 10 ) {

		return add_filter( $tag, array( new R_Hook_Handler( $prepend ), 'prepend' ), $priority, 1 );
	}

	/**
	 * @param string $tag
	 * @param mixed  $prepend
	 * @param int    $priority
	 */
	function remove_filter_prepend( $tag, $prepend, $priority = 10 ) {

		R_Hook_Handler::remove_action( $tag, $priority, $prepend, 'prepend' );
	}

	/**
	 * @param string $tag
	 * @param mixed  $append value to concatenate at end of filtered string or append to end of filtered array
	 * @param int    $priority
	 *
	 * @return bool
	 */
	function add_filter_append( $tag, $append, $priority = 10 ) {

		return add_filter( $tag, array( new R_Hook_Handler( $append ), 'append' ), $priority, 1 );
	}

	/**
	 * @param string $tag
	 * @param mixed  $append
	 * @param int    $priority
	 */
	function remove_filter_append( $tag, $append, $priority = 10 ) {

		R_Hook_Handler::remove_action( $tag, $priority, $append, 'append' );
	}

	/**
	 * @param string $tag
	 * @param string $search  substring to search or array key
	 * @param string $replace string to replace substring or array value with
	 * @param int    $priority
	 *
	 * @return bool
	 */
	function add_filter_replace( $tag, $search, $replace, $priority = 10 ) {

		return add_filter( $tag, array( new R_Hook_Handler( compact( 'search', 'replace' ) ), 'replace' ), $priority );
	}

	/**
	 * @param string $tag
	 * @param string $search
	 * @param string $replace
	 * @param int    $priority
	 */
	function remove_filter_replace( $tag, $search, $replace, $priority = 10 ) {

		R_Hook_Handler::remove_action( $tag, $priority, compact( 'search', 'replace' ), 'replace' );
	}

	/**
	 * Add filter to only run once.
	 *
	 * @param string   $tag
	 * @param callback $callback
	 * @param int      $priority
	 * @param int      $accepted_args
	 *
	 * @return bool
	 */
	function add_filter_once( $tag, $callback, $priority = 10, $accepted_args = 1 ) {

		return add_action( $tag, array( new R_Hook_Handler( compact( 'priority', 'accepted_args' ), $callback ), 'once' ), $priority, $accepted_args );
	}

	/**
	 * Remove filter that runs once.
	 *
	 * @param string   $tag
	 * @param callback $callback
	 * @param int      $priority
	 * @param int      $accepted_args
	 */
	function remove_filter_once( $tag, $callback, $priority = 10, $accepted_args = 1 ) {

		R_Hook_Handler::remove_action( $tag, $priority, compact( 'priority', 'accepted_args' ), 'once', $callback );
	}

	/**
	 * Add action that only runs once.
	 *
	 * @param string   $tag
	 * @param callback $callback
	 * @param int      $priority
	 * @param int      $accepted_args
	 *
	 * @return bool
	 */
	function add_action_once( $tag, $callback, $priority = 10, $accepted_args = 1 ) {

		return add_filter_once( $tag, $callback, $priority, $accepted_args );
	}

	/**
	 * Remove action that runs once.
	 *
	 * @param string   $tag
	 * @param callback $callback
	 * @param int      $priority
	 * @param int      $accepted_args
	 */
	function remove_action_once( $tag, $callback, $priority = 10, $accepted_args = 1 ) {

		remove_filter_once( $tag, $callback, $priority, $accepted_args );
	}

	/**
	 * @param array|string       $tags  hook and method name or array of names
	 * @param int                $priority
	 * @param int                $accepted_args
	 * @param bool|string|object $class false for auto, class name or object
	 *
	 * @return bool|void
	 */
	function add_method( $tags, $priority = 10, $accepted_args = 1, $class = false ) {

		if ( empty( $class ) ) {

			list( , $caller) = debug_backtrace();

			if ( empty( $caller['class'] ) )
				return false;

			$class = ( '->' == $caller['type'] ) ? $caller['object'] : $caller['class'];
		}

		if ( ! is_array( $tags ) )
			$tags = array( $tags );

		foreach ( $tags as $tag ) {

			if ( method_exists( $class, $tag ) )
				add_action( $tag, array( $class, $tag ), $priority, $accepted_args );
		}

		return true;
	}
}