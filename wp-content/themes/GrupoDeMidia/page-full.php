<?php 
/*
Template Name: Full Width Page
*/
?>
<?php get_header(); ?>

<div id="content-area" class="clearfix fullwidth">
	<div id="left-area">
		<?php get_template_part('includes/breadcrumbs', 'page'); ?>
		<?php get_template_part('loop', 'page'); ?>
		<?php if ( 'on' == et_get_option('lucid_show_pagescomments') ) comments_template('', true); ?>
	</div> <!-- end #left-area -->
</div> 	<!-- end #content-area -->
	
<?php get_footer(); ?>