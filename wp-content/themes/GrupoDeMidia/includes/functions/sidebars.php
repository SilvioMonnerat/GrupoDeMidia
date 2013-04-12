<?php
if ( function_exists('register_sidebar') ) {
	register_sidebar( array(
		'name' => 'Sidebar Homepage',
		'id' => 'sidebar2',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div> <!-- end .widget -->',
		'before_title' => '<h3 class="main-title">',
		'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
		'name' => 'Sidebar',
		'id' => 'sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div> <!-- end .widget -->',
		'before_title' => '<h3 class="main-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => 'Sidebar Notícia',
		'id' => 'sidebar3',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div> <!-- end .widget -->',
		'before_title' => '<h3 class="main-title">',
		'after_title' => '</h3>',
	) );			
	
	register_sidebar( array(
		'name' => 'Homepage Noticias #1',
		'id' => 'recent-area-1',
		'before_widget' => '<div class="recent-category_widget">',
		'after_widget' => '</div> <!-- end .recent-category_widget -->',
		'before_title' => '<h3 class="main-title">',
		'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
		'name' => 'Homepage Blog Pulso #2',
		'id' => 'recent-area-2',
		'before_widget' => '<div class="recent-category_widget">',
		'after_widget' => '</div> <!-- end .recent-category_widget -->',
		'before_title' => '<h3 class="main-title">',
		'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
		'name' => 'Homepage Conteúdo patrocinado #3',
		'id' => 'recent-area-3',
		'before_widget' => '<div class="recent-category_widget">',
		'after_widget' => '</div> <!-- end .recent-category_widget -->',
		'before_title' => '<h3 class="main-title">',
		'after_title' => '</h3>',
	) );
	
	register_sidebar( array(
		'name' => 'Footer Area #1',
		'id' => 'footer-area-1',
		'before_widget' => '<div id="%1$s" class="f_widget %2$s">',
		'after_widget' => '</div> <!-- end .footer-widget -->',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	) );
	
	register_sidebar( array(
		'name' => 'Footer Area #2',
		'id' => 'footer-area-2',
		'before_widget' => '<div id="%1$s" class="f_widget %2$s">',
		'after_widget' => '</div> <!-- end .footer-widget -->',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	) );
	
	register_sidebar( array(
		'name' => 'Footer Area #3',
		'id' => 'footer-area-3',
		'before_widget' => '<div id="%1$s" class="f_widget %2$s">',
		'after_widget' => '</div> <!-- end .footer-widget -->',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	) );
}