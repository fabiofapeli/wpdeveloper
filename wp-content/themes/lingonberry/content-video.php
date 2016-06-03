<div class="post-bubbles">

	<a href="<?php the_permalink(); ?>" class="format-bubble"></a>
	
	<?php if( is_sticky()) {; ?>
		<a href="<?php the_permalink(); ?>" title="<?php _e( 'Sticky post', 'lingonberry'); ?>" class="sticky-bubble"><?php _e( 'Sticky', 'lingonberry'); ?></a>
	<?php } ?>

</div>

<div class="content-inner">

	<div class="post-header">
	
		<?php

/*
Na página dos Posts há um box para inclusão de url de vídeo que será incorporado.

Porém a incorporação dos vídeos esta sendo feita de forma manual, dessa forma caso desejemos imprimir apenas a url de um vídeo, 
não será possível pois será feito a incoporação automaticamente. Já caso seja inserido uma nova url de vídeo diferente dos formatos
esperados por essa página não será feita a incorporação. Para fazer a incorporação automático deveremos usar a action '' API oEmbed para incorporação
de videos de fontes externas não reconhecidos por esta página.


*/

		 $videourl = get_post_meta($post->ID, 'videourl', true); //recuperado o valor do metadado

		 if ( $videourl != '' ) : ?>

			<div class="featured-media">
			
				<?php if (strpos($videourl,'youtube') !== false) : ?>
				
					<?php $url = $videourl;
					preg_match(
					        '/[\\?\\&]v=([^\\?\\&]+)/',
					        $url,
					        $matches
					    );
					$id = $matches[1];
					 
					echo '<iframe width="1136" height="568" src="http://www.youtube.com/embed/' . $id . '?autoplay=0" allowfullscreen></iframe>' ?>
					
				<?php elseif (strpos($videourl,'vimeo') !== false) : ?>
				
					<?php $url = $videourl;
					preg_match(
					        '/(\d+)/',
					        $url,
					        $matches
					    );
					$id = $matches[1];
					 
					echo '<iframe width="1136" height="568" src="http://player.vimeo.com/video/' . $id . '" allowfullscreen></iframe>' ?>
					
				<?php elseif (strpos($videourl,'dailymotion') !== false) : ?>
				
					<?php $url = $videourl;
					preg_match(
					        '/video\/([^_]+)/',
					        $url,
					        $matches
					    );
					$id = $matches[1];
					 
					echo '<iframe width="1136" height="568" src="http://www.dailymotion.com/embed/video/' . $id . '" allowfullscreen></iframe>' ?>
					
				<?php elseif (strpos($videourl,'kickstarter') !== false) : ?>
				
					<?php $url = $videourl;
					preg_match(
					        '/projects\/([^?]+)/',
					        $url,
					        $matches
					    );
					$id = $matches[1];
					 
					echo '<iframe width="1136" height="568" src="http://www.kickstarter.com/projects/' . $id . '/widget/video.html" allowfullscreen></iframe>' ?>
															
				<?php elseif (strpos($videourl,'.mp4') !== false) : ?>
					
					<video controls>
					
						<source src="<?php echo $videourl ?>" type="video/mp4">
						<?php _e('Your browser does not support the video tag.', 'lingonberry'); ?>
						
					</video>
						
				<?php endif; ?>
				
			</div>
		
		<?php endif; ?>
		
	    <h2 class="post-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
	    
	    <div class="post-meta">
		
			<span class="post-date"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><span><?php _e('Posted', 'lingonberry'); ?> </span><?php the_time(get_option('date_format')); ?></a></span>
				
				<span class="post-author"> <?php _e('by', 'lingonberry'); ?> <?php the_author_posts_link(); ?></span>
						
			<?php edit_post_link(__('Edit', 'lingonberry')); ?>
						
		</div>
	    
    </div> <!-- /post-header -->
    
										                                    	    
    <div class="post-content">
    	    		            			            	                                                                                            
		<?php the_content(); ?>
				
		<?php wp_link_pages(); ?>
					        
    </div> <!-- /post-content -->
    
	<div class="clear"></div>
	
	<?php if (is_single() ) : ?>
	
		<div class="post-cat-tags">
					
			<p class="post-categories"><?php _e('Categories:', 'lingonberry'); ?> <?php the_category(', '); ?></p>
		
			<p class="post-tags"><?php the_tags(__('Tags: ', 'lingonberry'),', '); ?></p>
		
		</div>
		
	<?php endif; ?>
        
</div> <!-- /post content-inner -->