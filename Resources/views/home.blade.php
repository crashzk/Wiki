@extends('flute::layouts.app')

@section('title', __('wiki.title'))

@push('head')
    @at('Modules/Wiki/Resources/assets/scss/wiki.scss')
    @at('Modules/Wiki/Resources/assets/js/wiki.js')
@endpush

@push('content')
    <div class="wiki-landing">
        <header class="wiki-landing__hero">
            <div class="wiki-landing__hero-content">
                <h1 class="wiki-landing__title">{{ __('wiki.home.title') }}</h1>
                <p class="wiki-landing__subtitle">{{ __('wiki.home.subtitle') }}</p>
                
                <div class="wiki-landing__search">
                    <div class="wiki-landing__search-box">
                        <x-icon path="ph.regular.magnifying-glass" class="wiki-landing__search-icon" />
                        <input type="text" 
                            class="wiki-landing__search-input" 
                            id="wikiSearchInput"
                            placeholder="{{ __('wiki.home.search_placeholder') }}" 
                            autocomplete="off">
                        <div class="wiki-landing__search-hint">
                            <kbd>⌘</kbd><kbd>K</kbd>
                        </div>
                    </div>
                    <div class="wiki-search__results wiki-landing__search-results" id="wikiSearchResults"></div>
                </div>
            </div>
        </header>

        <div class="container">
            @if (!empty($categories))
                @foreach ($categories as $category)
                    @php
                        $categoryArticles = $category->getActiveArticles();
                    @endphp
                    @if (count($categoryArticles) > 0)
                        <section class="wiki-category-section">
                            <header class="wiki-category-section__header">
                                <div class="wiki-category-section__icon">
                                    @if ($category->icon)
                                        <x-icon path="{{ $category->icon }}" />
                                    @else
                                        <x-icon path="ph.regular.folder" />
                                    @endif
                                </div>
                                <div class="wiki-category-section__info">
                                    <h2 class="wiki-category-section__title">{{ __($category->name) }}</h2>
                                    @if ($category->description)
                                        <p class="wiki-category-section__desc">{{ __($category->description) }}</p>
                                    @endif
                                </div>
                                <span class="wiki-category-section__count">{{ count($categoryArticles) }}</span>
                            </header>
                            
                            <div class="wiki-category-section__articles">
                                @foreach ($categoryArticles as $article)
                                    <a href="{{ route('wiki.article', ['slug' => $article->slug]) }}"
                                        class="wiki-article-row" hx-boost="true" hx-target="#main"
                                        hx-swap="outerHTML transition:true">
                                        <div class="wiki-article-row__content">
                                            <h3 class="wiki-article-row__title">{{ __($article->title) }}</h3>
                                            @if ($article->description)
                                                <p class="wiki-article-row__desc">{{ \Illuminate\Support\Str::limit(__($article->description), 100) }}</p>
                                            @endif
                                        </div>
                                        <x-icon path="ph.regular.arrow-right" class="wiki-article-row__arrow" />
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            @endif

            @if (empty($categories))
                <div class="wiki-landing__empty">
                    <x-icon path="ph.regular.book-open" />
                    <h3>{{ __('wiki.empty.title') }}</h3>
                    <p>{{ __('wiki.empty.description') }}</p>
                </div>
            @endif
        </div>
    </div>
@endpush
