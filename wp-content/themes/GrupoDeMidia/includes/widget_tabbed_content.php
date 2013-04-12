<li class="clearfix">
	<span class="post-meta"><?php echo get_the_time( 'D' ); ?><span><?php echo get_the_time( 'd' ); ?></span></span>
	<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
	<p class="meta-info"><?php esc_html_e('Postado','Lucid'); ?> <?php esc_html_e('por:','Lucid');?> <?php the_author_posts_link(); ?> 
	<?php esc_html_e('em','Lucid'); ?> <?php echo get_the_category_list(', '); ?></p>
</li>