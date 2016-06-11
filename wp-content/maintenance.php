<?php

global $upgrading;

printf(
	'<h1>Modo de manutenção</h1>'
	. '<p>Estaremos de volta em <b>%s</b></p>',
	date('d/m/Y H:i', $upgrading)
	);