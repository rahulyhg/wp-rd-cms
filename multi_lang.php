<?php
define('DEFAULT_LANG', 'en');

function rd_get_langs(){
	return array(
		'en',
		'nl',
	);
}

function rd_get_langs_except_current(){
	return array_diff(rd_get_langs(), array(rd_get_current_lang()));
}

function rd_get_lang_links(){
	$langs = rd_get_langs();
	$links = array();
	foreach ($langs as $lang) {
		$links[] = rd_get_lang_link($lang);
	}
	return $links;
}

function rd_get_lang_link($lang){
	$url = $_SERVER['REQUEST_URI'];
	if (!empty($_GET)) {
		$urlHere = str_replace('lang=' . rd_get_current_lang(), 'lang=' . $lang, $url);
	} else {
		$char = (strpos($url, '?') === false) ? '?' : '&';
		$urlHere = $url . $char . 'lang=' . $lang;
	}
	$class = '';
	if ($lang == rd_get_current_lang()) {
		$class = ' class="selected" ';
	}
	return '<a href="' . $urlHere . '" ' . $class . '>' . $lang . '</a>';
}

function rd_get_current_lang(){
	if (!empty($_GET['lang'])) {
		return $_GET['lang'];
	} else {
		return DEFAULT_LANG;
	}
}

// remember store language selection in links
function rd_multi_lang_post_type_link( $post_link, $id = 0, $leavename = FALSE ) {
	if (isset($_GET['lang'])) {
		$lang = $_GET['lang'];
	} else {
		$lang = DEFAULT_LANG;
	}
	return $post_link . '&lang=' . urlencode($lang);
}
add_filter('post_type_link', 'rd_multi_lang_post_type_link', 1, 3);
add_filter('page_link', 'rd_multi_lang_post_type_link', 1, 3);
add_filter('post_type_archive_link', 'rd_multi_lang_post_type_link', 1, 3);

