<?php
/*
Customização dos links de acordo com a formatação do tema
*/
class KDM_Menu_Walker extends Walker_Nav_Menu
{
	function start_el(&$output, $item, $depth=0, $args=array(), $id=0)
	{
		$classes = (empty($item->classes))?array():(array) $item->classes;
		array_push($classes, 'custom-menu-item');

		$output .= sprintf('<li id="custom-item-%d" class="%s">',
			$item->ID,
			implode(' ', $classes)
			);

		$attr = "";

		if(!empty($item->attr_title))
			$attr .= 'title="' . esc_attr($item->attr_title) . '"';

		if(!empty($item->url))
			$attr .= 'href="' . esc_attr($item->url) . '"';

		//if(!empty($item->description) && ($depth = 0)) $depth verifica se o link far parte da raiz do menu
		if(!empty($item->description))
			$description = '<span>' . esc_attr($item->description) . '</span>';

		$output .= '<a href="#" ' . $attr . '>' . $item->title . $description . '</a> ';
		/* 
		variavel output é passada por referência no qual não será necessário retornar nenhum valor que a mesma será atualizada automaticamente
		*/
	}
}