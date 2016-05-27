<?php 

function post_info($more=false){
	global $post;
	echo '<p class="meta">Posted by ';
	printf(
		'<a href="%s">%s</a> %s</p>',
		get_author_posts_url($post->post_author),
		'todos os posts do autor',
		get_the_author());
	echo ' on ' . get_the_date('F d, Y');

	if ($more) {
		printf(
			' &bull; <a href="%1$s" title="%2$s" class="permalink">%2$s</a>',
			get_permalink(),
			' Artigo completo'
			);
	}

	echo '</p>';

}

function get_image_data($image, $size){
	$alt = get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
	if(!$alt) $alt = $image->post_title; //caso não tenha sido definido um alt pega o titulo da imagem
	list($src) = wp_get_attachment_image_src($image->ID,$size); //pega src da imagem de acordo com o argumento $size (full ou thumbnail)
	return array ($src, $alt);
}

?>