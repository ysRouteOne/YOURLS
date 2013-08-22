<?php
// Functions that relate to HTTP stuff

// TODO: ponder: always include functions-http? always include Requests?

/*
$headers = array('Accept' => 'application/json');
$options = array('auth' => array('user', 'pass'));
$request = Requests::get('https://api.github.com/gists', $headers, $options);

var_dump($request->status_code);
// int(200)

var_dump($request->headers['content-type']);
// string(31) "application/json; charset=utf-8"

var_dump($request->body);
// string(26891) "[...]"
*/

/**
 * Check if Requests class is defined, include Requests library if need be
 *
 * All HTTP functions should perform that check prior to any operation. This is to avoid
 * include()-ing all the Requests files on every YOURLS instance disregarding whether needed or not.
 *
 * @since 1.7
 */
function yourls_http_load_library() {
	if ( !class_exists( 'Requests', false ) ) {
		require_once dirname(__FILE__) . '/Requests/Requests.php';
		Requests::register_autoloader();
	}
}


/**
 * Perform a GET request, return response array
 *
 * Wrapper for yourls_http_request()
 *
 * @since 1.7
 * @see yourls_http_request
 * @return array Response
 */
function yourls_http_get( $url, $headers = array(), $data = array(), $options = array() ) {
	return yourls_http_request( 'GET', $url, $headers, $data, $options );
}

function yourls_http_retrieve_body( $response ) {
	return $response->body;
}


/**
 * Perform a HTTP request, return response array
 *
 * Long desc, multiline
 *
 * @since 1.7
 * @param string $var Stuff
 * @return string Result
 */
function yourls_http_request( $type, $url, $headers, $data, $options ) {
	yourls_http_load_library();
	
	$options = array_merge( yourls_http_default_options(), $options );

	return Requests::request( $url, $headers, $data, $type, $options );
}

/**
 * Default HTTP requests options for YOURLS
 *
 * @since 1.7
 * @return array Options
 */
function yourls_http_default_options() {
	$options = array(
		'timeout' => '5',
		'useragent' => yourls_http_user_agent(),
		'follow_redirects' => true,
		'redirects' => 3,
		//'verify' => false,
		//'verifyname' => false,
	);

	return yourls_apply_filter( 'http_default_options', $options );	
}


/**
 * Deprecated. Get remote content via a GET request using best transport available
 * Returns $content (might be an error message) or false if no transport available
 *
 */
function yourls_get_remote_content( $url,  $maxlen = 4096, $timeout = 5 ) {
	yourls_deprecated_function( __FUNCTION__, '1.7', 'yourls_http_get' );
	$response = yourls_http_get( $url );
	return $response->body;
}


/**
 * Return funky user agent string
 *
 */
function yourls_http_user_agent() {
	return yourls_apply_filter( 'http_user_agent', 'YOURLS v'.YOURLS_VERSION.' +http://yourls.org/ (running on '.YOURLS_SITE.')' );
}
