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

function get_similarity($words) {
	$result = array();
	$count = 0;

	// compare each word in array
	foreach ( $words as $word_a ) {
		foreach ( $words as $word_b ) {
			if ( $word_a == $word_b ) {
				continue;
			}

			$result[ similar_percentage($word_a, $word_b) ] = "{$word_a}, {$word_b}";
		}
	}

	// sort desc comparations betwwen words
	krsort($result);

	// display the 2 words with the highest similarity rate
	return current($result);
}

echo get_similarity($orange_words);
