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
    $widget_ops = array('description' => __("Tag Cloud with Bootstrap tooltips."));
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

/**
 * Custom Nav Menu widget class that adds nav-pills CSS class to the list of items
 */
class M4txblog_Nav_Menu_Widget extends WP_Nav_Menu_Widget {
  function __construct() {
    $widget_ops = array('description' => __('Add a custom Bootstrapped menu to your sidebar.'));
    Wp_Widget::__construct('m4txblog_nav_menu', __('m4txblog³ Nav Menu'), $widget_ops);
  }

  function widget($args, $instance) {
    // Get menu
    $nav_menu = !empty($instance['nav_menu']) ? wp_get_nav_menu_object($instance['nav_menu']) : false;

    if (!$nav_menu) {
      return;
    }

    $instance['title'] = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

    echo $args['before_widget'];

    if (!empty($instance['title'])) {
      echo $args['before_title'] . $instance['title'] . $args['after_title'];
    }

    wp_nav_menu(array('fallback_cb' => '', 'menu' => $nav_menu, 'menu_class' => 'menu nav nav-pills nav-stacked'));

    echo $args['after_widget'];
  }
}

/**
 * Custom Recent Comments widget class
 */
class M4txblog_Widget_Recent_Comments extends WP_Widget_Recent_Comments {

  function __construct() {
    $widget_ops = array('classname' => 'm4txblog_widget_recent_comments', 'description' =>
        __('Recent Comments widget that\'s looking awesome!'));
    Wp_Widget::__construct('m4txblog-recent-comments', __('m4txblog³ Recent Comments'),
        $widget_ops);
    $this->alt_option_name = 'm4txblog_widget_recent_comments';


    add_action('comment_post', array($this, 'flush_widget_cache'));
    add_action('transition_comment_status', array($this, 'flush_widget_cache'));
  }

  protected function widget_get_author_avatar($comment) {
    // Construct img

    // 4x bigger image (64x64 instead of 32x32) for HiDPI support
    $img = str_replace('class=\'avatar', 'class=\'avatar img-thumbnail', get_avatar($comment, 64));

    $url = $comment->comment_author_url;
    if (empty($url) || 'http://' == $url) {
      return $img;
    } else {
      return '<a href="' . $url . '">' . $img . '</a>';
    }
  }

  private function widget_get_shortened_link($url, $text, $count) {
    $output = "";

    $excerpt = get_excerpt($text, $count);
    $output .= empty($url) ? '<span' : '<a href="' . esc_url($url) . '"';

    if ($excerpt != $text) {
      $output .= ' title="' . $text . '"';
    }
    $output .= '>' . $excerpt;

    $output .= empty($url) ? '</span> ' : '</a>';
    return $output;
  }

  function widget($args, $instance) {
    global $comments, $comment;

    $cache = wp_cache_get('widget_recent_comments', 'widget');

    if (!is_array($cache)) {
      $cache = array();
    }

    if (!isset($args['widget_id'])) {
      $args['widget_id'] = $this->id;
    }

    if (isset($cache[$args['widget_id']])) {
      echo $cache[$args['widget_id']];
      return;
    }

    extract($args, EXTR_SKIP);
    $output = '';
    $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Comments') : $instance['title'], $instance, $this->id_base);

    if (empty($instance['number']) || !$number = absint($instance['number'])) {
      $number = 5;
    }

    $comments = get_comments(apply_filters('widget_comments_args', array('number' => $number, 'status' => 'approve', 'post_status' => 'publish')));
    $output .= $before_widget;
    if ($title) {
      $output .= $before_title . $title . $after_title;
    }

    $output .= '<ul id="recentcomments" class="list-unstyled">';
    if ($comments) {
      // Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
      $post_ids = array_unique(wp_list_pluck($comments, 'comment_post_ID'));
      _prime_post_caches($post_ids, strpos(get_option('permalink_structure'), '%category%'), false);

      foreach ((array)$comments as $comment) {
        //$output .=  '<li class="recentcomments">' . /* translators: comments widget: 1: comment author, 2: post link */ sprintf(_x('%1$s on %2$s', 'widgets'), get_comment_author_link(), '<a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
        $output .= '<li class="recentcomments">';
        $output .= $this->widget_get_author_avatar($comment);
        $output .= '<div class="commentbox">
                            <div class="boxcontent">
                                <p>' . get_excerpt($comment->comment_content, 90) . '</p>
                            </div>
                            <small class="userinfo">
                                ' . sprintf(_x('%1$s on %2$s', 'widgets'), $this->widget_get_shortened_link($comment->comment_author_url, $comment->comment_author, 15), $this->widget_get_shortened_link(get_comment_link($comment->comment_ID), get_the_title($comment->comment_post_ID), 40)) . '
                            </small>
                        </div></li>';
      }
    }
    $output .= '</ul>';
    $output .= $after_widget;

    echo $output;
    $cache[$args['widget_id']] = $output;
    wp_cache_set('widget_recent_comments', $cache, 'widget');
  }
}

// Register widgets
if (!function_exists('m4txblog_register_widgets')) {
  function m4txblog_register_widgets() {
    register_widget('M4txblog_Widget_Categories');
    register_widget('M4txblog_Widget_Tag_Cloud');
    register_widget('M4txblog_Nav_Menu_Widget');
    register_widget('M4txblog_Widget_Recent_Comments');
  }

  add_action('widgets_init', 'm4txblog_register_widgets', 1);
}