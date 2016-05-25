<?php

class KDM_Posts
{

	static $prefix = 'grp_';

	function activation(){
		add_option(self::$prefix . 'search', 'wordpress', false, 'no');
		add_option(self::$prefix . 'count', '3', false, 'no');
	}

	function deactivation(){
		delete_option(self::$prefix . 'search');
		delete_option(self::$prefix . 'count');
	}

	function init(){
		add_action('admin_menu', array('KDM_Posts', 'admin_menu'));
	}

	public function admin_menu()
	{
		/*
		add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function_callback);
		$page_title - Título da página
		$menu_title - Título do menu 
		$capability - Perfil com permissão de acesso
		$menu_slug - Identificador único
		$function_callback - função para exibição da página
		add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function_callback);
		$parent_slug = $menu_slug de qual fará parte

		É possível adicionar submenu a menu já existentes das seguintes forma
		Nome do arquivo
		add_submenu_page('index.php', 'Submenu de teste', 'Submenu', 'administrator', 'grp-subdata', array('KDM_Posts', 'subcontent'));
		add_submenu_page('options-general.php', 'Submenu de teste', 'Submenu', 'administrator', 'grp-subdata', array('KDM_Posts', 'subcontent'));
		
		Função específica (sem a necessidade do 1º parâmetro da função add_submenu_page)
		add_theme_page('Submenu de teste', 'Submenu', 'administrator', 'grp-subdata', array('KDM_Posts', 'subcontent'));
		add_plugins_page('Submenu de teste', 'Submenu', 'administrator', 'grp-subdata', array('KDM_Posts', 'subcontent'));
		add_submenu_page('grp-data', 'Submenu de teste', 'Submenu', 'administrator', 'grp-subdata', array('KDM_Posts', 'subcontent'));
		*/
		add_menu_page('Dados remotos', 'Dados remotos', 'administrator', 'grp-data', array('KDM_Posts', 'options_form'));
	}


	function options_form(){
		$fields = self::options_save();
		if(!$fields['search'] || !$fields['count']){
			$fields = array(
				'search'	=> get_option(self::$prefix . 'search'),
				'count'	=> get_option(self::$prefix . 'count')
			);
		}

		//table.form-table>(tr>th+td)*2
			?>
			<div class="wrap get-remore-posts">
				<h2>Configurações do Plugin</h2>
				<form method='post' action="">
					<table class="form-table">
						<tr>
							<th>Termos de busca</th>
							<td><input type="text" name="_search" id="grp-search" size="20" value="<?php echo $fields['search'];?>"></td>
						</tr>
						<tr>
							<th>Qtd de posts</th>
							<td><input type="text" name="_count" id="grp-count" size="20" value="<?php echo $fields['count'];?>"></td>
						</tr>
						<tr>
							<td colspan="2"><input type="submit" value="Salvar" class="button-primary"></td>
						</tr>
					</table>
				</form>
			</div>
			<?php
	}

	function options_save(){
		$fields = array(
			'search' => '',
			'count'	 => '',
			);
		if(!empty($_POST)){
			foreach ($fields as $f => $v) {
				$field = self::$prefix . $f;
				$new = false;
				$old = get_option($field);

				if(isset($_POST['_' . $f]))
					$new = $_POST['_' . $f];

				$fields[$f] = $new;
				if($new && ($new !== $old))
					update_option($field, $new);
				else
					delete_option($field);
			}
		}
			return $fields;
	}

	/*
	function subcontent(){
			echo 'Página Submenu';
	}
	*/
}



?>