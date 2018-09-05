<?php

define('DS', DIRECTORY_SEPARATOR);
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('PROTOCOL', isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http');
define('DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
define('PORT', $_SERVER['SERVER_PORT']);

// helper to define and use new_path
function define_dir( $define = null, $new_path = null , $default_path = null ) {

	// if new directory exists define it
	if ( is_dir( $new_path ) ) {
		if ( ($realpath = realpath( $new_path )) !== FALSE ) {
			$new_path = fixslash( $realpath );
			if ( !defined( $define ) ) define( $define, trailingslash(realpath( $new_path )) );
		} else {
			$new_path = fixslash( $new_path );
			if ( !defined( $define ) ) define( $define, trailingslash(realpath( $new_path )) );
		}

	// if new directory not exists use default one
	} else if ( (is_dir( $default_path )) ) {
		if ( !defined( $define ) ) define( $define, trailingslash(realpath( $default_path )) );

	// exit if new_path and default_path not exists
	} else {
		$protocol = "HTTP/1.0";
		if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] ) $protocol = "HTTP/1.1";
		header( "$protocol 503 Service Unavailable.", true, 503 );
		print ("<p style=\"text-align:center;\">Incorrect path or directory name for <b>$default_path</b> !!!<br>Related  file to this problem<br><span style=\"color:#800\"><b>" . realpath(SELF) . "</b></span></p>\n");
		exit(3);
	}
}

// to fix trailing slash
function trailingslash( $url_path ) {
	if ( pathinfo($url_path, PATHINFO_EXTENSION) ) return fixslash( $url_path );
	else return fixslash( $url_path ) . '/';
}
// to fix slash
function fixslash( $url_path ) {
	$url_path = str_replace('\\', '/', $url_path);
	return rtrim(preg_replace('/([^:])(\/{2,})/', '$1/', $url_path), '/\\' );
}

function url( $url = null ) {
// convert system PATH to URL
	if (is_absolute_path($url)) {
		return trailingslash(PROTOCOL . '://' . DOMAIN . '/' . strip_root($url));
	} else {
		return trailingslash(PROTOCOL . '://' . DOMAIN . '/' . $url);
	}
}

function strip_root( $path = null ) {
	$path = fixslash($path);
	if (is_absolute_path($path)) {
		$docRoot = fixslash($_SERVER['DOCUMENT_ROOT']);
		return str_replace( $docRoot, '', $path );
	}
}

function is_absolute_path($path = null) {
	$path = fixslash($path);
	if ($path[0] === DIRECTORY_SEPARATOR || preg_match('~\A[A-Z]:(?![^/\\\\])~i',$path))
		return true;
	else
		return false;
}