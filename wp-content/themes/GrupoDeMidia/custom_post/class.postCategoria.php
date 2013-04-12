<?php 
	class postCategoria{

		function postCategoria(){
			add_action('init', array(&$this, 'register'));
		}

		function register(){
			$this->registerCategorias();
		}

		function registerCategorias(){
			$lables = array(
				'name'              => __('Categoria'),
				'singular_name'     => __('Categoria'),
				'search_items'      => __('Pesquisar Categorias'),
				'popular_items'     => __('Mais usados'),
				'all_items'         => __('Todas as Categorias'),
				'parent_item'       => NULL,
				'parent_item_colon' => NULL,
				'edit_item'         => __('Editar'),
				'update_item'       => __('Atualizar'),
				'add_new_item'      => __('Adicionar nova Categoria'),
				'menu_name'         => __('Categoria')
				);
			$args = array(
				'hierarchical'      => TRUE,
				'labels'            => $labels,
				'singular_label'    => 'Categoria',
				'all_items'         => 'Todas as Categorias',
				'show_ui'           => TRUE,
				'query_var'         => TRUE,
				'rewrite'           => array('slug' => 'categoria')
				);
			register_taxonomy('postCategoria', '', $args);
		}
	}
	$obj = new postCategoria();
?>