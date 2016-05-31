<?php
get_header();
$slides = new WP_Query(
    array(
        'post_type'         => CPT_SLIDE,
        'posts_per_page'    => 2,
        'orderby'           => 'menu_order',
        'order'             => 'Asc'
        )
    );

$albums = new WP_Query(
    array(
        'post_type'         => CPT_ALBUM,
        'posts_per_page'    => 3,
        'orderby'           => 'menu_order',
        'order'             => 'Asc'
        )
    );

$posts = get_posts( 'posts_per_page=5' ); //recuperar os últimos 5 posts publicados
 ?>
				<div id="content">
                    <?php if ($slides->have_posts()) { ?>
                    <div class="entry slideshow">
                        <ul id="slider">
                            <?php 
                            while ($slides->have_posts()) {
                               $slides->the_post();
                               global $post;
                               $url = get_post_meta( $post->ID, 'url', true );
                               if(!$url) $url='#';
                               ?>
                            <li>
                                <a href="<?php echo $url; ?>" class="image image-full">
                                    <?php the_post_thumbnail('feature'); ?>
                                    <span><?php echo $post->post_content; ?></span>
                                </a>
                            </li>
                               <?php
                            } wp_reset_query(); //Quando usamos várias instâncias da classe WP_Query devemos usar essa função de reset
                                                // Reseta todas váriaveis globais e evita que o mesmo CPT seja referenciado no proximo loop
                             ?>
                        </ul>
                    </div>
                    <?php 
                    } 
                    if ($albums->have_posts()) {
                       ?>
                       <div class="features">
                        <ul>
                            <?php while($albums->have_posts()){ $albums->the_post(); global $post;
                                ?>
                            <li>
                                <h3><?php printf(
                                '<a href="%1$s">%2$s</a>',
                                get_permalink(),
                                get_the_title());
                                 ?></h3>
                                <?php the_post_thumbnail('photo-thumb'); ?>
                                <?php the_excerpt(); ?>
                            </li>
                                <?php
                            } wp_reset_query(); ?>
                        </ul>
                    </div>
                       <?php
                    }
                    ?>
                    
					<div style="clear: both;">&nbsp;</div>
				</div>
				<!-- end #content -->
	        <?php 
	        get_sidebar(); 
	        get_footer();
	        ?>