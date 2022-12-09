<?php

function similar_percentage($first, $second) {
	$percentage = NULL;

	similar_text($first, $second, $percentage);

	return $percentage;
}

$orange_words = array(
	'orange',
	'naranja',
	'oranssi',
	'narancs',
	'arancione',
	'laranja',
	'turuncu',
);

// answer

function get_combinations($arr) {
	$out = array();
	$i = 0;

	foreach ( $arr as $v1 ) {
		foreach ( $arr as $v2 ) {
			if ( $v1 == $v2 ) {
				continue;
			}

			$out[ similar_percentage($v1, $v2) ] = "{$v1}, {$v2}";
		}
	}

	krsort($out);

	if ( !empty($out) ) {
		foreach ( $out as $v ) {
			if ( $i >= 2 ) {
				break;
			}

			$words[] = $v;

			$i++;
		}

		return $words;
	}
}

var_dump(
	get_combinations($orange_words)
);
