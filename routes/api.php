<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Book
    Route::apiResource('books', 'BookApiController');

    // Book Variant
    Route::apiResource('book-variants', 'BookVariantApiController');

    // Stock Movement
    Route::apiResource('stock-movements', 'StockMovementApiController');

    // Materials
    Route::apiResource('materials', 'MaterialsApiController');
});
