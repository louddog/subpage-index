<?php

if (!function_exists('generate_excerpt')) {
	function generate_excerpt($post_id = false) {
		if ($post_id) $post = is_numeric($post_id) ? get_post($post_id) : $post_id;
		else $post = $GLOBALS['post'];

		if (!$post) return '';
		if (isset($post->post_excerpt) && !empty($post->post_excerpt)) return $post->post_excerpt;
		if (!isset($post->post_content)) return '';
	
		$content = $raw_content = $post->post_content;
	
		if (!empty($content)) {
			$content = strip_shortcodes($content);
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = strip_tags($content);

			$excerpt_length = apply_filters('excerpt_length', 55);
			$words = preg_split("/[\n\r\t ]+/", $content, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
			if (count($words) > $excerpt_length) {
				array_pop($words);
				$content = implode(' ', $words);
				$content .= "...";
			} else $content = implode(' ', $words);
		}
	
		return apply_filters('wp_trim_excerpt', $content, $raw_content);
	}
}

if (!function_exists('csv_to_array')) {
	function csv_to_array($csv, $separator = ',') {
		if (!$csv || !is_string($csv)) return $csv;
		
		$array = array();
		foreach (explode($separator, $csv) as $item) {
			$array[] = trim($item);
		}
		
		return $array;
	}
}