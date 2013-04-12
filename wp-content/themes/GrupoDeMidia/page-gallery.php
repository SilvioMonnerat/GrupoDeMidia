<?php 
/*
Template Name: Galeria de Fotos
*/
?>
<?php 
$et_ptemplate_settings = array();
$et_ptemplate_settings = maybe_unserialize( get_post_meta($post->ID,'et_ptemplate_settings',true) );

$fullwidth = isset( $et_ptemplate_settings['et_fullwidthpage'] ) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : (bool) $et_ptemplate_settings['et_fullwidthpage'];

$gallery_cats = isset( $et_ptemplate_settings['et_ptemplate_gallerycats'] ) ? $et_ptemplate_settings['et_ptemplate_gallerycats'] : array();
$et_ptemplate_gallery_perpage = isset( $et_ptemplate_settings['et_ptemplate_gallery_perpage'] ) ? (int) $et_ptemplate_settings['et_ptemplate_gallery_perpage'] : 12;
?>

<?php get_header(); ?>

<div id="content-area" class="clearfix<?php if ( $fullwidth ) echo ' fullwidth'; ?>">
	<div id="left-area">
		<?php //get_template_part('includes/breadcrumbs', 'page'); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(' clearfix'); ?>>
		<?php if ( have_posts() ) { while ( have_posts() ) : the_post(); ?>			
			<article id="post-<?php the_ID(); ?>" <?php post_class(' clearfix'); ?>>
				<?php 
					$thumb = '';
					$width = apply_filters('et_blog_image_width',630);
					$height = apply_filters('et_blog_image_height',210);
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
				<h1 class="title"><?php the_title(); ?></h1>
				<div class="post_gallery clearfix">
					
					<?php the_content(); ?>
					
					<div id="et_pt_gallery" class="clearfix responsive">
						<?php 
							$count = 0; global $mb_galeria;
							$gallery_query = '&post_type=' . 'galeria';
							$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );
						?>
						<?php query_posts("showposts=$et_ptemplate_gallery_perpage&paged=" . $et_paged . $gallery_query); ?>
						
						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							
							<?php $width = 207;
							$height = 136;
							$titletext = get_the_title();

							$thumbnail = get_thumbnail($width,$height,'portfolio',$titletext,$titletext,true,'Portfolio');
							$thumb = $thumbnail["thumb"]; ?>
							
							<div class="et_pt_gallery_entry">
								<div class="et_pt_item_image">
									<?php 
										//print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, 'portfolio'); 
										the_crop_image($thumb, '&amp;w=260&amp;h=200&amp;zc=1');
									?>
									<span class="overlay"></span>
									
									<a class="zoom-icon fancybox" title="<?php the_title(); ?>" rel="gallery-<?php echo $count; ?>" href="<?php echo($thumbnail['fullpath']); ?>"><?php esc_html_e('Zoom in','Lucid'); ?></a>
									<?php 
										$mb_galeria->the_meta();
										$meta = $mb_galeria->meta;

										foreach ($meta['docs'] as $key => $doc) {  ?>
											 <a class="fancybox hidden" title="<?php echo $doc['title']; ?>" rel="gallery-<?php echo $count; ?>" href="<?php echo $doc['imgurl']; ?>"></a>
									<?php } ?>
								</div> <!-- end .et_pt_item_image -->
								<div class="title-foto"><?php the_custom_length(get_the_title(), 45); ?></div>
							</div> <!-- end .et_pt_gallery_entry -->
							
						<?php $count++; endwhile; ?>
							<div class="page-nav clearfix">
								<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
								else { ?>
									 <?php get_template_part('includes/navigation'); ?>
								<?php } ?>
							</div> <!-- end .entry -->
						<?php else : ?>
							<?php get_template_part('includes/no-results'); ?>
						<?php endif; wp_reset_query(); ?>
					</div> <!-- end #et_pt_gallery -->
					
					<?php wp_link_pages(array('before' => '<p><strong>'.esc_attr__('Pages','Lucid').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<?php edit_post_link(esc_attr__('Editar está página','Lucid')); ?>
				</div> 	<!-- end .post_content -->
			</article> <!-- end .entry -->
		<?php endwhile; }// end of the loop. ?>
	</div> <!-- end #left-area -->
	
	<?php if ( ! $fullwidth ) get_sidebar(); ?>
</div> 	<!-- end #content-area -->
	
<?php get_footer(); ?>