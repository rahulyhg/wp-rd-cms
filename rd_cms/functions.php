<?php
function d($var){
	print '<pre>'; var_dump($var); exit;
}

/**
 * Prints value of meta data field $f for current post.
 * 
 * @param $f String, name of field.
 * 
 * @return string Value of field.
 */
function rd_pmd($f, $id = null){
	print rd_gmd($f, $id);
}

/**
 * Gets value of meta data field $f for current post.
 * 
 * @param $f String, name of field.
 * 
 * @return string Value of field.
 */
function rd_gmd($f, $id = null){
	if ($id === null) {
		$id = get_the_ID();
	}
	return get_post_meta($id, $f, true);
}

/**
 * Same as pmd(), but checks current lang.
 * 
 * @param $f String, name of field.
 * 
 * @return string Value of field.
 */
function rd_pmdi($f, $id = null){
	print rd_gmdi($f, $id);
}


/**
 * Same as gmd(), but checks current lang.
 * 
 * @param $f String, name of field.
 * 
 * @return string Value of field.
 */
function rd_gmdi($f, $id = null){
	return rd_gmd($f . '_' . rd_get_current_lang(), $id);
}

function rd_pagination_link($new_offset){
	$url = $_SERVER['PHP_SELF'];
	$_GET['offset'] = $new_offset;
	$frags = array();
	foreach ($_GET as $key => $value) {
		$frags[] = $key . '=' . $value;
	}
	$url .= '?' . join($frags, '&');
	return $url;
}