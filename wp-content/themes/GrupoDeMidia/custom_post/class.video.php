<?php

/**
 * Adicionamos uma acção no inicio do carregamento do WordPress
 * através da função add_action( 'init' )
 */
add_action( 'init', 'create_post_type_video' );

/**
 * Esta é a função que é chamada pelo add_action()
 */
function create_post_type_video() {

    /**
     * Labels customizados para o tipo de post
     * 
     */
    $labels = array(
	    'name'                 => _x('Vídeo', 'post type general name'),
	    'singular_name'        => _x('Vídeo', 'post type singular name'),
	    'add_new'              => _x('Adicionar Novo', 'video'),
	    'add_new_item'         => __('Adicionar Novo Vídeo'),
	    'edit_item'            => __('Editar Vídeo'),
	    'new_item'             => __('Novo Vídeo'),
	    'all_items'            => __('Vídeo'),
	    'view_item'            => __('Vizualizar Vídeo'),
	    'search_items'         => __('Pesquisar por Vídeo'),
	    'not_found'            => __('Nenhum Vídeo encontrado'),
	    'not_found_in_trash'   => __('Nenhum Vídeo encontrado na lixeira'),
	    'parent_item_colon'    => '',
	    'menu_name'            => 'Vídeo'
    );
    
    /**
     * Registamos o tipo de post ´noticia através desta função
     * passando-lhe os labels e parâmetros de controlo.
     */
    register_post_type( 'video', array(
	    'labels'               => $labels,
	    'public'               => true,
	    'publicly_queryable'   => true,
	    'show_ui'              => true,
	    'show_in_menu'         => true,
	    'has_archive'          => 'video',
	    'rewrite'              => array('slug' 
	    	                   => 'video','with_front' 
	    	                   => false,),
	    'capability_type'      => 'post',
	    'has_archive'          => true,
	    'hierarchical'         => false,
	    'menu_position'        => 5,
	    'supports'             => array('title','editor','author','thumbnail','excerpt','comments','post-formats')
	    )
    );
    
    /**
     * Registamos a categoria de noticia para o tipo de post noticia
     */
    register_taxonomy( 'video_category', array( 'video' ), array(
        'hierarchical'         => true,
        'label'                => __( 'Vídeo Category' ),
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
                               => 'video/categories','with_front' 
                               => false,),
        )
    );
    
    /** 
     * Esta função associa tipos de categorias com tipos de posts.
     * Aqui associamos as tags ao tipo de post noticia.
     */
    register_taxonomy_for_object_type( 'tags', 'video' );
    
}
