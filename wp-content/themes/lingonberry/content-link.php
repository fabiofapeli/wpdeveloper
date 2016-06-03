<div class="post-bubbles">

	<a href="<?php the_permalink(); ?>" class="format-bubble"></a>
	
	<?php if( is_sticky()) {; ?>
		<a href="<?php the_permalink(); ?>" title="<?php _e( 'Sticky post', 'lingonberry'); ?>" class="sticky-bubble"><?php _e( 'Sticky', 'lingonberry'); ?></a>
	<?php } ?>

</div>

<div class="content-inner">

    <?php if ( is_single() ) : ?>

		<div class="post-header">
			
		    <h2 class="post-title">
		    
		    <h2 class="post-title">
		    
		    	<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
		    			    	
		    </h2>
			
			<div class="post-meta">
			
				<span class="post-date"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><span><?php _e('Posted', 'lingonberry'); ?> </span><?php the_time(get_option('date_format')); ?></a></span>
				
				<span class="post-author"> <?php _e('by', 'lingonberry'); ?> <?php the_author_posts_link(); ?></span>
							
				<?php edit_post_link(__('Edit', 'lingonberry')); ?>
							
			</div>
					    	    
		</div> <!-- /post-header -->
	
	<?php endif; ?>

										                                    	    
	<div class="post-content">
		    		            			            	                                                                                            
		<?php the_content(); ?>
				
		<?php wp_link_pages(); ?>
		
	</div> <!-- /post-content -->
	
	<div class="clear"></div>

</div> <!-- /content-inner -->