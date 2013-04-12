<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
	<?php 
		$index_postinfo = et_get_option('lucid_postinfo1');
		
		$thumb = '';
		$width = apply_filters('et_blog_image_width',170);
		$height = apply_filters('et_blog_image_height',125);
		$classtext = '';
		$titletext = get_the_title();
		$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Blogimage');
		$thumb = $thumbnail["thumb"];
		
		?>	
	<?php if ( 'on' == et_get_option('lucid_thumbnails_index','on') && '' != $thumb ){ ?>
		
		<div class="category-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
				<span class="overlay"></span>
			</a>	
		</div> 	<!-- end .post-thumbnail -->
	<?php } ?>
	<div class="et_pt_blogentry clearfix">
		<h2 class="et_cat_title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="et_pt_blogmeta">
			<?php esc_html_e('Postado'); ?> <?php esc_html_e('por:'); ?> <?php the_author_posts_link(); ?> 
				<?php esc_html_e('em:'); ?> <?php the_time(get_option('date_format')); ?> 

				<?php if (!$et_ptemplate_blogstyle) { ?>
					<p><?php truncate_post(130);?></p>
					<a href="<?php the_permalink(); ?>" class="readmore"><span><?php esc_html_e('Saiba mais &raquo;'); ?></span></a>
				<?php } else { ?>
				<?php
					global $more;
					$more = 0;
				?>
				<?php the_content(); ?>
				<?php } ?>
	</div> <!-- end .post_content -->
</article> <!-- end .entry -->