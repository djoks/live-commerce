<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cart Time-To-Live
    |--------------------------------------------------------------------------
    |
    | This value determines how long a cart remains active before it expires.
    | When a cart expires, its items are automatically moved to the user's
    | wishlist for later retrieval. Set to 0 to disable expiration.
    |
    */

    'ttl_minutes' => (int) env('CART_TTL_MINUTES', 60),

];
