<?php

// Plugin Name: File Header API
// Author: Kodame

if(!is_admin()){ //Verifica se o usuário não esta logado no painel

	//Recupera dados de um tema
	$info=array('Theme Name', 'Author'); // Dados desejados
	$d = get_file_data(ABSPATH . 'wp-content/themes/twentyfourteen/style.css', $info); // Usamo a função para recuperar dados de estilo do tema
	/*
	echo '<pre>';

	
	var_dump($d); 
	exit;
	*/

	//Recuperar dados do plugin atual
	require ABSPATH . 'wp-admin/includes/plugin.php';
	$p = get_plugin_data(__FILE__);

	/*
	var_dump($p);
	exit;
	*/

	//Recuperar informações de um tema em forma de objeto
	$t = wp_get_theme(); // Caso não seja passado argumento será recuperada informações do tema ativo, para recuperar informações de um tema específico deve se passar o caminho da folha de estilo do tema

	/*
	var_dump($t);
	exit;
	*/

	//Para recuperar alguma informação específica podemos utilizar o método get da instância recém criada
	/*
	var_dump($t->get('Name'), $t->get('ThemeURI'));

	var_dump($t->get_files(array('css'))); // Recuperamos todos os aquivos css contidos no tema
	var_dump($t->get_files(array('css'))); // Recuperamos todos os aquivos js contidos no tema
	var_dump($t->get_files()); // Exibe todos os aquivos independente do formato

	exit;
	*/

}