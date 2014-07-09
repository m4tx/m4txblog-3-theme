<footer class="content-info" role="contentinfo">
    <div class="container">
        <?php dynamic_sidebar('sidebar-footer'); ?>
        <p class="pull-left">© 2010-<?php echo date('Y'); ?> Mateusz Maćkowski<br/>
            <?php _e('All texts on the <a rel="license" class="link" href="http://creativecommons.org/licenses/by-nd/4.0/">CC-BY-ND</a> license.', 'roots');
            echo ' <a href="' . site_url('/cookies/') . '">' . __('Cookies', 'roots') . '</a>'; ?></p>
        <a class="pull-right" id="fork-me-gh" href="https://github.com/m4tx/m4txblog-3-theme" data-toggle="tooltip"
           data-placement="top" title="m4txblog³ theme on GitHub">
            <i class="ic ic-github ic-3x"></i>
        </a>
    </div>
</footer>
<?php wp_footer(); ?>

<?php if (current_user_can('administrator')): ?>
    <!--
    Queries: <?php echo get_num_queries() . "\n";
    global $wpdb;
    foreach ($wpdb->queries as $query) {
        echo $query[0] . "\n";
    }?>
    Time: <?php timer_stop(1); ?>s
    Memory: <?php echo number_format(memory_get_usage() / 1024 / 1024, 2); ?>MB
    -->
<?php endif; ?>
