<?php

return [
    'title' => 'База знаний',
    'search_provider_desc' => 'Поиск по статьям базы знаний',
    'knowledge_base' => 'База знаний',
    'search_articles' => 'Поиск статей...',
    'table_of_contents' => 'Содержание',
    'last_changes' => 'Последние изменения',
    'updated' => 'Обновлено',
    'views' => 'просмотров',
    'was_helpful' => 'Была ли эта статья полезной?',
    'yes' => 'Да',
    'no' => 'Нет',
    'related_articles' => 'Связанные статьи',
    'article_not_found' => 'Статья не найдена',
    'content_in_development' => 'Содержимое этой статьи находится в разработке',

    'home' => [
        'title' => 'База знаний',
        'subtitle' => 'Найдите ответы на часто задаваемые вопросы и полезные руководства',
        'search_placeholder' => 'Поиск статей...',
        'articles' => 'статей',
        'categories' => 'категорий',
        'browse_categories' => 'Категории',
        'popular' => 'Популярные статьи',
        'articles_count' => 'статей',
    ],

    'empty' => [
        'title' => 'Нет статей',
        'description' => 'В базе знаний пока нет ни одной статьи',
    ],

    'admin' => [
        'menu' => [
            'wiki' => 'База знаний',
            'categories' => 'Категории',
            'articles' => 'Статьи',
        ],

        'title' => [
            'categories' => 'Категории Wiki',
            'categories_description' => 'Управление категориями базы знаний',
            'add_category' => 'Добавить категорию',
            'edit_category' => 'Редактировать категорию',
            'articles' => 'Статьи Wiki',
            'articles_description' => 'Управление статьями базы знаний',
            'add_article' => 'Добавить статью',
            'edit_article' => 'Редактировать статью',
        ],

        'buttons' => [
            'add_category' => 'Добавить категорию',
            'add_article' => 'Добавить статью',
        ],

        'fields' => [
            'name' => 'Название',
            'name_placeholder' => 'Введите название категории',
            'slug' => 'URL (slug)',
            'slug_placeholder' => 'nazvanie-kategorii',
            'slug_help' => 'Уникальный идентификатор для URL. Используйте только латиницу, цифры и дефис.',
            'description' => 'Описание',
            'description_help' => 'Краткое описание статьи для предпросмотра',
            'icon' => 'Иконка',
            'icon_help' => 'Класс иконки Phosphor, например: ph.regular.folder',
            'sort_order' => 'Порядок сортировки',
            'active' => 'Активна',
            'articles_count' => 'Статей',
            'title' => 'Заголовок',
            'title_placeholder' => 'Введите заголовок статьи',
            'category' => 'Категория',
            'no_category' => 'Без категории',
            'tags' => 'Теги',
            'tags_placeholder' => 'тег1, тег2, тег3',
            'tags_help' => 'Введите теги через запятую',
            'content' => 'Содержимое статьи',
            'published' => 'Опубликована',
            'views' => 'Просмотры',
            'updated' => 'Обновлено',
        ],

        'sections' => [
            'category_info' => 'Информация о категории',
            'article_info' => 'Информация о статье',
            'article_content' => 'Содержимое',
            'settings' => 'Настройки',
        ],

        'confirms' => [
            'delete_category' => 'Вы уверены, что хотите удалить эту категорию? Все статьи в этой категории также будут удалены.',
            'delete_article' => 'Вы уверены, что хотите удалить эту статью?',
        ],

        'messages' => [
            'required_fields' => 'Заполните все обязательные поля',
            'category_not_found' => 'Категория не найдена',
            'category_created' => 'Категория успешно создана',
            'category_updated' => 'Категория успешно обновлена',
            'category_deleted' => 'Категория успешно удалена',
            'article_not_found' => 'Статья не найдена',
            'article_created' => 'Статья успешно создана',
            'article_updated' => 'Статья успешно обновлена',
            'article_deleted' => 'Статья успешно удалена',
            'slug_exists' => 'Запись с таким slug уже существует',
        ],
    ],
];
