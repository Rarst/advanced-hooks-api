<?php

class R_Hook_Handler {

	private $data;

	/**
	 * @var callback
	 */
	private $callback;

	function __construct( $data, $callback = null ) {

		$this->data     = $data;
		$this->callback = $callback;
	}

	function action() {

		call_user_func_array( $this->callback, $this->data );

		if ( func_num_args() )
			return func_get_arg( 0 );

		return null;
	}

	function filter() {

		return $this->data;
	}

	function replace( $input ) {

		if ( is_array( $input ) )
			$input[$this->data['search']] = $this->data['replace'];
		else
			$input = str_replace( $this->data['search'], $this->data['replace'], $input );

		return $input;
	}

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
}