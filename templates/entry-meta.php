<span class="postmeta">
    <i class="ic ic-calendar" data-toggle="tooltip" title="<?php _e('Publication date',
        'roots') ?>" aria-label="<?php _e('Publication date', 'roots'); ?>"></i>
    <time class="published" datetime="<?php echo get_the_time('c'); ?>">
        <?php echo get_the_date(); ?>
    </time>
    <?php
    // Entry categories
    $cats = get_the_category();
    $ic_title = _n('Entry category', 'Entry categories', count($cats), 'roots');

    // Icon
    echo '<i class="ic ic-folder-open" data-toggle="tooltip" title="' . $ic_title .
        '" aria-label="' . $ic_title . '"></i>';

    // List the categories
    $init = true;
    foreach ($cats as $c) {
        $cat = get_category($c);
        if (!$init) {
            echo ', ';
        }
        echo '<a data-toggle="tooltip" title="' .
            sprintf(_n('%d post', '%d posts', $cat->count, 'roots'), $cat->count) . '" href="' .
            get_category_link($cat->cat_ID) . '">' . $cat->name . '</a>';

        $init = false;
    }
    ?>
</span>

<a data-toggle="tooltip" title="<?php _e('Comments') ?>" aria-label="<?php _e('Comments') ?>"
   href="<?php echo get_comments_link(); ?>" class="pull-right badge<?php
$n = get_comments_number();

foreach (unserialize(COMMENT_CLASSES) as $id => $amount) {
    if ($n < $amount) {
        echo ' commentbadge-' . $id;
        break;
    }
}
?>"><?php echo $n; ?></a>
