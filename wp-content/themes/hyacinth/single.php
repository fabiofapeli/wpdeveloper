<?php 
get_header(); 
the_post();
?>
        <div id="wrapper3">
            <div id="single" class="container">
                <div class="title">
                    <h1><?php echo the_title(); ?></h1>
                </div>
                <div class="post-content">
                   <?php echo the_content(); ?>
                </div>
               <?php comments_template() ?> 
            </div>
        </div>
<?php get_footer(); ?>
