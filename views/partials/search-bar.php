<form action="<?= url('/search') ?>" method="GET" class="search-bar" role="search">
    <label class="sr-only" for="site-search">Search products</label>
    <input
        type="search"
        id="site-search"
        name="q"
        placeholder="Search for products, brands and more"
        autocomplete="off"
        data-search-input
    >
    <button type="submit" class="search-btn" aria-label="Search">
        <i class="fa-solid fa-magnifying-glass"></i>
    </button>
    <div class="search-suggestions" data-search-suggestions hidden>
        <p class="search-suggestions-title">Popular searches</p>
        <button type="button" data-suggestion="wireless earbuds">wireless earbuds</button>
        <button type="button" data-suggestion="running shoes">running shoes</button>
        <button type="button" data-suggestion="face serum">face serum</button>
        <button type="button" data-suggestion="laptop charger">laptop charger</button>
    </div>
</form>
