<?php 
/*
if(is_home()){
    echo "Página inicial";
}else if(is_category()){
    echo the_category();
}else if(is_page()){
    echo the_title();
}
*/
get_header();?>
        <div id="three-column" class="container">
            <div><span class="arrow-down"></span></div>
            <div id="tbox1"> 
                <span class="icon icon-wrench"></span>
                <div class="title">
                    <h2>Maecenas luctus</h2>
                </div>
                <p>Nullam non wisi a sem semper eleifend. Donec mattis libero eget urna. Duis pretium velit ac suscipit mauris. Proin eu wisi suscipit nulla suscipit interdum.</p>
            </div>
            <div id="tbox2"> 
                <span class="icon icon-cogs"></span>
                <div class="title">
                    <h2>Integer gravida</h2>
                </div>
                <p>Proin eu wisi suscipit nulla suscipit interdum. Nullam non wisi a sem semper suscipit eleifend. Donec mattis libero eget urna. Duis pretium velit ac mauris.</p>
            </div>
            <div id="tbox3"> 
                <span class="icon icon-legal"></span>
                <div class="title">
                    <h2>Praesent mauris</h2>
                </div>
                <p>Donec mattis libero eget urna. Duis pretium velit ac mauris. Proin eu wisi suscipit nulla suscipit interdum. Nullam non wisi a sem suscipit semper eleifend.</p>
            </div>
        </div>
        <div id="wrapper3">
            <div id="portfolio" class="container">
                <?php if(!is_home()){ ?>
                <div class="title">
                    <h2>Design Portfolio</h2>
                </div>
                <?php } 
                $i=1;
                while(have_posts()){
                    the_post();
                ?>
                <div class="column<?php echo $i;?>">
                    <div class="box">
                     <?php 
                     $permalink = get_permalink();
                     $title = get_the_title();
                        printf('<a href="%s" title="%s">%s</a>',
                            $permalink,
                            $title,
                            get_the_post_thumbnail()
                            );
                        ?>
                        <h3><?php echo $title; ?></h3>
                        <p><?php the_excerpt(); ?></p>
                        <a href="<?php echo $permalink; ?>" class="button button-small">Leia mais</a>
                    </div>
                </div>
                <?php
                $i++;
                 } ?>
            </div>
            <nav id="pagination" class="container">
                <?php 

                /* 
                <a href="#" title="1">1</a>
                <a href="#" title="1">2</a>
                <span>3</span>
                <a href="#" title="1">4</a> 

                //OPÇÃO 1
                previous_posts_link('Página anterior');
                next_posts_link('Página posterior');
                
                //OPÇÃO 2
                posts_nav_link(' . ', 'Anterior', 'Próxima');
                */
               
                //OPÇÃO 3
                global $wp_query;
                $page_cur = (int) $wp_query->get('paged');
                if (!$page_cur) $page_cur = 1;
                $page_total = (int) $wp_query->max_num_pages;

                echo paginate_links(
                    array(
                        'current'   => $page_cur,
                        'total'     => $page_total,
                        /*
                        Para base tem que definir no admin em Links permanentes a opção
                        Estrutura personalizada como /arquivos/%post_id%
                        */
                        'base'      => str_replace($page_total+1, '%#%', get_pagenum_link($page_total+1)),
                        'prev_next' => false
                        )
                    );
                 ?>
            </nav>   
        </div>
<?php get_footer(); ?>
        
