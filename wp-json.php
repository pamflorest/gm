<?php

if (empty($wp)) {
    require_once('wp-config.php');
    wp('feed=rss2');
}

function json_cats() {
    $categories = get_the_category();
    $the_list = '';
    foreach ( (array) $categories as $category ) {
        $category->cat_name = convert_chars($category->cat_name);
        $cats[] = $category->cat_name;
    }
    $the_list = '"' . implode('","', $cats) . '"';
    return apply_filters('the_category_json', $the_list, $type);
}

function json_safe_text($t) {
    return str_replace(
            array("\\",'"', "\n", "\t",  "\r"), 
            array("\\\\", '\\"', "\\n", "\\t", "\\r"), 
            $t);
}

//header('Content-type: application/json; charset=' . get_option('blog_charset'), true);
header('Content-type: text/javascript; charset=' . get_option('blog_charset'), true);
$more = 1;

global $wp_query;
$total_posts = count($wp_query->posts);

?>
<?php if( $_GET['callback'] ) { echo $_GET['callback']?>(<?php } ?>{
    "title": "<?php bloginfo_rss('name'); ?>",
    "link": "<?php bloginfo_rss('url') ?>",
    "self": "<?php bloginfo_rss('url') ?>",
    "description": "<?php bloginfo_rss("description") ?>",
    "language": "<?php echo get_option('rss_language'); ?>",
    "pubDate": "<?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?>",
    "generator": {
        "name": "Wordpress", url: "http://wordpress.org"
    },

    <?php do_action('json_head'); ?>
    
    "entries": [
<?php while( have_posts()) : the_post(); $msdate = get_post_time('Y-m-d H:i:s', true); ?>
    
    {
        "id": "<?php the_guid(); ?>",
        "title": "<?php the_title_rss() ?>",
        "author": {
            "name": "<?php the_author() ?>"
        },
<?php if (get_option('rss_use_excerpt')) : ?>
        "description": "<?php echo json_safe_text(get_the_excerpt()) ?>",
<?php else : ?>
        "description": "<?php echo json_safe_text(get_the_excerpt()) ?>",
<?php if ( strlen( $post->post_content ) > 0 ) : ?>
        "content": "<?php echo json_safe_text(get_the_content()) ?>",
<?php else : ?>
        "content": "<?php echo json_safe_text(get_the_excerpt()) ?>",
<?php endif; ?>
<?php endif; ?>
<?php /*
        description: '<?php echo json_safe_text(get_the_excerpt()) ?>',
*/ ?>
        "categories": [<?php echo json_cats() ?>],
        "link": "<?php permalink_single_rss() ?>",
        "humanDate": "<?php echo mysql2date('D, d M Y H:i:s +0000', $msdate, false); ?>",
        "machineDate": { 
            "month": <?php echo (int) mysql2date('m', $msdate, false); ?>,
            "date": <?php echo (int) mysql2date('d', $msdate, false); ?>,
            "year": <?php echo (int) mysql2date('Y', $msdate, false); ?>,
            "hours": <?php echo (int) mysql2date('H', $msdate, false); ?>,
            "minutes": <?php echo (int) mysql2date('i', $msdate, false); ?>,
            "seconds": <?php echo (int) mysql2date('s', $msdate, false); ?>,
            "timezone": "+0000"
        }<?php do_action('json_item'); ?>
    
    }<?php if( --$total_posts ) { ?>, <?php } ?>
    
<?php endwhile; ?>
    ]
}<?php if( $_GET['callback'] ) { ?>);<?php } ?> 