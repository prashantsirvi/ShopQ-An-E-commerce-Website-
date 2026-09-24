<?php

declare(strict_types=1);

function cart_service(): Models\Cart
{
    static $cart = null;
    $cart ??= new Models\Cart();

    return $cart;
}

function cart_count(): int
{
    return cart_service()->count();
}

/** @return array<int, int> */
function cart_product_ids(): array
{
    $ids = [];

    foreach (cart_service()->items() as $item) {
        $ids[] = (int) $item['id'];
    }

    return array_values(array_unique($ids));
}

function cart_has_product(int $productId): bool
{
    return in_array($productId, cart_product_ids(), true);
}

function wishlist_service(): Models\Wishlist
{
    static $wishlist = null;
    $wishlist ??= new Models\Wishlist();

    return $wishlist;
}

function wishlist_count(): int
{
    return wishlist_service()->count();
}

function compare_service(): Models\CompareList
{
    static $compare = null;
    $compare ??= new Models\CompareList();

    return $compare;
}

function compare_count(): int
{
    return compare_service()->count();
}

function merge_guest_commerce(int $userId): void
{
    (new Models\Cart())->mergeSessionToUser($userId);
    (new Models\Wishlist())->mergeSessionToUser($userId);
    (new Models\CompareList())->mergeSessionToUser($userId);
}
