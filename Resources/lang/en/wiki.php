<?php

return [
    'title' => 'Knowledge Base',
    'search_provider_desc' => 'Search knowledge base articles',
    'knowledge_base' => 'Knowledge Base',
    'search_articles' => 'Search articles...',
    'table_of_contents' => 'Table of Contents',
    'last_changes' => 'Last Changes',
    'updated' => 'Updated',
    'views' => 'views',
    'was_helpful' => 'Was this article helpful?',
    'yes' => 'Yes',
    'no' => 'No',
    'related_articles' => 'Related Articles',
    'article_not_found' => 'Article not found',
    'content_in_development' => 'This article content is in development',

    'home' => [
        'title' => 'Knowledge Base',
        'subtitle' => 'Find answers to frequently asked questions and helpful guides',
        'search_placeholder' => 'Search articles...',
        'articles' => 'articles',
        'categories' => 'categories',
        'browse_categories' => 'Categories',
        'popular' => 'Popular Articles',
        'articles_count' => 'articles',
    ],

    'empty' => [
        'title' => 'No articles',
        'description' => 'There are no articles in the knowledge base yet',
    ],

    'admin' => [
        'menu' => [
            'wiki' => 'Knowledge Base',
            'categories' => 'Categories',
            'articles' => 'Articles',
        ],

        'title' => [
            'categories' => 'Wiki Categories',
            'categories_description' => 'Manage knowledge base categories',
            'add_category' => 'Add Category',
            'edit_category' => 'Edit Category',
            'articles' => 'Wiki Articles',
            'articles_description' => 'Manage knowledge base articles',
            'add_article' => 'Add Article',
            'edit_article' => 'Edit Article',
        ],

        'buttons' => [
            'add_category' => 'Add Category',
            'add_article' => 'Add Article',
        ],

        'fields' => [
            'name' => 'Name',
            'name_placeholder' => 'Enter category name',
            'slug' => 'URL (slug)',
            'slug_placeholder' => 'category-name',
            'slug_help' => 'Unique identifier for URL. Use only letters, numbers, and hyphens.',
            'description' => 'Description',
            'description_help' => 'Short description for article preview',
            'icon' => 'Icon',
            'icon_help' => 'Phosphor icon class, e.g.: ph.regular.folder',
            'sort_order' => 'Sort Order',
            'active' => 'Active',
            'articles_count' => 'Articles',
            'title' => 'Title',
            'title_placeholder' => 'Enter article title',
            'category' => 'Category',
            'no_category' => 'No category',
            'tags' => 'Tags',
            'tags_placeholder' => 'tag1, tag2, tag3',
            'tags_help' => 'Enter tags separated by commas',
            'content' => 'Article Content',
            'published' => 'Published',
            'views' => 'Views',
            'updated' => 'Updated',
        ],

        'sections' => [
            'category_info' => 'Category Information',
            'article_info' => 'Article Information',
            'article_content' => 'Content',
            'settings' => 'Settings',
        ],

        'confirms' => [
            'delete_category' => 'Are you sure you want to delete this category? All articles in this category will also be deleted.',
            'delete_article' => 'Are you sure you want to delete this article?',
        ],

        'messages' => [
            'required_fields' => 'Fill in all required fields',
            'category_not_found' => 'Category not found',
            'category_created' => 'Category successfully created',
            'category_updated' => 'Category successfully updated',
            'category_deleted' => 'Category successfully deleted',
            'article_not_found' => 'Article not found',
            'article_created' => 'Article successfully created',
            'article_updated' => 'Article successfully updated',
            'article_deleted' => 'Article successfully deleted',
            'slug_exists' => 'A record with this slug already exists',
        ],
    ],
];
