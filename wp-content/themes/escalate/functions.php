<?php 

add_action('after_setup_theme','custom_setup');

function custom_setup(){
	//add_filter('show_admin_bar', '__return_false');
	add_action('wp_enqueue_scripts', 'custom_formats');
	register_nav_menu('menu-header', 'Menu de cabeçalho');
	add_action( 'init', 'custom_init' );
	add_theme_support('post-thumbnails');
	add_filter('pre_get_posts','custom_posts_filter');
	add_action( 'add_meta_boxes', 'custom_metaboxes' );
	add_action( 'save_post', 'custom_metaboxes_save' );

	add_action( 'manage_posts_columns', 'custom_cols', 10, 2);
	add_action( 'manage_'. CPT_SLIDE . '_posts_custom_column', 'custom_cols_content');
	add_action( 'manage_'. CPT_ALBUM . '_posts_custom_column', 'custom_cols_content');
	add_filter( 'manage_edit-' . CPT_SLIDE . '_sortable_columns', 'custom_cols_sort' );
	add_filter( 'request', 'custom_cols_request' );

	add_action( 'restrict_manage_posts', 'custom_filter' );
	//Força o corte para tipos diferente de formato de imagens
	add_image_size( 'feature', 		650, 217, true );// tamanho para slider da home
	add_image_size( 'photo-thumb',	215, 125, true );
	add_image_size( 'photo-full', 	450, 320, true );

	add_filter( 'intermediate_image_sizes', 'custom_sizes', 10, 3 );

	//Gancho para acessar opções de reescrita do wordpress
	add_filter('rewrite_rules_array', 'custom_rules');

	//Novo filtro para registrar a váriável no contexto das variáveis globais do WP
	add_filter('query_vars', 'custom_vars');

	//filtro para redirecionameto
	add_filter('template_redirect', 'custom_redirect');
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

	$attr = array(
		'public'		=> true,
		'hierarchical'	=> true, //true segue modelo de categorias, false segue modelo de tags
		'rewrite'		=> array('slug' => 'assuntos'),
		'labels'		=> array(
			'name'			=> 'Assuntos',
			'singular_name'	=> 'Assunto'
			)
		);

	register_taxonomy( TAX_SUBJECT, CPT_ALBUM, $attr );
	//register_taxonomy( TAX_SUBJECT, array(CPT_ALBUM, 'page'), $attr ); é possível adicionar a taxonomia a mais de um tipo de conteúdo

	if(get_option( 'custom_permalinks') !== CUSTOM_PERMALINKS){
		update_option('custom_permalinks', CUSTOM_PERMALINKS );
		flush_rewrite_rules();
	}
}

define('CPT_ALBUM', 'album');
define('CPT_SLIDE', 'slide');
define('PW_URL', get_home_url() . '/');
define('PW_THEME_URL', get_bloginfo('template_url') . '/');
define('PW_SITE_NAME', get_bloginfo('name'));
define('TAX_SUBJECT', 'subject');
define('CUSTOM_PERMALINKS', 1);

require_once TEMPLATEPATH . '/custom.php';
require_once TEMPLATEPATH . '/dashboard.php';
require_once TEMPLATEPATH . '/class-menu-walker.php';