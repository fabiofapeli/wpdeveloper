<?php get_header(); ?>

<div class="content section-inner">
											        
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div class="posts">
	
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<?php get_template_part( 'content', get_post_format() ); ?>
							
				<div class="post-nav">
				
					<?php
					/*
					A paginação é feita recuperando o post anterior get_previous_post() e próximo get_next_post() e
					exibido os respectivos links através de get_permalink

					*/

					$next_post = get_next_post();
					if (!empty( $next_post )): ?>
				
						<a class="post-nav-newer" title="<?php _e('Next post:', 'lingonberry'); echo ' ' . get_the_title($next_post); ?>" href="<?php echo get_permalink( $next_post->ID ); ?>"><?php echo get_the_title($next_post); ?> &raquo;</a>
				
					<?php endif; ?>
					
					<?php
					$prev_post = get_previous_post();
					if (!empty( $prev_post )): ?>
				
						<a class="post-nav-older" title="<?php _e('Previous post:', 'lingonberry'); echo ' ' . get_the_title($prev_post); ?>" href="<?php echo get_permalink( $prev_post->ID ); ?>">&laquo; <?php echo get_the_title($prev_post); ?></a>
				
					<?php endif; ?>
					
					<div class="clear"></div>
				
				</div> <!-- /post-nav -->
				
				<?php comments_template( '', true ); ?>
											                        
		   	<?php endwhile; else: ?>
		
				<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "lingonberry"); ?></p>
			
			<?php endif; ?>    
	
			</div> <!-- /post -->
		
	</div> <!-- /posts -->

</div> <!-- /content section-inner -->
		
<?php get_footer(); ?>