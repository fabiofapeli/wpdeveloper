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

		/*
		add_settings_section irá agrupar o campos do formulário
		Essa função possui os seguintes parâmetros
		add_settings_section($id, $title, $callback, '$page);

		A seção pode ser adicionada também a uma página já existente no caso a general - para página de opções, writing para escrita e etc
		add_settings_section('grp', 'Opções Personalizadas', array('KDM_Posts', 'section'), 'general'); 

		*/
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
		/*
		$request=new WP_Http();
		$r=$request->request($url, array('method' => 'GET', 'sslverify' => false));	
		*/
		$r = wp_remote_get($url, array('sslverify'=>false));
		/*
		Outras opções
		$r = wp_remote_post($url, array('sslverify'=>false));
		$r = wp_remote_head($url, array('sslverify'=>false));
		$r = wp_remote_request($url, array('method' => 'GET','sslverify'=>false));
		*/
		$data = json_decode($r['body']);
		var_dump($data);
	}


}


?>