<?php
/*

Plugin name: DropIns & MustUse Plugins

*/

add_action('init','custom_init'); // Função inicializadora do plugin

function custom_init(){
	if(!is_admin()){ // Parte pública do site
		require ABSPATH . 'wp-admin/includes/plugin.php';
		echo '<pre>';
		var_dump (_get_dropins()); // Recuper os tipos de plugins dropins

		if(class_exists('db')){  // Verifica existência de uma classe
			$b = new db();
			var_dump($b); // Dump caso exista
		}

		echo '</pre>';
		exit;
	}
}
