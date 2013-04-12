<?php 
/*
Template Name: Página de Anuncio
*/
 ?>

 <?php get_header(); ?>

	<?php $et_full_post = get_post_meta( $post->ID, '_et_full_post', true ); ?>

		<div id="content-area" class="clearfix<?php if ( 'on' == $et_full_post ) echo ' fullwidth'; ?>">
			<div id="left-area">

				<?php //barra superior da página get_template_part('includes/breadcrumbs', 'anucio'); ?>

				<?php get_template_part('loop', 'anuncio'); ?>
			</div> <!-- end #left_area -->
			<?php if ( 'on' != $et_full_post ) get_sidebar(); ?>
		</div> 	<!-- end #content-area -->
	
<?php get_footer(); ?>