document.addEventListener('DOMContentLoaded', function () {
    initWiki();
});

document.addEventListener('htmx:afterSwap', function () {
    initWiki();
});

function initWiki() {
    initCategoryToggles();
    initSearch();
    initKeyboardShortcuts();
    initTableOfContents();
    initFeedbackButtons();
    initCodeBlocks();
}

const WIKI_STORAGE_KEY = 'wiki_expanded_categories';

function getExpandedCategories() {
    try {
        return JSON.parse(localStorage.getItem(WIKI_STORAGE_KEY)) || [];
    } catch {
        return [];
    }
}

function saveExpandedCategories(ids) {
    try {
        localStorage.setItem(WIKI_STORAGE_KEY, JSON.stringify(ids));
    } catch {
        // ignore
    }
}

function initCategoryToggles() {
    const categories = document.querySelectorAll('.wiki-category');
    const expanded = getExpandedCategories();
    
    categories.forEach((category) => {
        const header = category.querySelector('[data-toggle="category"]');
        const list = category.querySelector('.wiki-articles-list');
        const chevron = category.querySelector('.wiki-category__chevron');
        const categoryId = category.dataset.categoryId;
        
        if (!header || !list || !chevron) return;
        
        const hasActiveArticle = category.querySelector('.wiki-article-item--active');
        const shouldBeOpen = expanded.includes(categoryId) || hasActiveArticle;
        
        if (shouldBeOpen) {
            header.classList.add('wiki-category__header--active');
            list.classList.add('wiki-articles-list--open');
            chevron.classList.add('wiki-category__chevron--open');
        } else {
            header.classList.remove('wiki-category__header--active');
            list.classList.remove('wiki-articles-list--open');
            chevron.classList.remove('wiki-category__chevron--open');
        }
        
        if (header.dataset.initialized) return;
        header.dataset.initialized = 'true';
        
        header.addEventListener('click', function () {
            const isOpen = list.classList.contains('wiki-articles-list--open');
            
            this.classList.toggle('wiki-category__header--active');
            list.classList.toggle('wiki-articles-list--open');
            chevron.classList.toggle('wiki-category__chevron--open');
            
            let currentExpanded = getExpandedCategories();
            if (isOpen) {
                currentExpanded = currentExpanded.filter(id => id !== categoryId);
            } else {
                if (!currentExpanded.includes(categoryId)) {
                    currentExpanded.push(categoryId);
                }
            }
            saveExpandedCategories(currentExpanded);
        });
    });
}

function initSearch() {
    const searchInput = document.getElementById('wikiSearchInput');
    const searchResults = document.getElementById('wikiSearchResults');

    if (!searchInput || !searchResults || searchInput.dataset.initialized) return;
    searchInput.dataset.initialized = 'true';

    let debounceTimer;

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();

        clearTimeout(debounceTimer);

        if (query.length < 2) {
            searchResults.classList.remove('wiki-search__results--open');
            searchResults.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/wiki/search?q=${encodeURIComponent(query)}`)
                .then((response) => response.json())
                .then((data) => {
                    if (data.results && data.results.length > 0) {
                        searchResults.innerHTML = data.results
                            .map(
                                (item) => `
                            <a href="${item.url}" class="wiki-search__result-item" hx-boost="true" hx-target="#main" hx-swap="outerHTML transition:true">
                                <svg class="wiki-search__result-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 256 256">
                                    <path fill="currentColor" d="M213.66,82.34l-56-56A8,8,0,0,0,152,24H56A16,16,0,0,0,40,40V216a16,16,0,0,0,16,16H200a16,16,0,0,0,16-16V88A8,8,0,0,0,213.66,82.34ZM160,51.31,188.69,80H160ZM200,216H56V40h88V88a8,8,0,0,0,8,8h48V216Z"/>
                                </svg>
                                <div class="wiki-search__result-content">
                                    <div class="wiki-search__result-title">${escapeHtml(item.title)}</div>
                                    <div class="wiki-search__result-category">${escapeHtml(item.category)}</div>
                                </div>
                            </a>
                        `
                            )
                            .join('');
                        searchResults.classList.add('wiki-search__results--open');
                        htmx.process(searchResults);
                    } else {
                        searchResults.innerHTML = '<div class="wiki-search__no-results">Ничего не найдено</div>';
                        searchResults.classList.add('wiki-search__results--open');
                    }
                })
                .catch((error) => {
                    console.error('Search error:', error);
                });
        }, 250);
    });

    searchInput.addEventListener('focus', function () {
        if (searchResults.innerHTML) {
            searchResults.classList.add('wiki-search__results--open');
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.wiki-search-wrapper') && !e.target.closest('.wiki-landing__search')) {
            searchResults.classList.remove('wiki-search__results--open');
        }
    });
}

function initKeyboardShortcuts() {
    if (document.body.dataset.wikiShortcutsInit) return;
    document.body.dataset.wikiShortcutsInit = 'true';

    document.addEventListener('keydown', function (e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.getElementById('wikiSearchInput');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }

        if (e.key === 'Escape') {
            const searchResults = document.getElementById('wikiSearchResults');
            if (searchResults) {
                searchResults.classList.remove('wiki-search__results--open');
            }
            document.activeElement?.blur();
        }
    });
}

function initTableOfContents() {
    const tocList = document.getElementById('wikiTocList');
    const articleContent = document.querySelector('.wiki-article-content');

    if (!tocList || !articleContent) return;

    const headings = articleContent.querySelectorAll('h2, h3');

    if (headings.length === 0) {
        tocList.innerHTML = '<li class="wiki-toc__item"><span class="wiki-toc__link" style="cursor: default; opacity: 0.5;">Нет разделов</span></li>';
        return;
    }

    tocList.innerHTML = '';

    headings.forEach((heading, index) => {
        const id = heading.textContent
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^\w\u0400-\u04FF-]/g, '');
        heading.id = id || `heading-${index}`;

        const li = document.createElement('li');
        li.className = 'wiki-toc__item';

        const link = document.createElement('span');
        link.className = `wiki-toc__link ${heading.tagName === 'H3' ? 'wiki-toc__link--h3' : ''}`;
        link.textContent = heading.textContent;
        link.title = heading.textContent;
        link.addEventListener('click', () => {
            heading.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        li.appendChild(link);
        tocList.appendChild(li);
    });

    let ticking = false;
    
    function updateActiveSection() {
        if (ticking) return;
        ticking = true;
        
        requestAnimationFrame(() => {
            let current = '';

            headings.forEach((heading) => {
                const rect = heading.getBoundingClientRect();
                if (rect.top <= 120) {
                    current = heading.id;
                }
            });

            tocList.querySelectorAll('.wiki-toc__link').forEach((link, index) => {
                const heading = headings[index];
                if (heading && heading.id === current) {
                    link.classList.add('wiki-toc__link--active');
                } else {
                    link.classList.remove('wiki-toc__link--active');
                }
            });
            
            ticking = false;
        });
    }

    window.addEventListener('scroll', updateActiveSection, { passive: true });
    updateActiveSection();
}

function initFeedbackButtons() {
    const feedbackButtons = document.querySelectorAll('.wiki-feedback__btn');

    feedbackButtons.forEach((btn) => {
        if (btn.dataset.initialized) return;
        btn.dataset.initialized = 'true';
        
        btn.addEventListener('click', function () {
            const slug = this.dataset.articleSlug;
            const isHelpful = this.dataset.helpful === '1';

            feedbackButtons.forEach((b) => b.classList.remove('active'));
            this.classList.add('active');

            fetch(`/wiki/article/${slug}/feedback`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ helpful: isHelpful ? 1 : 0 }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        const helpfulCount = document.getElementById('helpfulCount');
                        const notHelpfulCount = document.getElementById('notHelpfulCount');

                        if (helpfulCount) helpfulCount.textContent = data.helpful;
                        if (notHelpfulCount) notHelpfulCount.textContent = data.not_helpful;
                    }
                })
                .catch((error) => {
                    console.error('Feedback error:', error);
                });
        });
    });
}

function initCodeBlocks() {
    const codeBlocks = document.querySelectorAll('.wiki-article-content pre');

    codeBlocks.forEach((block) => {
        if (block.closest('.wiki-code-wrapper')) return;

        const code = block.querySelector('code');
        if (!code) return;

        const wrapper = document.createElement('div');
        wrapper.className = 'wiki-code-wrapper';

        const header = document.createElement('div');
        header.className = 'wiki-code-header';

        const lang = code.className.match(/language-(\w+)/)?.[1] || 'code';
        header.innerHTML = `
            <span class="wiki-code-lang">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 256 256">
                    <path fill="currentColor" d="M69.12,94.15,28.5,128l40.62,33.85a8,8,0,1,1-10.24,12.29l-48-40a8,8,0,0,1,0-12.29l48-40a8,8,0,0,1,10.24,12.3Zm176,27.7-48-40a8,8,0,1,0-10.24,12.3L227.5,128l-40.62,33.85a8,8,0,1,0,10.24,12.29l48-40a8,8,0,0,0,0-12.29ZM162.73,32.48a8,8,0,0,0-10.25,4.79l-64,176a8,8,0,0,0,4.79,10.26A8.14,8.14,0,0,0,96,224a8,8,0,0,0,7.52-5.27l64-176A8,8,0,0,0,162.73,32.48Z"/>
                </svg>
                ${lang}
            </span>
            <button class="wiki-code-copy" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 256 256">
                    <path fill="currentColor" d="M216,32H88a8,8,0,0,0-8,8V80H40a8,8,0,0,0-8,8V216a8,8,0,0,0,8,8H168a8,8,0,0,0,8-8V176h40a8,8,0,0,0,8-8V40A8,8,0,0,0,216,32ZM160,208H48V96H160Zm48-48H176V88a8,8,0,0,0-8-8H96V48H208Z"/>
                </svg>
                <span>Копировать</span>
            </button>
        `;

        block.parentNode.insertBefore(wrapper, block);
        wrapper.appendChild(header);
        wrapper.appendChild(block);

        const copyBtn = header.querySelector('.wiki-code-copy');
        copyBtn.addEventListener('click', async () => {
            const text = code.textContent;
            await navigator.clipboard.writeText(text);

            copyBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 256 256">
                    <path fill="currentColor" d="M229.66,77.66l-128,128a8,8,0,0,1-11.32,0l-56-56a8,8,0,0,1,11.32-11.32L96,188.69,218.34,66.34a8,8,0,0,1,11.32,11.32Z"/>
                </svg>
                <span>Скопировано</span>
            `;
            copyBtn.classList.add('copied');

            setTimeout(() => {
                copyBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 256 256">
                        <path fill="currentColor" d="M216,32H88a8,8,0,0,0-8,8V80H40a8,8,0,0,0-8,8V216a8,8,0,0,0,8,8H168a8,8,0,0,0,8-8V176h40a8,8,0,0,0,8-8V40A8,8,0,0,0,216,32ZM160,208H48V96H160Zm48-48H176V88a8,8,0,0,0-8-8H96V48H208Z"/>
                    </svg>
                    <span>Копировать</span>
                `;
                copyBtn.classList.remove('copied');
            }, 2000);
        });
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
