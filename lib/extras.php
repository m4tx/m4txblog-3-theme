<?php
/**
 * Clean up the_excerpt()
 */
function roots_excerpt_more($more) {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'roots') . '</a>';
}

add_filter('excerpt_more', 'roots_excerpt_more');

/**
 * Manage output of wp_title()
 */
function roots_wp_title($title) {
  if (is_feed()) {
    return $title;
  }

  $title .= get_bloginfo('name');

  return $title;
}

add_filter('wp_title', 'roots_wp_title', 10);

/**
 * "Intelligent" excerpt of a string. Cuts the string to the last space before the char limit,
 * or exactly to the char limit if there's only one word.
 *
 * @param $string string the string to excerpt
 * @param $count int maximum count of characters the result string can have
 * @return string excerpt of the $string
 */
function get_excerpt($string, $count) {
  $string = strip_tags($string);
  if (strlen($string) <= $count + 1) {
    return $string;
  }
  $string = substr($string, 0, $count);
  $pos = strripos($string, " ");
  if ($pos != false) {
    $string = substr($string, 0, $pos);
  }
  return $string . '&hellip;';
}

// Advanced pagination
function wp_corenavi() {
  global $wp_query, $wp_rewrite;
  $pages = '';
  $max = $wp_query->max_num_pages;
  if (!$current = get_query_var('paged')) {
    $current = 1;
  }
  $a['base'] = str_replace(999999999, '%#%', get_pagenum_link(999999999));
  $a['total'] = $max;
  $a['current'] = $current;

  $total = 0; //1 - display the text "Page N of N", 0 - not display
  $a['mid_size'] = 3; //how many links to show on the left and right of the current
  $a['end_size'] = 1; //how many links to show in the beginning and end
  $a['prev_text'] = '&laquo;'; //text of the "Previous page" link
  $a['next_text'] = '&raquo;'; //text of the "Next page" link
  $a['type'] = 'list';

  $output = paginate_links($a);
  $output = str_replace('<ul class=\'page-numbers\'>', '<ul class=\'pagination\'>', $output);
  $output = str_replace('<li><span class=\'page-numbers current\'>', '<li class=\'active\'><span>', $output);
  $output = str_replace('<li><a class=\'page-numbers\'', '<li><a', $output);
  $output = str_replace('<li><a class="next page-numbers"', '<li><a', $output);
  $output = str_replace('<li><span class="page-numbers dots">', '<li class="disabled"><span class="page-numbers dots">', $output);
  echo '<nav class="pagination-wrapper">' . $output . '</nav>';
}