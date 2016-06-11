<?php 

add_action( 'wp_print_scripts', 'original_style' );

function original_style()
{
	wp_enqueue_style( 'original-2014', get_template_directory_uri() . '/style.css' );  // incorporação do css
}


 ?>