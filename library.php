<?php

/**
 * Return a URL safe base64 encoded string (RFC 3458 Section 4) with all
 * padding = characters stripped off to avoid potential problems with
 * non-standard URL parsers that will choke on the = characters.
 */
function mcore_urlsafe_b64encode($value) {
	$encoded_value = base64_encode($value);
	$encoded_value = strtr($encoded_value, '+/', '-_');
	$encoded_value = trim($encoded_value, '=');
	return $encoded_value;
}

/**
 * Generate a signature for the given key and value.
 *
 * Assumes the given secret key is a Unicode string containing a bse64 encoded
 * version of the key.
 */
function mcore_get_signature($secret_key, $value) {
	$secret_key = base64_decode($secret_key);
	$mac = hash_hmac("sha256", $value, $secret_key, true);
	return $mac;
}

/**
 * Create a signature for the given MediaCore URL.
 *
 * If both ``ttl`` and ``expiry_epoch`` are specified, ``expiry_epoch`` will
 * be used. If neither is specified, the generated URL will be valid forever.
 *
 * Example usage:
 *
 *     $secret_key = 'secret==';
 *     $key_id = 'keyid';
 *     $url = 'http://localhost:8080/media/pandamp4/embed_player';
 *     $qs = 'iframe=True';
 *     $one_hour = 60 * 60;
 *     $localhost_only = '127.0.0.1/32';
 *     $signed_qs = get_signed_qs($url, $qs, $key_id, $secret_key, $one_hour, null, $localhost_only);
 *     $signed_url = $url . '?' . $signed_qs;
 *     echo '<a href="' . $signed_url . '">This link contains the signed version of ' . $url . '</a>';
 *
 * @param string $url A fully qualified MediaCore URL. Must not include query
 *     parameters.
 *
 * @param string $key_id The AWS ID for the RSA key pair.
 *
 * @param string $secret_key The secret key string (a base64 encoded 1024-bit
 *     key).
 *
 * @param string $query_string '*' for wildcard, '' for empty, or 'a=b&c=d' to
 *     require a specific QS.
 *
 * @param int|null $ttl The number of seconds from now that the URL will be valid.
 *
 * @param int|null $expiry_epoch The exact UTC unix epoch that the URL will expire.
 *
 * @param string|null $ip_mask An IP address mask restrict this URL to. e.g.:
 *     '192.168.0.0/24' would limit the IP to any IPs beginning with
 *     '192.168.0.'.
 *
 * @return string A query string to be appended to the URL. Does not include a
 *     leading & or ? character.
 */
function mcore_get_signed_qs($url, $query_string, $key_id, $secret_key, $ttl=null, $expiry_epoch=null, $ip_mask=null) {
	if ($expiry_epoch === null and $ttl !== null) {
		$expiry_epoch = time() + $ttl;
	}

	$policy = array();
	$policy['resource'] = $url;
	if ($query_string != '') {
		$policy['query_string'] = $query_string;
	}
	if ($ip_mask !== null) {
		$policy['ip_range'] = $ip_mask;
	}
	if ($expiry_epoch !== null) {
		$policy['expiry_epoch'] = $expiry_epoch;
	}

	$policy_string = json_encode($policy);
	$encoded_policy_string = mcore_urlsafe_b64encode($policy_string);

	$signature = mcore_get_signature($secret_key, $encoded_policy_string);
	$encoded_signature = mcore_urlsafe_b64encode($signature);

	$policy_qs = '_Policy=%s&_Signature=%s&_KeyId=%s';
	$new_qs = sprintf($policy_qs, $encoded_policy_string, $encoded_signature, $key_id);

	if (!in_array($query_string, array('', '*'))) {
		$new_qs = $query_string . '&' . $new_qs;
	}
	return $new_qs;
}

/**
 * Make a HTTP request to the MediaCore API and return the results.
 *
 * This will handle Basic Auth if you provide your credentials, but be aware
 * MediaCore only allows Basic Auth over HTTPS!
 *
 * If you must use HTTP because HTTPS is not supported in your environment,
 * you should provide your keys so that signed URLs can be generated for you.
 *
 * @param string $method Request method (GET, POST, PUT, DELETE).
 * @param string $url Absolute URL for the API to call.
 * @param array|null $params Request parameters to include, if any.
 * @param array|null $credentials Your username and password for basic auth.
 *     Example: array('username' => 'me', 'password' => 'pw')
 * @param array|null $keys Your keys, for generating a signed URL.
 *     Example: array('key_id' => 'Abc123', 'secret_key' => 'Def456')
 * @param int|null $ttl The number of seconds from now that the URL will be valid.
 *     We've chosen 30 seconds by default to avoid time sync issues.
 * @param int|null $expiry_epoch The exact UTC unix epoch that the URL will expire.
 * @return {Requests_Response} Response object.
 */
function mcore_call_api($method, $url, $params, $credentials=null, $keys=null, $ttl=30, $expiry_epoch=null) {
	// Lazy-load Requests
	if (!class_exists('Requests', false)) {
		require_once dirname(__FILE__) . '/deps/Requests.php';
		Requests::register_autoloader();
	}

	$headers = array();
	$options = array(
		'useragent' => 'MediaCore PHP Client/1.0',
	);

	if ($credentials && !empty($credentials['username'])) {
		$options['auth'] = array($credentials['username'], $credentials['password']);
	} else if ($keys && !empty($credentials['key_id'])) {
		// Separate out the query string if there is one embedded in the URL.
		$query_string_start = strpos($url, '?');
		if ($query_string_start >= 0) {
			$query_string = substr($query_string, $query_string_start + 1);
			$url = substr($url, 0, $query_string_start);
		} else {
			$query_string = '';
		}

		// Manually add the params to query string for GET requests.
		// Normally Requests does this for us, but we need to sign these params too.
		if ($method == 'GET' && !empty($params)) {
			$query_string = ($query_string ? '&' : '?')
			              . http_build_query($params, null, '&');
			$params = null;
		}

		$key_id = $keys['key_id'];
		$secret_key = $keys['secret_key'];
		$query_string = mcore_get_signed_qs($url, $query_string, $key_id, $secret_key, $ttl, $expiry_epoch);
		$url = $url . '?' . $query_string;
	}

	$response = Requests::request($url, $headers, $params, $method, $options);

	if ($response->status_code >= 500) {
		throw new Exception('API error occurred ' . $response->status_int);
	}

	return $response;
}

/**
 * Make a HTTP request to the MediaCore API and return decoded JSON result.
 *
 * This will handle Basic Auth if you provide your credentials, but be aware
 * MediaCore only allows Basic Auth over HTTPS!
 *
 * If you must use HTTP because HTTPS is not supported in your environment,
 * you should provide your keys so that signed URLs can be generated for you.
 *
 * @param string $method Request method (GET, POST, PUT, DELETE).
 * @param string $url Absolute URL for the API to call.
 * @param array|null $params Request parameters to include, if any.
 * @param array|null $credentials Your username and password for basic auth.
 *     Example: array('username' => 'me', 'password' => 'pw')
 * @param array|null $keys Your keys, for generating a signed URL.
 *     Example: array('key_id' => 'Abc123', 'secret_key' => 'Def456')
 * @param number $ttl How long the request should be valid for. We've
 *     chosen 30 seconds to avoid time sync issues.
 */
function mcore_call_json_api($method, $url, $params, $credentials=null, $keys=null, $ttl=30) {
	$response = mcore_call_api($method, $url, $params, $credentials, $keys, $ttl);
	return json_decode($response->body);
}
