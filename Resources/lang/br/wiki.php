<?php

return [
    'title' => 'Base de Conhecimento',
    'search_provider_desc' => 'Pesquisar artigos da base de conhecimento',
    'knowledge_base' => 'Base de Conhecimento',
    'search_articles' => 'Pesquisar artigos...',
    'table_of_contents' => 'Sumário',
    'last_changes' => 'Últimas alterações',
    'updated' => 'Atualizado',
    'views' => 'visualizações',
    'was_helpful' => 'Este artigo foi útil?',
    'yes' => 'Sim',
    'no' => 'Não',
    'related_articles' => 'Artigos relacionados',
    'article_not_found' => 'Artigo não encontrado',
    'content_in_development' => 'O conteúdo deste artigo está em desenvolvimento',

    'home' => [
        'title' => 'Base de Conhecimento',
        'subtitle' => 'Encontre respostas para perguntas frequentes e guias úteis',
        'search_placeholder' => 'Pesquisar artigos...',
        'articles' => 'artigos',
        'categories' => 'categorias',
        'browse_categories' => 'Categorias',
        'popular' => 'Artigos populares',
        'articles_count' => 'artigos',
    ],

    'empty' => [
        'title' => 'Nenhum artigo',
        'description' => 'Ainda não há artigos na base de conhecimento',
    ],

    'admin' => [
        'menu' => [
            'wiki' => 'Base de Conhecimento',
            'categories' => 'Categorias',
            'articles' => 'Artigos',
        ],

        'title' => [
            'categories' => 'Categorias da Wiki',
            'categories_description' => 'Gerenciar categorias da base de conhecimento',
            'add_category' => 'Adicionar categoria',
            'edit_category' => 'Editar categoria',
            'articles' => 'Artigos da Wiki',
            'articles_description' => 'Gerenciar artigos da base de conhecimento',
            'add_article' => 'Adicionar artigo',
            'edit_article' => 'Editar artigo',
        ],

        'buttons' => [
            'add_category' => 'Adicionar categoria',
            'add_article' => 'Adicionar artigo',
        ],

        'fields' => [
            'name' => 'Nome',
            'name_placeholder' => 'Digite o nome da categoria',
            'slug' => 'URL (slug)',
            'slug_placeholder' => 'nome-da-categoria',
            'slug_help' => 'Identificador único para URL. Use apenas letras, números e hífens.',
            'description' => 'Descrição',
            'description_help' => 'Descrição curta para pré-visualização do artigo',
            'icon' => 'Ícone',
            'icon_help' => 'Classe de ícone Phosphor, ex.: ph.regular.folder',
            'sort_order' => 'Ordem de classificação',
            'active' => 'Ativo',
            'articles_count' => 'Artigos',
            'title' => 'Título',
            'title_placeholder' => 'Digite o título do artigo',
            'category' => 'Categoria',
            'no_category' => 'Sem categoria',
            'tags' => 'Tags',
            'tags_placeholder' => 'tag1, tag2, tag3',
            'tags_help' => 'Digite as tags separadas por vírgula',
            'content' => 'Conteúdo do artigo',
            'published' => 'Publicado',
            'views' => 'Visualizações',
            'updated' => 'Atualizado',
        ],

        'sections' => [
            'category_info' => 'Informações da categoria',
            'article_info' => 'Informações do artigo',
            'article_content' => 'Conteúdo',
            'settings' => 'Configurações',
        ],

        'confirms' => [
            'delete_category' => 'Tem certeza de que deseja excluir esta categoria? Todos os artigos desta categoria também serão excluídos.',
            'delete_article' => 'Tem certeza de que deseja excluir este artigo?',
        ],

        'messages' => [
            'required_fields' => 'Preencha todos os campos obrigatórios',
            'category_not_found' => 'Categoria não encontrada',
            'category_created' => 'Categoria criada com sucesso',
            'category_updated' => 'Categoria atualizada com sucesso',
            'category_deleted' => 'Categoria excluída com sucesso',
            'article_not_found' => 'Artigo não encontrado',
            'article_created' => 'Artigo criado com sucesso',
            'article_updated' => 'Artigo atualizado com sucesso',
            'article_deleted' => 'Artigo excluído com sucesso',
            'slug_exists' => 'Já existe um registro com este slug',
        ],
    ],
];
