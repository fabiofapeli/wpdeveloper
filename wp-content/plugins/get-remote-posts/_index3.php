<?php


class KDM_Posts
{

	static $prefix = 'grp_';

	function activation(){
		$opt = array(
			'search'	=> 'wordpress',
			'count'		=> '3'
			);
		add_option(self::$prefix . 'opt', $opt, false, 'no');
	}

	function deactivation(){
		delete_option(self::$prefix . 'opt');
	}

	function init(){
		add_action('admin_menu', array('KDM_Posts', 'admin_menu'));
	}

	public function admin_menu()
	{
		add_menu_page('Dados remotos', 'Dados remotos', 'administrator', 'grp-data', array('KDM_Posts', 'options_form'));
	}


	function options_form(){
			$tab_cur = (isset($_GET['tab']))?$_GET['tab']:'opt';
			?>
			<div class="wrap get-remore-posts">
				<h2>Configurações do Plugin</h2>
				<h2 class="nav-tab-wrapper">
					<?php 
					$tabs = array(
					'opt' => 'Opções',
					'help' => 'Suporte'
					);
					foreach ($tabs as $tab => $label) {
						printf('<a href="%s" class="nav-tab%s">%s</a>',
							admin_url('admin.php?page=grp-data&tab=' . $tab),
							($tab==$tab_cur) ? ' nav-tab-active' : '',
							$label
							);
					}


					 ?>
				</h2>
				<?php 

				if($tab_cur == 'opt'){ //tab opções
				
				settings_errors(); //get_settings_errors(); permite outra forma de tratamento de erro
				?>
				<form method="post" action="options.php">
					<?php 
					settings_fields('grp');
					do_settings_sections('grp');
					submit_button();
					?>
				</form><?php
				}
				else{					//tab Ajuda
					echo '<p>Tela de suporte do plugin...</p>';
				}
				?>
			</div>
			<?php
	}

	function settings()
	{
		$opt = get_option(self::$prefix . 'opt');
		if(!$opt || !is_array($opt))
			$opt = array(
				'search' => '',
				'count'  => ''
				);

		//add_settings_section('grp', 'Opções Personalizadas', array('KDM_Posts', 'section'), 'general'); Página de opções
		add_settings_section('grp-section', 'Opções Personalizadas', array('KDM_Posts', 'section'), 'grp');

		add_settings_field(
			'grp-search',
			'Termos de busca',
			array('KDM_Posts', 'text'),
			'grp',
			'grp-section',
			array(
				'name' => 'search',
				'value' => $opt['search']
				)
			);

		add_settings_field(
			'grp-count',
			'Qtd de posts',
			array('KDM_Posts', 'text'),
			'grp',
			'grp-section',
			array(
				'name' => 'count',
				'value' => $opt['count']
				)
			);
		register_setting('grp', self::$prefix . 'opt', array('KDM_Posts', 'check_count'));
	}

	function section(){
		echo 'Abertura de seção...';
	}

	function text($args){
			echo '<input type="text" name="'. self::$prefix . 'opt[' . $args['name'] . ']' . '"  value="'.$args['value'].'"/>';
	}

	function check_count($value){
		$num = (int) $value['count'];
		if (!$num) {
			$value = false;
			add_settings_error('count', 'isNaN', 'O Valor informado não é um número');
		}
		return $value;
	}

	function get_posts(){
		if(!class_exists('WP_Http'))
			require_once(ABSPATH . WPINC . '/CLASS-HTTP.PHP');
		$opt = get_option(self::$prefix.'opt');
		if(!is_array($opt))
			return false;

		$url = sprintf(
			'http://mazetto.blog.br/remote-posts.php?s=%s&count=%d',
			$opt['search'],
			(int) $opt['count']);

		$r = wp_remote_get($url, array('sslverify'=>false));

		$posts = array();
		if(is_array($r)){
			$data = json_decode($r['body']);

			if(is_object($data) && isset($data->status)){
					if($data->status=='success'){
						foreach($data->content as $d){
							$post = sprintf(
								'<a href="%1$s" title="%2$s">%2$s</a> em %3$s',
								$d->url,
								$d->title,
								date('d/m/Y', strtotime($d->date))
								);
								array_push($posts, $post);
							}
					} else {
						array_push($posts, $data->content);
					}
			} 
		}
		
		if(count($posts)==0)
			array_push($posts, 'Não foi possível acessar o servidor remoto...');	
		
		

		return $posts;
	}

	function show_posts()
	{
		$posts = self::get_posts();

		echo '<div class="get-remote-posts">'
		. '<h2>Publicações remotas</h2>'
		. '<ul>';

		foreach($posts as $p)
			echo "<li>{$p}</li>";

		echo '</ul></div>';
	}

	function list_remote_posts(){
		KDM_Posts::show_posts();
	}


}

register_activation_hook(__FILE__, array('KDM_Posts','activation'));

register_deactivation_hook(__FILE__, array('KDM_Posts', 'deactivation'));

add_action('plugins_loaded', array('KDM_Posts','init'));

add_action('admin_init', array('KDM_Posts', 'settings'));

?>