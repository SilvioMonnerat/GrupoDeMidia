<?php 
/*
	Template Name: Template Pulso
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

	//$rss = mLoadXml('http://www.blogpulso.com.br/feed/atom/');
	set_time_limit(60);
    $rss = mLoadXml('http://www.blogpulso.com.br/feed/', True, True);

?>
<?php get_header(); ?>
	<div id="content-area" class="clearfix<?php if ( $fullwidth ) echo ' fullwidth'; ?>">
		<div id="left-area">
			<?php if(have_posts()): while(have_posts()): the_post(); ?>
					<h1 class="title"><?php the_title(); ?></h1>
						<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>

							<?php 
								$thumb = '';
								$width = apply_filters('et_blog_image_width',170);
								$height = apply_filters('et_blog_image_height',125);
								$classtext = '';
								$titletext = get_the_title();
								$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Singleimage');
								$thumb = $thumbnail["thumb"];
							?>
								
							<?php if ( '' != $thumb && 'on' == et_get_option('lucid_page_thumbnails') ) { ?>
								<div class="post-thumbnail">
									<?php 
										//print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); 
										the_crop_image($thumb, '&amp;w=170&amp;h=125&amp;zc=1');
									?>
								</div> 	<!-- end .post-thumbnail -->
							<?php } ?>
							<div class="post_content clearfix">

						    <div id="et_pt_blog" class="responsive">
						    	<?php the_content(); ?>
							<?php $cat_query = ''; 
								if ( !empty($blog_cats) ) $cat_query = '&cat=' . implode(",", $blog_cats);
								else echo '<!-- blog category is not selected -->'; ?>
							<?php 
								$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );
							?>

							<?php if($rss) : 

							    $posts = $rss->channel->xpath('item');
							    foreach ($posts as $k => $item) { ?>

							<?php 

						        $author = (string)$item->creator;
						        $title = (string)$item->title;
						        $link = (string)$item->link;
						        $description = (string)$item->description;
						        $imgfile = ereg_replace(' ', '-', $author);
						        $imgfile = toASCII($imgfile);
		         				$img = "http://www.blogpulso.com.br/wp-content/uploads/2013/02/$imgfile.jpg";
								
								$width = 170;
								$height = 125;

								$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext);
								$thumb = $thumbnail["thumb"]; 
							?>
									
							<?php if ( $img <> '' && !$et_ptemplate_showthumb ) { ?>
								<div class="et_pt_thumb alignleft">
									<a href="<?php echo $link; ?>" target="_blank"><span class="overlay">
									<?php 
									//print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); 
									the_crop_image($img, '&amp;w=170&amp;h=125&amp;zc=1');
									?>
									</span></a>
									<!-- <a href="<?php echo $link; ?>" target="_blank"><span class="overlay"><img src="<?php echo $img ?>" alt="" width="<?php echo $width ?>" height="<?php echo $height ?>"></span></a> -->
								</div> <!-- end .thumb -->
							<?php }; ?>
							<div class="et_pt_blogentry clearfix">
								<div class="box-post">
								<h2 class="et_pt_title"><a href="<?php echo $link; ?>" target="_blank"><?php echo $title; ?></a></h2>

									<p class="et_pt_blogmeta">
										<?php if (!$et_ptemplate_blogstyle) { ?>
											<p><a href="<?php echo $link; ?>" target="_blank"><?php the_custom_length($description, 150);?></a></p>
											<a href="<?php echo $link; ?>" class="readmore" target="_blank"><span><?php esc_html_e('Ir para o Pulso &raquo;'); ?></span></a>
										<?php } ?>
							</div> <!-- end .et_pt_blogentry -->
						</div>
							<?php } ?><!-- end of loop -->
							
								<?php else : ?>
									<?php get_template_part('includes/no-results'); ?>
								<?php endif; wp_reset_query(); ?>
							</div> <!-- end #et_pt_blog -->
							
							<?php wp_link_pages(array('before' => '<p><strong>'.esc_attr__('Pages','Lucid').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
							<?php edit_post_link(esc_attr__('Editar está página','Lucid')); ?>

						</div> 	<!-- end .post_content -->
						</article><!-- end article -->
					<?php endwhile; ?>
				<?php else: ?>
			<?php endif; ?>
		</div><!-- end #left-area -->
	<?php if ( ! $fullwidth ) get_sidebar(); ?>
</div><!-- end #content-area -->
<?php get_footer(); ?>