<?php 

/* TwentyTen functions and definitions for child theme Supermoon  */

add_action( 'after_setup_theme', 'supermoon_setup' );

function supermoon_setup() {
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 280 ) );
        define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 900 ) );
        define('HEADER_IMAGE', get_bloginfo('stylesheet_directory') . '/images/moon3.jpg');
        define( 'NO_HEADER_TEXT', false );

/* Additional default headers. Photos are by Deltina Hay of http://deltina.com */

	$supermoon_dir =	get_bloginfo('stylesheet_directory');
	register_default_headers( array (
		'moon1' => array (
			'url' => "$supermoon_dir/images/moon1.jpg",
			'thumbnail_url' => "$supermoon_dir/images/moon1-thumbnail.jpg",
			'description' => __( 'Moon1 by Deltina Hay', 'supermoon' )
		),
                'moon2' => array (
			'url' => "$supermoon_dir/images/moon2.jpg",
			'thumbnail_url' => "$supermoon_dir/images/moon2-thumbnail.jpg",
			'description' => __( 'Moon2 by Deltina Hay', 'supermoon' )
		),
                'moon3' => array (
			'url' => "$supermoon_dir/images/moon3.jpg",
			'thumbnail_url' => "$supermoon_dir/images/moon3-thumbnail.jpg",
			'description' => __( 'Moon3 by Deltina Hay', 'supermoon' )
		),
                'moon4' => array (
			'url' => "$supermoon_dir/images/moon4.jpg",
			'thumbnail_url' => "$supermoon_dir/images/moon4-thumbnail.jpg",
			'description' => __( 'Moon4 by Deltina Hay', 'supermoon' )
		),
                'moon5' => array (
			'url' => "$supermoon_dir/images/moon5.jpg",
			'thumbnail_url' => "$supermoon_dir/images/moon5-thumbnail.jpg",
			'description' => __( 'Moon5 by Deltina Hay', 'supermoon' )
		),
                'moon6' => array (
			'url' => "$supermoon_dir/images/moon6.jpg",
			'thumbnail_url' => "$supermoon_dir/images/moon6-thumbnail.jpg",
			'description' => __( 'Moon6 by Deltina Hay', 'supermoon' )
		)

	));
}

function supermoon_remove_twenty_ten_headers(){
    unregister_default_headers( array(
        'berries',
        'cherryblossom',
        'concave',
        'fern',
        'forestfloor',
        'inkwell',
        'path' ,
        'sunset')
    );
}
 
add_action( 'after_setup_theme', 'supermoon_remove_twenty_ten_headers', 11 );

function google_fonts(){
echo '<link href="http://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet" type="text/css">'. "\n";
}
add_action('wp_head', 'google_fonts', 0);

require_once('text-wrangler.php');
add_filter('gettext', array('supermoon_Text_Wrangler', 'site_generator'), 10, 4);