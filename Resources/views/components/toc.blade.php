<aside class="wiki-toc">
    <h4 class="wiki-toc__title">
        <x-icon path="ph.regular.list" />
        {{ __('wiki.table_of_contents') }}
    </h4>

    <ul class="wiki-toc__list" id="wikiTocList"></ul>

    <div class="wiki-last-updated">
        <h4 class="wiki-last-updated__title">{{ __('wiki.last_changes') }}</h4>
        @if ($article->author)
            <a href="{{ url('profile/' . $article->author->id) }}"
                class="wiki-update-item"
                data-user-card="{{ $article->author->id }}">
                <img src="{{ asset($article->author->avatar) }}"
                    alt="{{ $article->author->name }}"
                    class="wiki-update-item__avatar">
                <div class="wiki-update-item__info">
                    <span class="wiki-update-item__author">{{ $article->author->name }}</span>
                    <span class="wiki-update-item__date">
                        {{ carbon($article->updatedAt)->format(default_date_format(true)) }}
                    </span>
                </div>
            </a>
        @else
            <div class="wiki-update-item">
                <div class="wiki-update-item__avatar wiki-update-item__avatar--placeholder">AD</div>
                <div class="wiki-update-item__info">
                    <span class="wiki-update-item__author">{{ __('def.user') }}</span>
                    <span class="wiki-update-item__date">
                        {{ carbon($article->updatedAt)->format(default_date_format(true)) }}
                    </span>
                </div>
            </div>
        @endif
    </div>
</aside>

