<?php

/**
 * Custom Categories widget class that shows list of categories inside ul with Bootstrap's
 * nav-pills class. m4txblog³ categories widget also outputs cleaner HTML output than the
 * WordPress's default Category widget.
 */
class M4txblog_Widget_Categories extends WP_Widget {
  function __construct() {
    $widget_ops = array('classname' => 'widget_m4txblog_categories',
        'description' => __("Bootstrapped list of categories."));
    parent::__construct('m4txblog_categories', __('m4txblog³ Categories'), $widget_ops);
  }

  function widget($args, $instance) {
    extract($args);

    $title = apply_filters('widget_title',
        empty($instance['title']) ? __('Categories') : $instance['title'],
        $instance, $this->id_base);

    echo $before_widget;
    if ($title) {
      echo $before_title . $title . $after_title;
    }

    $cat_args = array('orderby' => 'name', 'show_count' => 1, 'hierarchical' => 0);

    ?>
    <ul class="nav nav-pills nav-stacked">
      <?php
      $cat_args['echo'] = false;
      $cat_args['title_li'] = '';
      $cats = wp_list_categories($cat_args);

      echo preg_replace_callback(
          '~<li class="cat-item cat-item-\d+"><a href="(.+)" title="(.+)">(.+)</a> \((\d+)\)~',
          function ($matches) {
            return '<li><a href="' . $matches[1] . '" aria-label="' . $matches[2] .
            '" data-toggle="tooltip" data-placement="left" title="' .
            sprintf(_n('%d post', '%d posts', $matches[4], 'roots'),
                $matches[4]) . '">' . $matches[3] . '</a>';
          }, $cats);
      ?>
    </ul>
    <?php

    echo $after_widget;
  }

  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    return $instance;
  }

  function form($instance) {
    // Defaults
    $instance = wp_parse_args((array)$instance, array('title' => ''));
    $title = esc_attr($instance['title']);
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
             name="<?php echo $this->get_field_name('title'); ?>" type="text"
             value="<?php echo $title; ?>"/>
    </p>
  <?php
  }

}

/**
 * Custom Tag Cloud widget class that provides Bootstrap tooltips showing the number of the posts
 * inside each tag.
 */
class M4txblog_Widget_Tag_Cloud extends WP_Widget {
  function __construct() {
    $widget_ops = array('description' => __("Tag Cloud with Bootstrap tooltips"));
    parent::__construct('m4txblog_tag_cloud', __('m4txblog³ Tag Cloud'), $widget_ops);
  }

  static function tagcloud_filter($output) {
    $output = str_replace('title=\'', 'data-toggle=\'tooltip\' title=\'', $output);
    return $output;
  }

  function widget($args, $instance) {
    extract($args);
    if (!empty($instance['title'])) {
      $title = $instance['title'];
    } else {
      $title = __('Tags');
    }

    /** This filter is documented in wp-includes/default-widgets.php */
    $title = apply_filters('widget_title', $title, $instance, $this->id_base);

    echo $before_widget;
    if ($title) {
      echo $before_title . $title . $after_title;
    }
    echo '<div class="tagcloud">';

    add_filter('wp_tag_cloud', array('M4txblog_Widget_Tag_Cloud', 'tagcloud_filter'));
    wp_tag_cloud(apply_filters('widget_tag_cloud_args', array(
        'number' => 20,
        'taxonomy' => 'post_tag'
    )));

    echo "</div>\n";
    echo $after_widget;
  }

  function update($new_instance, $old_instance) {
    $instance['title'] = strip_tags(stripslashes($new_instance['title']));
    return $instance;
  }

  function form($instance) {
    ?>
    <p>
      <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
      <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
             name="<?php echo $this->get_field_name('title'); ?>"
             value="<?php if (isset ($instance['title'])) {
               echo esc_attr($instance['title']);
             } ?>"/>
    </p>
  <?php
  }
}

if (!function_exists('m4txblog_register_widgets')) {
  function m4txblog_register_widgets() {
    register_widget('M4txblog_Widget_Categories');
    register_widget('M4txblog_Widget_Tag_Cloud');
  }

  add_action('widgets_init', 'm4txblog_register_widgets', 1);
}