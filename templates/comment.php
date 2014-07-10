<div class="panel panel-default comment">
  <?php echo get_avatar($comment, $size = '128'); ?>
  <div class="panel-body">
    <h4 class="media-heading"><?php echo get_comment_author_link(); ?></h4>
    <?php if ($comment->user_id == get_the_author_meta('ID')): ?>
      <span class="label label-danger"><?php _e('admin'); ?></span>
    <?php endif; ?>
    <time data-toggle="tooltip" title="<?php echo get_comment_date('Y.m.d G:i:s') ?>" datetime="<?php echo comment_date('c'); ?>">
      <a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)); ?>"><?php printf(__('%1$s', 'roots'), get_comment_date(),  get_comment_time()); ?></a>
    </time><?php edit_comment_link(__('(Edit)', 'roots'), '', ''); ?>

    <?php if ($comment->comment_approved == '0') : ?>
      <p class="text-warning comment-awaiting-moderation"">
        <?php _e('Your comment is awaiting moderation.', 'roots'); ?>
      </p>
    <?php endif; ?>

    <?php comment_text(); ?>
    <?php echo str_replace('<a class=\'comment-reply-link', '<a class=\'btn btn-default btn-xs comment-reply-link',
        get_comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth'])))); ?>
  </div>
</div>