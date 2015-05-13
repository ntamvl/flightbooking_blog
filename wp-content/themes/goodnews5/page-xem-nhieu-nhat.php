<?php get_header(); ?>
        <div class="inner">
            <div class="main_container">
                <div class="main-col">
                  <div class="base-box page-wrap">
                    <h1 class="page-title">Xem nhiều nhất</h1>
                    <div class="entry-content">
                      <?php
                      $wp_query = new WP_Query( array( 'posts_per_page' => 10,
                            'no_found_rows' => true,
                            'post_status' => 'publish',
                            'ignore_sticky_posts' => true,
                            'meta_key' => '_count-views_all', 'meta_value_num' => '0',
                            'meta_compare' => '>',
                            'orderby'=>'meta_value_num',
                            'order'=> 'DESC',
                            'date_query' => array(
                                'column' => 'post_date',
                                'after' => '- 30 days'
                            )
                          )
                      );
                      while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                     <div itemtype="http://schema.org/Article" itemscope="" class="post-7555 post type-post status-private format-standard has-post-thumbnail hentry category-breaking-news category-du-lich-bui-phuot category-du-lich-mien-nam category-du-lich-trong-nuoc category-feather-article category-kinh-nghiem-du-lich base-box blog-post default-blog-post bp-vertical-share share-off">
                        <div class="bp-entry">
                            <div class="bp-head">
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <iframe src="//www.facebook.com/plugins/like.php?href=<?php the_permalink(); ?>&amp;width&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>
                                <div class="mom-post-meta nb-item-meta"></div>
                                <div class="mom-post-meta bp-meta">
                                  <span>Ngày đăng: <time class="updated" itemprop="datePublished" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_mom_date_format(); ?></time></span>
                                  <span class="post-views-label">Lượt xem: </span>  <?php echo do_shortcode('[post_view]'); ?>
                                </div>

                            </div> <!--blog post head-->
                            <div class="bp-details">
                                <div class="post-img">
                                  <a href="<?php the_permalink(); ?>"><img src="<?php echo mom_post_image('blog_medium'); ?>" data-hidpi="<?php echo mom_post_image('big-wide-img'); ?>" alt="<?php the_title(); ?>"></a>
                                </div> <!--img-->
                                <p>
                                    <?php
                                            $excerpt = get_the_excerpt();
                                            if ($excerpt == false) {
                                            $excerpt = get_the_content();
                                            }
                                            echo wp_html_excerpt(strip_shortcodes($excerpt), $excerpt_length, '...');
                                    ?>
                                 <a href="<?php the_permalink(); ?>" class="read-more-link"><?php _e('Xem thêm', 'theme'); ?> <?php echo $da; ?></a>
                                </p>

                            </div> <!--details-->
                        </div> <!--entry-->
                            <div class="clear"></div>
                    </div>
                    <?php endwhile; ?>
                    </div>
                  </div>

                  <?php mom_pagination($wp_query->max_num_pages); ?>
                  <?php wp_reset_postdata(); ?>
                </div> <!--main column-->
                <?php get_sidebar('secondary'); ?>
            <div class="clear"></div>
            </div> <!--main container-->
                <?php get_sidebar(); ?>
        </div> <!--main inner-->
<?php get_footer(); ?>