<?php

function custom_metaboxes(){
	add_meta_box( 'box-album', 'Informações adicionais', 'box_album', CPT_ALBUM, 'normal', 'high');
	add_meta_box( 'box-slide', 'Informações adicionais', 'box_slide', CPT_SLIDE, 'normal', 'high');
}

function box_album(){
	global $post;
	$meta = get_post_meta($post->ID, 'add-info', true);
	if(!is_array($meta)){
		$meta =  array(
				'date'	=> '',
				'size'	=> '',
				'techs'	=> '',
				'price'	=> ''
				);
	}
	?>
	<table class="form-table">
		<input type="hidden" name="_custom-box" value="1">
		<tr>
			<th>Data da criação</th>
			<td><input type="text" name="_created" value="<?php echo $meta['date'] ?>" size="20"></td>
		</tr>
		<tr>
			<th>Tamanho das imagens</th>
			<td><input type="text" name="_size" value="<?php echo $meta['size'] ?>" size="20"></td>
		</tr>
		<tr>
			<th>Técnicas utilizadas</th>
			<td><textarea name="_techs" id="" cols="50" rows="4"><?php echo $meta['techs'] ?></textarea></td>
		</tr>
		<tr>
			<th>Preço</th>
			<td><input type="text" name="_price" value="<?php echo $meta['price'] ?>" size="10"></td>
		</tr>
	</table>
	<?php
}

function box_slide(){
	global $post;
	$url = get_post_meta( $post->ID, 'url', true );
	?>
	<table class="form-table">
		<input type="hidden" name="_custom-box" value="1">
		<tr>
			<th>URL</th>
			<td><input type="text" name="_url" value="<?php echo $url ?>" size="50"></td>
		</tr>
	</table>
	<?php
}

function custom_metaboxes_save($post_id){
	$break = false;
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) //verifica se WP esta salvando formulario automaticamente
		$break = true;

	if(empty($_POST) || !isset($_POST['post_type']))//vazio o envio das informações ou se não tiver sido enviado o tipo de post
		$break = true;

	if(!isset($_POST['_custom-box']) && !$_POST['_custom-box']) //verifica campo oculto existe e salva dados
		$break = true;

	if(!$break){
		switch ($_POST['post_type']) {
			case CPT_ALBUM:
				$meta =  array(
					'date'	=> $_POST['_created'],
					'size'	=> $_POST['_size'],
					'techs'	=> $_POST['_techs'],
					'price'	=> $_POST['_price']
					);
				$blank = true;
				foreach ($meta as $k => $v) {	
					if($v){ //ao menos um campo foi preenchido
						$blank = false;
						break;
					}
				}

				if(!$blank)
					update_post_meta( $post_id, 'add-info', $meta ); // insere ou atualiza
				else
					delete_post_meta( $post_id, 'add-info' );
				break;
			case CPT_SLIDE:
				$url = $_POST['_url'];
				if($url)
					update_post_meta( $post_id, 'url', $url ); // insere ou atualiza
				else
					delete_post_meta( $post_id, 'url' );
		}
	}
	return $post_id;
}

function custom_cols($cols, $post_type)
{
	if($post_type == CPT_SLIDE){
		$cols['url'] = 'URL';
		$cols['order'] = 'Ordem';
		$cols['thumb'] = 'Imagem destacada';
	} else if($post_type == CPT_ALBUM){
		$cols['subject'] = 'Assunto';
	}
	return $cols;
}

function custom_cols_content($col)
{
	global $post;
	switch ($col) {
		case 'url':
			echo get_post_meta($post->ID, 'url', true);
			break;
		case 'order':
			echo $post->menu_order;
			break;
		case 'thumb':
			echo get_the_post_thumbnail( $post->ID, array(32,32) );
			break;
		case 'subject';
			$terms = wp_get_post_terms( $post->ID, TAX_SUBJECT );
			$tax = array();
			foreach ($terms as $t) 
				array_push($tax, $t->name);
			
			if(!empty($tax))
				echo implode(' ,', $tax);

			break;
	}
}

function custom_cols_sort($cols) //passa por parâmetro todas as colunas ordenáveis
{
	unset($cols['title']);// caso não queira que seja ordenado por algum campo
	$c = array('order' => 'ordem');
	return array_merge($cols, $c);
}

function custom_cols_request($vars) //passa por parâmetro todos os campos da url
{
	if (is_admin()) { // válido somente no ambiente administrativo
		if (isset($vars['orderby']) && ($vars['orderby'] == 'ordem')) {
			$vars['orderby'] = 'menu_order';
		}

		if(isset($_GET['_subject'])){
			$tax_id = (int) $_GET['_subject'];
			$t = get_term_by( 'ID', $tax_id, TAX_SUBJECT );
			if (isset($t->slug)) {
				$vars['taxonomy'] = TAX_SUBJECT;
				$vars['term'] = $t->slug;
			}
		}
	}
	return $vars;
}

function custom_filter(){
	$subject = (isset($_GET['_subject'])) ? $_GET['_subject'] : '';
	$type = (isset($_GET['post_type'])) ? $_GET['post_type'] : '';

	if ($type == CPT_ALBUM) {
		wp_dropdown_categories( 
			array(
				'name'				=> '_subject',
				'orderby'			=> 'title',
				'taxonomy'			=> TAX_SUBJECT,
				'selected'			=> $subject,
				'show_option_none'	=> 'Todos os assuntos')
			);
	}

}


function custom_sizes($sizes) // Passa os tamanhos padrão do WP thumbnail, medium e big
{
	//Função evitará que WP gere formato de imagens em tamanhos que não serão utilizados
	global $post;
	$type = '';

	if (isset($_POST['post_id'])) {
		$type = get_post_type ($_POST['post_id']);
	} else if (isset($post->post_parent) && ($post->post_parent>0)){
		$type = get_post_type( $post->post_parent );	
	}

	switch ($type) {
		case CPT_SLIDE:
			$sizes = array('feature');
			break;
		
		case CPT_ALBUM:
			$sizes = array('photo-thumb', 'photo-full');
			break;

	}

	return $sizes;
}

function custom_rules($rules) //função de callback recebe parâmetro com todas as opções de reescrita
{
	/*
	//Instrução insere regra no final do array, não funciona e é dispensada pelo WP
	$rules['links/?$'] = "index.php?links=1";
	return $rules;
	//Instrução correta que insere regra no início do array
	*/
	$r = array('links/?$' => "index.php?links=1");
	return array_merge($r, $rules);
}

function custom_vars($qv){ //recebe variáveis como parâmetro
	array_push($qv, 'links');
	return $qv;
}

/*
função irá recuperar os valores que estão sendo recebido pelo wordpress, 
se tiver valor links será exibido a tela personalizada
*/
function custom_redirect()
{
	global $wp_query;
	$l=$wp_query->get('links');
	if($l){
		require TEMPLATEPATH . '/links.php';
		exit;
	}

}
