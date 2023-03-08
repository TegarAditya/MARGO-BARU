<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Marketing Area
    Route::delete('marketing-areas/destroy', 'MarketingAreaController@massDestroy')->name('marketing-areas.massDestroy');
    Route::post('marketing-areas/parse-csv-import', 'MarketingAreaController@parseCsvImport')->name('marketing-areas.parseCsvImport');
    Route::post('marketing-areas/process-csv-import', 'MarketingAreaController@processCsvImport')->name('marketing-areas.processCsvImport');
    Route::resource('marketing-areas', 'MarketingAreaController');

    // Salesperson
    Route::delete('salespeople/destroy', 'SalespersonController@massDestroy')->name('salespeople.massDestroy');
    Route::post('salespeople/parse-csv-import', 'SalespersonController@parseCsvImport')->name('salespeople.parseCsvImport');
    Route::post('salespeople/process-csv-import', 'SalespersonController@processCsvImport')->name('salespeople.processCsvImport');
    Route::resource('salespeople', 'SalespersonController');

    // Address
    Route::delete('addresses/destroy', 'AddressController@massDestroy')->name('addresses.massDestroy');
    Route::post('addresses/parse-csv-import', 'AddressController@parseCsvImport')->name('addresses.parseCsvImport');
    Route::post('addresses/process-csv-import', 'AddressController@processCsvImport')->name('addresses.processCsvImport');
    Route::resource('addresses', 'AddressController');

    // Semester
    Route::delete('semesters/destroy', 'SemesterController@massDestroy')->name('semesters.massDestroy');
    Route::post('semesters/parse-csv-import', 'SemesterController@parseCsvImport')->name('semesters.parseCsvImport');
    Route::post('semesters/process-csv-import', 'SemesterController@processCsvImport')->name('semesters.processCsvImport');
    Route::resource('semesters', 'SemesterController');

    // Cover
    Route::delete('covers/destroy', 'CoverController@massDestroy')->name('covers.massDestroy');
    Route::post('covers/parse-csv-import', 'CoverController@parseCsvImport')->name('covers.parseCsvImport');
    Route::post('covers/process-csv-import', 'CoverController@processCsvImport')->name('covers.processCsvImport');
    Route::resource('covers', 'CoverController');

    // Jenjang
    Route::delete('jenjangs/destroy', 'JenjangController@massDestroy')->name('jenjangs.massDestroy');
    Route::post('jenjangs/parse-csv-import', 'JenjangController@parseCsvImport')->name('jenjangs.parseCsvImport');
    Route::post('jenjangs/process-csv-import', 'JenjangController@processCsvImport')->name('jenjangs.processCsvImport');
    Route::resource('jenjangs', 'JenjangController');

    // Kurikulum
    Route::delete('kurikulums/destroy', 'KurikulumController@massDestroy')->name('kurikulums.massDestroy');
    Route::post('kurikulums/parse-csv-import', 'KurikulumController@parseCsvImport')->name('kurikulums.parseCsvImport');
    Route::post('kurikulums/process-csv-import', 'KurikulumController@processCsvImport')->name('kurikulums.processCsvImport');
    Route::resource('kurikulums', 'KurikulumController');

    // Mapel
    Route::delete('mapels/destroy', 'MapelController@massDestroy')->name('mapels.massDestroy');
    Route::post('mapels/parse-csv-import', 'MapelController@parseCsvImport')->name('mapels.parseCsvImport');
    Route::post('mapels/process-csv-import', 'MapelController@processCsvImport')->name('mapels.processCsvImport');
    Route::resource('mapels', 'MapelController');

    // Kelas
    Route::delete('kelas/destroy', 'KelasController@massDestroy')->name('kelas.massDestroy');
    Route::post('kelas/parse-csv-import', 'KelasController@parseCsvImport')->name('kelas.parseCsvImport');
    Route::post('kelas/process-csv-import', 'KelasController@processCsvImport')->name('kelas.processCsvImport');
    Route::resource('kelas', 'KelasController');

    // Halaman
    Route::delete('halamen/destroy', 'HalamanController@massDestroy')->name('halamen.massDestroy');
    Route::post('halamen/parse-csv-import', 'HalamanController@parseCsvImport')->name('halamen.parseCsvImport');
    Route::post('halamen/process-csv-import', 'HalamanController@processCsvImport')->name('halamen.processCsvImport');
    Route::resource('halamen', 'HalamanController');

    // Unit
    Route::delete('units/destroy', 'UnitController@massDestroy')->name('units.massDestroy');
    Route::post('units/parse-csv-import', 'UnitController@parseCsvImport')->name('units.parseCsvImport');
    Route::post('units/process-csv-import', 'UnitController@processCsvImport')->name('units.processCsvImport');
    Route::resource('units', 'UnitController');

    // Book
    Route::delete('books/destroy', 'BookController@massDestroy')->name('books.massDestroy');
    Route::post('books/media', 'BookController@storeMedia')->name('books.storeMedia');
    Route::post('books/ckmedia', 'BookController@storeCKEditorImages')->name('books.storeCKEditorImages');
    Route::resource('books', 'BookController');

    // Book Variant
    Route::delete('book-variants/destroy', 'BookVariantController@massDestroy')->name('book-variants.massDestroy');
    Route::resource('book-variants', 'BookVariantController');

    // Warehouse
    Route::delete('warehouses/destroy', 'WarehouseController@massDestroy')->name('warehouses.massDestroy');
    Route::post('warehouses/parse-csv-import', 'WarehouseController@parseCsvImport')->name('warehouses.parseCsvImport');
    Route::post('warehouses/process-csv-import', 'WarehouseController@processCsvImport')->name('warehouses.processCsvImport');
    Route::resource('warehouses', 'WarehouseController');

    // Stock Movement
    Route::delete('stock-movements/destroy', 'StockMovementController@massDestroy')->name('stock-movements.massDestroy');
    Route::resource('stock-movements', 'StockMovementController');

    // Stock Opname
    Route::delete('stock-opnames/destroy', 'StockOpnameController@massDestroy')->name('stock-opnames.massDestroy');
    Route::resource('stock-opnames', 'StockOpnameController');

    // Stock Adjustment
    Route::delete('stock-adjustments/destroy', 'StockAdjustmentController@massDestroy')->name('stock-adjustments.massDestroy');
    Route::resource('stock-adjustments', 'StockAdjustmentController');

    // Materials
    Route::delete('materials/destroy', 'MaterialsController@massDestroy')->name('materials.massDestroy');
    Route::post('materials/parse-csv-import', 'MaterialsController@parseCsvImport')->name('materials.parseCsvImport');
    Route::post('materials/process-csv-import', 'MaterialsController@processCsvImport')->name('materials.processCsvImport');
    Route::resource('materials', 'MaterialsController');

    // Stock Adjustment Detail
    Route::delete('stock-adjustment-details/destroy', 'StockAdjustmentDetailController@massDestroy')->name('stock-adjustment-details.massDestroy');
    Route::resource('stock-adjustment-details', 'StockAdjustmentDetailController');

    // Stock Saldo
    Route::delete('stock-saldos/destroy', 'StockSaldoController@massDestroy')->name('stock-saldos.massDestroy');
    Route::resource('stock-saldos', 'StockSaldoController');

    // Estimasi Saldo
    Route::delete('estimasi-saldos/destroy', 'EstimasiSaldoController@massDestroy')->name('estimasi-saldos.massDestroy');
    Route::resource('estimasi-saldos', 'EstimasiSaldoController');

    // Sales Order
    Route::delete('sales-orders/destroy', 'SalesOrderController@massDestroy')->name('sales-orders.massDestroy');
    Route::resource('sales-orders', 'SalesOrderController');

    // Delivery Order
    Route::delete('delivery-orders/destroy', 'DeliveryOrderController@massDestroy')->name('delivery-orders.massDestroy');
    Route::resource('delivery-orders', 'DeliveryOrderController');

    // Delivery Order Item
    Route::delete('delivery-order-items/destroy', 'DeliveryOrderItemController@massDestroy')->name('delivery-order-items.massDestroy');
    Route::resource('delivery-order-items', 'DeliveryOrderItemController');

    // Invoice
    Route::delete('invoices/destroy', 'InvoiceController@massDestroy')->name('invoices.massDestroy');
    Route::resource('invoices', 'InvoiceController');

    // Invoice Item
    Route::delete('invoice-items/destroy', 'InvoiceItemController@massDestroy')->name('invoice-items.massDestroy');
    Route::resource('invoice-items', 'InvoiceItemController');

    // Return Good
    Route::delete('return-goods/destroy', 'ReturnGoodController@massDestroy')->name('return-goods.massDestroy');
    Route::resource('return-goods', 'ReturnGoodController');

    // Return Good Item
    Route::delete('return-good-items/destroy', 'ReturnGoodItemController@massDestroy')->name('return-good-items.massDestroy');
    Route::resource('return-good-items', 'ReturnGoodItemController');

    // Rekap Billing
    Route::delete('rekap-billings/destroy', 'RekapBillingController@massDestroy')->name('rekap-billings.massDestroy');
    Route::resource('rekap-billings', 'RekapBillingController');

    // Payment
    Route::delete('payments/destroy', 'PaymentController@massDestroy')->name('payments.massDestroy');
    Route::resource('payments', 'PaymentController');

    // Transaction
    Route::delete('transactions/destroy', 'TransactionController@massDestroy')->name('transactions.massDestroy');
    Route::resource('transactions', 'TransactionController');

    // Sales Report
    Route::delete('sales-reports/destroy', 'SalesReportController@massDestroy')->name('sales-reports.massDestroy');
    Route::resource('sales-reports', 'SalesReportController');

    // Production Estimation
    Route::delete('production-estimations/destroy', 'ProductionEstimationController@massDestroy')->name('production-estimations.massDestroy');
    Route::resource('production-estimations', 'ProductionEstimationController');

    // Vendor
    Route::delete('vendors/destroy', 'VendorController@massDestroy')->name('vendors.massDestroy');
    Route::post('vendors/parse-csv-import', 'VendorController@parseCsvImport')->name('vendors.parseCsvImport');
    Route::post('vendors/process-csv-import', 'VendorController@processCsvImport')->name('vendors.processCsvImport');
    Route::resource('vendors', 'VendorController');

    // Cetak
    Route::delete('cetaks/destroy', 'CetakController@massDestroy')->name('cetaks.massDestroy');
    Route::resource('cetaks', 'CetakController');

    // Cetak Item
    Route::delete('cetak-items/destroy', 'CetakItemController@massDestroy')->name('cetak-items.massDestroy');
    Route::resource('cetak-items', 'CetakItemController');

    // Finishing
    Route::delete('finishings/destroy', 'FinishingController@massDestroy')->name('finishings.massDestroy');
    Route::resource('finishings', 'FinishingController');

    // Finishing Item
    Route::delete('finishing-items/destroy', 'FinishingItemController@massDestroy')->name('finishing-items.massDestroy');
    Route::resource('finishing-items', 'FinishingItemController');

    Route::get('system-calendar', 'SystemCalendarController@index')->name('systemCalendar');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
