<?php
get_header();

the_post();
?>
				<div id="content">
					<div class="post">
						<h2 class="title"><?php the_title(); ?></h2>
						<?php post_info(); ?>
						<div class="entry">
							<?php the_content(); ?>	
						</div>
					</div>
					<div style="clear: both;">&nbsp;</div>
				</div>
				<!-- end #content -->
				<?php get_sidebar(); 
			     get_footer(); ?>