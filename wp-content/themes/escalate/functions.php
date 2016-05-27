<?php 

add_action('after_setup_theme','custom_setup');

function custom_setup(){
	add_filter('show_admin_bar', '__return_false');
	add_action('wp_enqueue_scripts', 'custom_formats');
	register_nav_menu('menu-header', 'Menu de cabeçalho');
	add_action( 'init', 'custom_init' );
	add_theme_support('post-thumbnails');
	add_filter('pre_get_posts','custom_posts_filter');
}

function custom_formats()
{

	wp_register_style( 'esc-font', 'http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600', null, null, 'all' );
	wp_register_style( 'esc-main', PW_THEME_URL . 'style.css', null, null, 'all' );

	wp_enqueue_style( 'esc-font');
	wp_enqueue_style( 'esc-main');

	
	wp_register_script( 'esc-jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', null, null, true );
	wp_register_script( 'esc-circle', 'http://malsup.github.com/jquery.cycle.lite.js', null, null, true );
	wp_register_script( 'esc-script', PW_THEME_URL . 'js/script.js', null, null, true );
	wp_enqueue_script( 'esc-jquery');
	wp_enqueue_script( 'esc-circle');
	wp_enqueue_script( 'esc-script');

}

function custom_posts_filter($q){
	if($q->is_search){
		$q->set('post_type', CPT_ALBUM); //Limitada a busca para o CPT Álbum
		//$q->set('posts_per_page', -1); Podemos incluir diversas condições a busca, no caso um número ilimitado de resultados
	}
	return $q;
}

function custom_init(){
	$attr = array(
		'public'	=> false,
		'show_ui'	=> true,
		'supports'	=> array( 'title', 'editor', 'page-attributes', 'thumbnail'),
		'labels'	=> array(
			'name'		=> 'Slides',
			'singular_name'	=> 'Slide'
			)
		);
	register_post_type( CPT_SLIDE, $attr );

	$attr = array(
		'public'		=> true,
		'has_archive'	=> true,
		'rewrite'		=> array('slug'=>'fotos'),
		'supports'		=> array('title', 'excerpt', 'editor', 'page-attributes', 'thumbnail'),
		'labels'		=> array(
			'name'			=> 'Álbuns',
			'singular_name'	=> 'Álbum'		
		)
	);
	register_post_type(CPT_ALBUM, $attr);

	if(get_option( 'custom_permalinks') !== CUSTOM_PERMALINKS){
		update_option('custom_permalinks', CUSTOM_PERMALINKS );
		flush_rewrite_rules();
	}
}

define('CPT_ALBUM', 'album');
define('CPT_SLIDE', 'Slide');
define('PW_URL', get_home_url() . '/');
define('PW_THEME_URL', get_bloginfo('template_url') . '/');
define('PW_SITE_NAME', get_bloginfo('name'));

define('CUSTOM_PERMALINKS', 1);

require_once TEMPLATEPATH . '/custom.php';