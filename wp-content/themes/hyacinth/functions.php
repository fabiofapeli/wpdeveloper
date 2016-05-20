<?php

add_action('after_setup_theme','custom_setup');

function custom_setup(){
	add_filter('show_admin_bar', '__return_false');
	add_action('wp_enqueue_scripts', 'custom_formats');
	register_nav_menu('menu-header', 'Menu de cabeçalho');
	add_filter('excerpt_length', 'custom_ex_len');
	add_filter('excerpt_more', 'custom_ex_more');
}

function custom_formats()
{
	wp_register_style('custom_font','http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900', null, null, 'all');
	wp_register_style('sd',PW_THEME_URL . 'default.css', null, null, 'all');
	wp_register_style('sd-font',PW_THEME_URL . 'fonts.css', null, null, 'all');
	wp_register_style('sd-ie',PW_THEME_URL . 'default_ie6.css', null, null, 'all');

	global $wp_styles;
	$wp_styles->add_data('sd-ie', 'conditional', 'IE-6');

	wp_enqueue_style('custom_font');
	wp_enqueue_style('sd');
	wp_enqueue_style('sd-font');
	wp_enqueue_style('sd-ie');

	 if ( is_single() )
        wp_enqueue_script( 'comment-reply' );
}

function custom_ex_len()
{
	return 10;
}

function custom_ex_more()
{
	return '...';
}

function custom_comments($comment, $args, $depth)
{
	$comment_id = (int) $comment->comment_ID;
	$user_avatar = get_avatar($comment, 160);
	$user_name = get_comment_author();
	$dt = get_comment_date('d/m/Y H:i');
	$content = get_comment_text();
	$reply = get_comment_reply_link(
		[
		'reply_text'	=> 'Responder',
		'respond_id'	=> 'responder',
		'depth'			=> $depth,
		'max_depth'		=> $args['max_depth']
		]
		);
	$html = sprintf('
		<li id="li-comment-%1$d">
                            <span class="comment-avatar">
                                %2$s
                            </span>
                            <div class="comment-content" id="comment-%1$s">
                                <span class="comment-info">
                                    <strong class="comment-author">%3$s</strong>
                                    <span class="comment-date">%4$s</span>
                                </span>
                                <p>%5$s</p>   
                                <p class="comment-reply">
                                    %6$s
                                </p>
                            </div>
		',
		$comment_id,
		$user_avatar,
		$user_name,
		$dt,
		$content,
		$reply
		);
	echo $html;
}


define('PW_URL', get_home_url() . '/');
define('PW_THEME_URL', get_bloginfo('template_url') . '/');
define('PW_SITE_NAME', get_bloginfo('name'));