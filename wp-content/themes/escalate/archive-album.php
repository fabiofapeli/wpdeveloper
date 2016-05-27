<?php get_header(); ?>
                <div class="entry photos">
                    <ul>
                    	<?php while(have_posts()){ the_post();
						?>
						<li>
							<?php the_post_thumbnail('full'); ?>
							<?php
							printf('<h2><a href="%s">%s</a></h2>',
								get_permalink(),
								get_the_title());
							?>
                        </li>
						<?php
                    	} ?>
                    </ul>
                </div>
                <div style="clear: both;">&nbsp;</div>
<?php get_footer();  ?>