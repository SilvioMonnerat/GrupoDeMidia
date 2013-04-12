<?php

/**
 * Adicionamos uma acção no inicio do carregamento do WordPress
 * através da função add_action( 'init' )
 */
add_action( 'init', 'create_post_type_noticia' );

/**
 * Esta é a função que é chamada pelo add_action()
 */
function create_post_type_noticia() {

    /**
     * Labels customizados para o tipo de post
     * 
     */
    $labels = array(
	    'name'                 => _x('Notícia', 'post type general name'),
	    'singular_name'        => _x('Notícia', 'post type singular name'),
	    'add_new'              => _x('Adicionar Novo', 'noticia'),
	    'add_new_item'         => __('Adicionar Novo Notícia'),
	    'edit_item'            => __('Editar Notícia'),
	    'new_item'             => __('Novo Notícia'),
	    'all_items'            => __('Notícia'),
	    'view_item'            => __('Vizualizar Notícia'),
	    'search_items'         => __('Pesquisar por Notícia'),
	    'not_found'            => __('Nenhum Notícia encontrado'),
	    'not_found_in_trash'   => __('Nenhum Notícia encontrado na lixeira'),
	    'parent_item_colon'    => '',
	    'menu_name'            => 'Notícia'
    );
    
    /**
     * Registamos o tipo de post ´noticia através desta função
     * passando-lhe os labels e parâmetros de controlo.
     */
    register_post_type( 'noticia', array(
	    'labels'               => $labels,
	    'public'               => true,
	    'publicly_queryable'   => true,
	    'show_ui'              => true,
	    'show_in_menu'         => true,
	    'has_archive'          => 'noticias',
	    'rewrite'              => array('slug' 
	    	                   => 'noticias','with_front' 
	    	                   => false,),
	    'capability_type'      => 'post',
	    'has_archive'          => true,
	    'hierarchical'         => false,
	    'menu_position'        => 6,
	    'supports'             => array('title','editor','author','thumbnail','excerpt','comments')
	    )
    );
    
    /**
     * Registamos a categoria de noticia para o tipo de post noticia
     */
    register_taxonomy( 'noticia_category', array( 'noticia' ), array(
        'hierarchical'         => true,
        'label'                => __( 'Notícia Category' ),
        'labels'               => array( // Labels customizadas
	    'name'                 => _x( 'Categories', 'taxonomy general name' ),
	    'singular_name'        => _x( 'Category', 'taxonomy singular name' ),
	    'search_items'         => __( 'Search Categories' ),
	    'all_items'            => __( 'All Categories' ),
	    'parent_item'          => __( 'Parent Category' ),
	    'parent_item_colon'    => __( 'Parent Category:' ),
	    'edit_item'            => __( 'Edit Category' ),
	    'update_item'          => __( 'Update Category' ),
	    'add_new_item'         => __( 'Add New Category' ),
	    'new_item_name'        => __( 'New Category Name' ),
	    'menu_name'            => __( 'Categorias' ),
	),
        'show_ui'              => true,
        'show_in_tag_cloud'    => true,
        'query_var'            => true,
        'rewrite'              => array('slug'
                               => 'noticia/categories','with_front' 
                               => false,),
        )
    );
    
    /** 
     * Esta função associa tipos de categorias com tipos de posts.
     * Aqui associamos as tags ao tipo de post noticia.
     */
    register_taxonomy_for_object_type( 'tags', 'noticia' );
    
}
