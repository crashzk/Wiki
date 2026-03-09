<aside class="wiki-sidebar">
    <div class="wiki-sidebar__header">
        <a href="{{ route('wiki.index') }}" class="wiki-sidebar__title" hx-boost="true" hx-target="#main" hx-swap="outerHTML transition:true">
            <x-icon path="ph.regular.book-open" />
            {{ __('wiki.knowledge_base') }}
        </a>

        <div class="wiki-search-wrapper">
            <x-icon path="ph.regular.magnifying-glass" class="wiki-search__icon" />
            <input type="text" class="wiki-search__input" id="wikiSearchInput"
                placeholder="{{ __('wiki.search_articles') }}" autocomplete="off">
            <div class="wiki-search__results" id="wikiSearchResults"></div>
        </div>
    </div>

    <nav class="wiki-categories">
        @foreach ($categories as $category)
            @php
                $articles = $category->getActiveArticles();
                $isExpanded =
                    $selectedArticle &&
                    $selectedArticle->category &&
                    $selectedArticle->category->id === $category->id;
            @endphp
            <div class="wiki-category" data-category-id="{{ $category->id }}">
                <div class="wiki-category__header {{ $isExpanded ? 'wiki-category__header--active' : '' }}"
                    data-toggle="category">
                    <span class="wiki-category__title">
                        @if ($category->icon)
                            <x-icon path="{{ $category->icon }}" class="wiki-category__icon" />
                        @else
                            <x-icon path="ph.regular.folder" class="wiki-category__icon" />
                        @endif
                        <span>{{ __($category->name) }}</span>
                    </span>
                    <div class="wiki-category__meta">
                        <span class="wiki-category__count">{{ count($articles) }}</span>
                        <x-icon path="ph.regular.caret-down"
                            class="wiki-category__chevron {{ $isExpanded ? 'wiki-category__chevron--open' : '' }}" />
                    </div>
                </div>

                <div class="wiki-articles-list {{ $isExpanded ? 'wiki-articles-list--open' : '' }}">
                    <div class="wiki-articles-list__inner">
                        @foreach ($articles as $article)
                            <a href="{{ route('wiki.article', ['slug' => $article->slug]) }}"
                                class="wiki-article-item {{ $selectedArticle && $selectedArticle->id === $article->id ? 'wiki-article-item--active' : '' }}"
                                hx-boost="true" hx-target="#main" hx-swap="outerHTML transition:true">
                                <span class="wiki-article-item__text">{{ __($article->title) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </nav>
</aside>

