<?php 

/*
query_posts('post_type=post'); filtro opcional para limitar busca para um tipo único de CPT, porém atualmente esta em desuso por afetar variáveis globais. O mais indicado é usar um filtro add_filter('pre_get_posts', $callback)
*/
get_template_part('index'); //Aqui informamos que a index.php será responsável pelo tratamento da busca 

?>