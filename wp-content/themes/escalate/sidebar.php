<?php // ?>
			<div id="sidebar">
					<ul>
						<li>
							<h2>Formulário de busca</h2>
							<?php get_search_form(); ?>
						</li>
						<li>
							<h2>Categories</h2>
							<ul>
								<?php
								 // $cats = get_categories(); opção personalizada para exibição de lista de categorias wp_list_categories
								wp_list_categories('title_li='); // função pronta para lista de categorias, argumento usado para ocultar título CATEGORIES
								?>
							</ul>
						</li>
						
						<li>
							<h2>Archives (Posts)</h2>
							<ul>
								<?php 
								//$post = get_posts(); forma tradicional de listar posts
								wp_get_archives(
									array(
										'type'  => 'postbypost', //tipo de listagem
										'limit' => 4));
								?>
							</ul>
						</li>
					</ul>
				</div>
				<!-- end #sidebar -->