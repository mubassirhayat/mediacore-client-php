<?php

function urlsafe_b64encode($value) {
	/*
	Return a URL safe base64 encoded string (RFC 3458 Section 4) with all
	padding = characters stripped off to avoid potential problems with
	non-standard URL parsers that will choke on the = characters.
	*/
	$encoded_value = base64_encode($value);
	$encoded_value = strtr($encoded_value, '+/', '-_');
	$encoded_value = trim($encoded_value, '=');
	return $encoded_value;
}

function get_signature($secret_key, $value) {
	/*
	Generate a signature for the given key and value.

	Assumes the given secret key is a Unicode string containing a bse64 encoded
	version of the key.
	*/
	$secret_key = base64_decode($secret_key);
	$mac = hash_hmac("sha256", $value, $secret_key, true);
	return $mac;
}

function get_signed_qs($url, $query_string, $key_id, $secret_key, $ttl=null, $expiry_epoch=null, $ip_mask=null) {
	/*
	Create a signature for the given MediaCore URL.

	If both ``ttl`` and ``expiry_epoch`` are specified, ``expiry_epoch`` will
	be used. If neither is specified, the generated URL will be valid forever.

	:param str url: A fully qualified MediaCore URL. Must not include query
		parameters.

	:param str key_id: The AWS ID for the RSA key pair.

	:param str secret_key: The secret key string (a base64 encoded 1024-bit
		key).

	:param str query_string: '*' for wildcard, '' for empty, or 'a=b&c=d' to
		require a specific QS.

	:param ttl: The number of seconds from now that the URL will be valid.
	:type ttl: int or null

	:param expiry_epoch: The exact UTC unix epoch that the URL will expire.
	:type expiry_epoch: int or null

	:param ip_mask: An IP address mask restrict this URL to. e.g.:
		'192.168.0.0/24' would limit the IP to any IPs beginning with
		'192.168.0.'.
	:type ip_mask: str or null

	:returns: A query string to be appended to the URL. Does not include a
		leading & or ? character.
	*/
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
	$encoded_policy_string = urlsafe_b64encode($policy_string);

	$signature = get_signature($secret_key, $encoded_policy_string);
	$encoded_signature = urlsafe_b64encode($signature);

	$policy_qs = '_Policy=%s&_Signature=%s&_KeyId=%s';
	$new_qs = sprintf($policy_qs, $encoded_policy_string, $encoded_signature, $key_id);

	if (!in_array($query_string, array('', '*'))) {
		$new_qs = $query_string . '&' . $new_qs;
	}
	return $new_qs;
}

/* Example usage */
$secret_key = 'secret==';
$key_id = 'keyid';
$url = 'http://localhost:8080/media/pandamp4/embed_player';
$qs = 'iframe=True';
$one_hour = 60 * 60;
$localhost_only = '127.0.0.1/32';
$signed_qs = get_signed_qs($url, $qs, $key_id, $secret_key, $one_hour, null, $localhost_only);
$signed_url = $url . '?' . $signed_qs;
echo '<a href="' . $signed_url . '">This link contains the signed version of ' . $url . '</a>';
