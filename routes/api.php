<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Marketing Area
    Route::apiResource('marketing-areas', 'MarketingAreaApiController');

    // Book
    Route::post('books/media', 'BookApiController@storeMedia')->name('books.storeMedia');
    Route::apiResource('books', 'BookApiController');

    // Book Variant
    Route::apiResource('book-variants', 'BookVariantApiController');

    // Stock Movement
    Route::apiResource('stock-movements', 'StockMovementApiController');

    // Materials
    Route::apiResource('materials', 'MaterialsApiController');

    // Sales Order
    Route::apiResource('sales-orders', 'SalesOrderApiController');

    // Delivery Order
    Route::apiResource('delivery-orders', 'DeliveryOrderApiController');

    // Payment
    Route::apiResource('payments', 'PaymentApiController');

    // Vendor
    Route::apiResource('vendors', 'VendorApiController');
});
