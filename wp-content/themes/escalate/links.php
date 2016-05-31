<?php
get_header();

$photos = get_posts( 'post_type=' . CPT_ALBUM . '&posts_per_page=3');
?>
				<div class="custom-links">
					<ul>
						<li>
							<h2>Categories</h2>
							<ul>
								<?php wp_list_categories('title_li='); ?>
							</ul>
						</li>
						<li>
							<h2>Achives</h2>
							<ul>
								<?php wp_get_archives(
									array(
										'type' 	=> 'postbypost',
										'limit' => 3)
								); ?>
							</ul>
						</li>
						<li>
							<h2>Photos</h2>
							<ul>
								<?php 
								foreach ($photos as $photo) {
								 	printf('<li><a href="%s">%s</a></li>',
								 		get_permalink( $photo->ID ),
								 		get_the_title( $photo->ID)
								 	);
								 } ?>
							</ul>
						</li>
					</ul>
				</div>
				<!-- end #sidebar -->
<?php get_footer(); ?>
