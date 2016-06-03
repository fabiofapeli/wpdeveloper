<div class="footer section">
	
	<div class="footer-inner section-inner">
	
		<?php if ( is_active_sidebar( 'footer-a' ) ) : //Se a widget tiver ativo ?>
		
			<div class="footer-a widgets">
		
				<?php dynamic_sidebar( 'footer-a' ); //revela o conteúdo da sidebar ?>
				
				<div class="clear"></div>
				
			</div>
			
		<?php else : //caso contrário exibido um conteúdo padrão ?>
		
			<div class="footer-a widgets">
			
				<div id="search" class="widget widget_search">
					<div class="widget-content">
					
						<h3 class="widget-title"><?php _e( 'Search form', 'lingonberry' ); ?></h3>
		                <?php get_search_form(); ?>
		                
					</div>
                </div>
				
				<div class="clear"></div>
				
			</div>
			
		<?php endif; ?> <!-- /footer-a -->
			
		<?php if ( is_active_sidebar( 'footer-b' ) ) : ?>
		
			<div class="footer-b widgets">
		
				<?php dynamic_sidebar( 'footer-b' ); ?>
				
				<div class="clear"></div>
				
			</div>
			
		<?php else : ?>
		
			<div class="footer-b widgets">
			
				<div class="widget widget_recent_entries">
					<div class="widget-content">
					
						<h3 class="widget-title"><?php _e( 'Latest posts', 'lingonberry' ); ?></h3>
						<ul>
			                <?php
								$args = array( 'numberposts' => '5' );
								$recent_posts = wp_get_recent_posts( $args );
								foreach( $recent_posts as $recent ){
									echo '<li><a href="' . get_permalink($recent["ID"]) . '" title="'.esc_attr($recent["post_title"]).'" >' .   $recent["post_title"].'</a> </li> ';
								}
							?>
						</ul>
		                
					</div>
                </div>
				
				<div class="clear"></div>
				
			</div>
			
		<?php endif; ?> <!-- /footer-b -->
							
		<?php if ( is_active_sidebar( 'footer-c' ) ) : ?>
		
			<div class="footer-c widgets">
		
				<?php dynamic_sidebar( 'footer-c' ); ?>
				
				<div class="clear"></div>
				
			</div>
			
		<?php else : ?>
		
			<div class="footer-c widgets">
			
				<div id="meta" class="widget widget_text">
					<div class="widget-content">
					
						<h3 class="widget-title"><?php _e( "Add some widgets, won't you?", "lingonberry" ); ?></h3>
						<p><?php _e( "This message is displayed because you haven't added any widgets to the footer yet. You can do so in the settings page at Appearance > Widgets.", "lingonberry" ); ?></p>
		                
					</div>
                </div>
				
				<div class="clear"></div>
				
			</div>
			
		<?php endif; ?> <!-- /footer-c -->
		
		<div class="clear"></div>
	
	</div> <!-- /footer-inner -->

</div> <!-- /footer -->

<div class="credits section">

	<div class="credits-inner section-inner">

		<p class="credits-left">
		
			<span><?php _e('Copyright', 'lingonberry'); ?></span> &copy; <?php echo date("Y") ?> <a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a>
		
		</p>
		
		<p class="credits-right">
			
			<span><?php printf( __( 'Theme by <a href="%s">Anders Noren</a>', 'lingonberry'), 'http://www.andersnoren.se' ); ?> &mdash; </span><a title="<?php _e('To the top', 'lingonberry'); ?>" class="tothetop"><?php _e('Up', 'lingonberry' ); ?> &uarr;</a>
			
		</p>
		
		<div class="clear"></div>
	
	</div> <!-- /credits-inner -->
	
</div> <!-- /credits -->

<?php wp_footer(); ?>

</body>
</html>