<?php

if(!is_admin()){
	wp_die( 'MU Plugin ativo', '$title');
}