@extends('flute::layouts.app')

@section('title', __($article->title))

@push('head')
    @at('Modules/Wiki/Resources/assets/scss/wiki.scss')
    @at('Modules/Wiki/Resources/assets/js/wiki.js')
@endpush

@push('content')
    <div class="container">
        <div class="wiki-page">
            @include('wiki::components.sidebar', ['categories' => $categories, 'selectedArticle' => $article])

            <div class="wiki-main">
                <nav class="wiki-breadcrumbs">
                    <a href="{{ route('wiki.index') }}" class="wiki-breadcrumb__link" hx-boost="true" hx-target="#main"
                        hx-swap="outerHTML transition:true">
                        <x-icon path="ph.regular.house" />
                    </a>
                    @if ($article->category)
                        <x-icon path="ph.regular.caret-right" class="wiki-breadcrumb__separator" />
                        <span class="wiki-breadcrumb__link">{{ __($article->category->name) }}</span>
                    @endif
                    <x-icon path="ph.regular.caret-right" class="wiki-breadcrumb__separator" />
                    <span class="wiki-breadcrumb__current">{{ __($article->title) }}</span>
                </nav>

                <div class="wiki-content-wrapper">
                    <div class="wiki-content">
                        <header class="wiki-article-header">
                            @if ($article->category)
                                <div class="wiki-article__meta">
                                    <span class="wiki-article__category">
                                        @if ($article->category->icon)
                                            <x-icon path="{{ $article->category->icon }}" />
                                        @else
                                            <x-icon path="ph.regular.folder" />
                                        @endif
                                        {{ __($article->category->name) }}
                                    </span>
                                </div>
                            @endif

                            <h1 class="wiki-article__title">{{ __($article->title) }}</h1>

                            @if ($article->description)
                                <p class="wiki-article__description">{{ __($article->description) }}</p>
                            @endif

                            <div class="wiki-article__info">
                                @if ($article->author)
                                    <a href="{{ url('profile/' . $article->author->id) }}"
                                        class="wiki-article__author"
                                        data-user-card="{{ $article->author->id }}">
                                        <img src="{{ asset($article->author->avatar) }}"
                                            alt="{{ $article->author->name }}"
                                            class="wiki-article__author-avatar">
                                        <span class="wiki-article__author-name">{{ $article->author->name }}</span>
                                    </a>
                                @endif
                                <span class="wiki-article__info-item">
                                    <x-icon path="ph.regular.clock" />
                                    {{ __('wiki.updated') }}
                                    {{ carbon($article->updatedAt)->translatedFormat('d F Y') }}
                                </span>
                                <span class="wiki-article__info-item">
                                    <x-icon path="ph.regular.eye" />
                                    {{ number_format($article->views) }} {{ __('wiki.views') }}
                                </span>
                            </div>

                            @if (!empty($article->getTagsArray()))
                                <div class="wiki-tags">
                                    @foreach ($article->getTagsArray() as $tag)
                                        <span class="wiki-tag">
                                            <x-icon path="ph.regular.hash" />
                                            {{ __($tag) }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </header>

                        <article class="wiki-article-content md-content">
                            @if ($article->content)
                                {!! markdown()->parse($article->content) !!}
                            @else
                                <div class="wiki-article-empty">
                                    <x-icon path="ph.regular.file-text" />
                                    <p>{{ __('wiki.content_in_development') }}</p>
                                </div>
                            @endif
                        </article>

                        <footer class="wiki-article-footer">
                            <div class="wiki-feedback">
                                <span class="wiki-feedback__question">{{ __('wiki.was_helpful') }}</span>
                                <div class="wiki-feedback__buttons">
                                    <button type="button" class="wiki-feedback__btn wiki-feedback__btn--positive"
                                        data-article-slug="{{ $article->slug }}" data-helpful="1">
                                        <x-icon path="ph.regular.thumbs-up" />
                                        {{ __('wiki.yes') }}
                                        <span class="wiki-feedback__count" id="helpfulCount">{{ $article->helpful }}</span>
                                    </button>
                                    <button type="button" class="wiki-feedback__btn wiki-feedback__btn--negative"
                                        data-article-slug="{{ $article->slug }}" data-helpful="0">
                                        <x-icon path="ph.regular.thumbs-down" />
                                        {{ __('wiki.no') }}
                                        <span class="wiki-feedback__count" id="notHelpfulCount">{{ $article->not_helpful }}</span>
                                    </button>
                                </div>
                            </div>

                            @if (!empty($relatedArticles))
                                <section class="wiki-related">
                                    <h3 class="wiki-related__title">{{ __('wiki.related_articles') }}</h3>
                                    <div class="wiki-related__grid">
                                        @foreach ($relatedArticles as $related)
                                            <a href="{{ route('wiki.article', ['slug' => $related->slug]) }}"
                                                class="wiki-related__card" hx-boost="true" hx-target="#main"
                                                hx-swap="outerHTML transition:true">
                                                @if ($related->category)
                                                    <span class="wiki-related__category">{{ __($related->category->name) }}</span>
                                                @endif
                                                <h4 class="wiki-related__card-title">{{ __($related->title) }}</h4>
                                                <p class="wiki-related__card-desc">
                                                    {{ \Illuminate\Support\Str::limit($related->description, 100) }}
                                                </p>
                                            </a>
                                        @endforeach
                                    </div>
                                </section>
                            @endif
                        </footer>
                    </div>

                    @if ($article->content)
                        @include('wiki::components.toc', ['article' => $article])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endpush

