<?php
/*
Template Name: Template Vídeo
*/
?>
<?php 
	$et_ptemplate_settings = array();
	$et_ptemplate_settings = maybe_unserialize( get_post_meta($post->ID,'et_ptemplate_settings',true) );

	$fullwidth = isset( $et_ptemplate_settings['et_fullwidthpage'] ) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : false;

	$et_ptemplate_blogstyle = isset( $et_ptemplate_settings['et_ptemplate_blogstyle'] ) ? (bool) $et_ptemplate_settings['et_ptemplate_blogstyle'] : false;

	$et_ptemplate_showthumb = isset( $et_ptemplate_settings['et_ptemplate_showthumb'] ) ? (bool) $et_ptemplate_settings['et_ptemplate_showthumb'] : false;

	$blog_cats = isset( $et_ptemplate_settings['et_ptemplate_blogcats'] ) ? (array) $et_ptemplate_settings['et_ptemplate_blogcats'] : array();
	$et_ptemplate_blog_perpage = isset( $et_ptemplate_settings['et_ptemplate_blog_perpage'] ) ? (int) $et_ptemplate_settings['et_ptemplate_blog_perpage'] : 5;
?>

<?php get_header(); ?>
	<h1 class="title"><?php the_title(); ?></h1>
	<div id="content-area" class="clearfix<?php if ( $fullwidth ) echo ' fullwidth'; ?>">
			<div id="left-area">
				<?php $loop = new WP_Query( array(
					'post_type'      => 'video',
					'posts_per_page' => '7',
					'paged' => $paged,
					) );
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
					<?php 
						$thumb = '';
						$width = apply_filters('et_blog_image_width',285);
						$height = apply_filters('et_blog_image_height',215);
						$classtext = '';
						$titletext = get_the_title();
						$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Singleimage');
						$thumb = $thumbnail["thumb"];
					?>
					
					<?php if ( '' != $thumb && 'on' == et_get_option('lucid_page_thumbnails') ) { ?>

				<div class="post-thumbnail">
					<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
				</div> 	<!-- end .post-thumbnail -->
					<?php } ?>
					
				<div class="post_content clearfix">
					<?php the_content(); ?>
				<div id="et_pt_blog" class="responsive">
					<?php $cat_query = ''; 
						if ( !empty($blog_cats) ) $cat_query = '&cat=' . implode(",", $blog_cats);
						else echo '<!-- blog category is not selected -->'; ?>
					<?php 
						$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );
					?>
					
					<!-- start of Loop -->
					<?php query_posts( array(
						'post_type'      => 'video',
						'posts_per_page' => '7',
						'paged' => $paged
						));
					?>
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

					<?php 
						$thumb = '';
						$width = 170;
						$height = 125;
						$classtext = '';
						$titletext = get_the_title();

						$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext);
						$thumb = $thumbnail["thumb"]; 
					?>
							
					<?php if ( $thumb <> '' && !$et_ptemplate_showthumb ) { ?>
						
						<div class="et_pt_thumb alignleft">
							<a href="<?php the_permalink(); ?>">
								<?php 
									//print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); 
									the_crop_image($thumb, '&amp;w=170&amp;h=125&amp;zc=1');
								?>
							</a>
						</div> <!-- end .thumb -->

					<?php }; ?>

					<div class="et_pt_blogentry clearfix">
						<div class="box-post">
						<h2 class="et_pt_title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

							<p class="et_pt_blogmeta">
								<?php esc_html_e('Postado'); ?> <?php esc_html_e('por:'); ?> <?php the_author_posts_link(); ?> 
								<?php esc_html_e('em:'); ?> <?php the_time(get_option('date_format')) ?> 

								<?php if (!$et_ptemplate_blogstyle) { ?>
									<p><a href="<?php the_permalink(); ?>"><?php truncate_post(130);?></a></p>
									<a href="<?php the_permalink(); ?>" class="readmore"><span><?php esc_html_e('Saiba mais &raquo;'); ?></span></a>
								<?php } else { ?>
								<?php
									global $more;
									$more = 0;
								?>
								<?php the_content(); ?>
								<?php } ?>
						</div>
					</div> <!-- end .et_pt_blogentry -->
							
					<?php endwhile; ?><!-- end of loop -->

							<div class="page-nav clearfix">
								<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
								else { ?>
									 <?php get_template_part('includes/navigation'); ?>
								<?php } ?>
							</div> <!-- end .entry -->
						<?php else : ?>
							<?php get_template_part('includes/no-results'); ?>
						<?php endif; wp_reset_query(); ?>
					</div> <!-- end #et_pt_blog -->
					
					<?php wp_link_pages(array('before' => '<p><strong>'.esc_attr__('Pages','Lucid').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link(esc_attr__('Editar está página','Lucid')); ?>

				</div> 	<!-- end .post_content -->
			</article> <!-- end .entry -->
			
	</div> <!-- end #left-area -->
	
	<?php if ( ! $fullwidth ) get_sidebar(); ?>
</div> 	<!-- end #content-area -->	
<?php get_footer(); ?>