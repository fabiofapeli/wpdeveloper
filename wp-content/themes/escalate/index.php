<?php
get_header();
?>
				<div id="content">
					<?php 
					if(is_search()) // se a requisição atual for uma pesquisa
						printf('<h2>Busca "%s"</h2>', get_search_query()); //Inclui um título com o termo da pesquisa
					if (!have_posts()) {
						echo '<h2>Nenhum post foi encontrado...</h2>';
					}else{
						while (have_posts()) {	
							the_post();
							global $post;
							$permalink=get_permalink();
							$title=get_the_title();
							?>
					<div class="post">
						<h2 class="title"><?php 
						printf(
						'<a href="%s" title="%s">%s</a>',
						$permalink,
						'Leia o post completo',
						$title
						) ?></h2>
						<?php post_info(true); ?>
						<div class="entry"> <?php 
						if (has_post_thumbnail()) {
						 	printf(
						 		'<a href="%s" title="%s" class="image image-full">%s</a>',
						 		$permalink,
						 		$title,
						 		get_the_post_thumbnail($post->ID, 'full')
						 		);
						 } 
						  the_excerpt(); ?>
						</div>
					</div>
					<?php
						} posts_nav_link();
					}
					?>
					<div style="clear: both;">&nbsp;</div>
				</div>
				<!-- end #content -->
				<?php get_sidebar(); ?>
<?php get_footer(); ?>