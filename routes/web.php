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
    Route::get('/god-route', 'HomeController@god')->name('god');
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

    // Group Area
    Route::delete('group-areas/destroy', 'GroupAreaController@massDestroy')->name('group-areas.massDestroy');
    Route::post('group-areas/parse-csv-import', 'GroupAreaController@parseCsvImport')->name('group-areas.parseCsvImport');
    Route::post('group-areas/process-csv-import', 'GroupAreaController@processCsvImport')->name('group-areas.processCsvImport');
    Route::resource('group-areas', 'GroupAreaController');

    // Marketing Area
    Route::delete('marketing-areas/destroy', 'MarketingAreaController@massDestroy')->name('marketing-areas.massDestroy');
    Route::post('marketing-areas/parse-csv-import', 'MarketingAreaController@parseCsvImport')->name('marketing-areas.parseCsvImport');
    Route::post('marketing-areas/process-csv-import', 'MarketingAreaController@processCsvImport')->name('marketing-areas.processCsvImport');
    Route::resource('marketing-areas', 'MarketingAreaController');

    // Salesperson
    Route::delete('salespeople/destroy', 'SalespersonController@massDestroy')->name('salespeople.massDestroy');
    Route::post('salespeople/parse-csv-import', 'SalespersonController@parseCsvImport')->name('salespeople.parseCsvImport');
    Route::post('salespeople/process-csv-import', 'SalespersonController@processCsvImport')->name('salespeople.processCsvImport');
    Route::post('salespeople/import', 'SalespersonController@import')->name('salespeople.import');
    Route::get('salespeople/template-import', 'SalespersonController@template_import')->name('salespeople.templateImport');
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

    // Isi
    Route::delete('isis/destroy', 'IsiController@massDestroy')->name('isis.massDestroy');
    Route::post('isis/parse-csv-import', 'IsiController@parseCsvImport')->name('isis.parseCsvImport');
    Route::post('isis/process-csv-import', 'IsiController@processCsvImport')->name('isis.processCsvImport');
    Route::resource('isis', 'IsiController');

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
    Route::delete('halaman/destroy', 'HalamanController@massDestroy')->name('halaman.massDestroy');
    Route::post('halaman/import', 'HalamanController@import')->name('halaman.import');
    Route::resource('halaman', 'HalamanController');

    // Unit
    Route::delete('units/destroy', 'UnitController@massDestroy')->name('units.massDestroy');
    Route::post('units/parse-csv-import', 'UnitController@parseCsvImport')->name('units.parseCsvImport');
    Route::post('units/process-csv-import', 'UnitController@processCsvImport')->name('units.processCsvImport');
    Route::resource('units', 'UnitController');

    // Book
    Route::delete('books/destroy', 'BookController@massDestroy')->name('books.massDestroy');
    Route::post('books/media', 'BookController@storeMedia')->name('books.storeMedia');
    Route::post('books/ckmedia', 'BookController@storeCKEditorImages')->name('books.storeCKEditorImages');
    Route::post('books/import', 'BookController@import')->name('books.import');
    Route::get('books/template-import', 'BookController@template_import')->name('books.templateImport');
    Route::resource('books', 'BookController');

    // Book Variant
    Route::delete('book-variants/destroy', 'BookVariantController@massDestroy')->name('book-variants.massDestroy');
    Route::post('book-variants/media', 'BookVariantController@storeMedia')->name('book-variants.storeMedia');
    Route::post('book-variants/ckmedia', 'BookVariantController@storeCKEditorImages')->name('book-variants.storeCKEditorImages');
    Route::post('book-variants/update-price', 'BookVariantController@updatePrice')->name('book-variants.updatePrice');
    Route::get('book-variants/get-products', 'BookVariantController@getProducts')->name('book-variants.getProducts');
    Route::get('book-variants/get-books', 'BookVariantController@getBooks')->name('book-variants.getBooks');
    Route::get('book-variants/get-book', 'BookVariantController@getBook')->name('book-variants.getBook');
    Route::get('book-variants/get-pg', 'BookVariantController@getPg')->name('book-variants.getPg');
    Route::get('book-variants/get-pg-delivery', 'BookVariantController@getPgDelivery')->name('book-variants.getPgDelivery');
    Route::get('book-variants/get-estimasi', 'BookVariantController@getEstimasi')->name('book-variants.getEstimasi');
    Route::get('book-variants/get-info-estimasi', 'BookVariantController@getInfoEstimasi')->name('book-variants.getInfoEstimasi');
    Route::get('book-variants/get-delivery', 'BookVariantController@getDelivery')->name('book-variants.getDelivery');
    Route::get('book-variants/get-info-delivery', 'BookVariantController@getInfoDelivery')->name('book-variants.getInfoDelivery');
    Route::get('book-variants/get-retur', 'BookVariantController@getRetur')->name('book-variants.getRetur');
    Route::get('book-variants/get-info-retur', 'BookVariantController@getInfoRetur')->name('book-variants.getInfoRetur');
    Route::get('book-variants/get-edit-retur', 'BookVariantController@getEditRetur')->name('book-variants.getEditRetur');
    Route::get('book-variants/get-info-edit-retur', 'BookVariantController@getInfoEditRetur')->name('book-variants.getInfoEditRetur');
    Route::get('book-variants/get-adjustment', 'BookVariantController@getAdjustment')->name('book-variants.getAdjustment');
    Route::get('book-variants/get-info-adjustment', 'BookVariantController@getInfoAdjustment')->name('book-variants.getInfoAdjustment');
    Route::get('book-variants/get-cetak', 'BookVariantController@getCetak')->name('book-variants.getCetak');
    Route::get('book-variants/get-info-cetak', 'BookVariantController@getInfoCetak')->name('book-variants.getInfoCetak');
    Route::get('book-variants/get-info-finishing', 'BookVariantController@getInfoFinishing')->name('book-variants.getInfoFinishing');
    Route::get('book-variants/get-list-finishing', 'BookVariantController@getListFinishing')->name('book-variants.getListFinishing');
    Route::get('book-variants/get-list-finishing-info', 'BookVariantController@getInfoFinishingDetail')->name('book-variants.getListFinishingInfo');
    Route::resource('book-variants', 'BookVariantController');

    // Book Component
    Route::delete('book-components/destroy', 'BookComponentController@massDestroy')->name('book-components.massDestroy');
    Route::resource('book-components', 'BookComponentController');

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
    Route::get('stock-opnames/summary', 'StockOpnameController@summary')->name('stock-opnames.summary');
    Route::post('stock-opnames/export', 'StockOpnameController@export')->name('stock-opnames.export');
    Route::resource('stock-opnames', 'StockOpnameController');

    // Stock Adjustment
    Route::delete('stock-adjustments/destroy', 'StockAdjustmentController@massDestroy')->name('stock-adjustments.massDestroy');
    Route::resource('stock-adjustments', 'StockAdjustmentController');

    // Materials
    Route::delete('materials/destroy', 'MaterialsController@massDestroy')->name('materials.massDestroy');
    Route::post('materials/parse-csv-import', 'MaterialsController@parseCsvImport')->name('materials.parseCsvImport');
    Route::post('materials/process-csv-import', 'MaterialsController@processCsvImport')->name('materials.processCsvImport');
    Route::post('materials/import', 'MaterialsController@import')->name('materials.import');
    Route::get('materials/template-import', 'MaterialsController@template_import')->name('materials.templateImport');
    Route::get('materials/get-plates', 'MaterialsController@getPlates')->name('materials.getPlates');
    Route::get('materials/get-plate-raws', 'MaterialsController@getPlateRaws')->name('materials.getPlateRaws');
    Route::get('materials/get-chemicals', 'MaterialsController@getChemicals')->name('materials.getChemicals');
    Route::get('materials/get-materials', 'MaterialsController@getMaterials')->name('materials.getMaterials');
    Route::get('materials/get-material', 'MaterialsController@getMaterial')->name('materials.getMaterial');
    Route::get('materials/get-adjustment', 'MaterialsController@getAdjustment')->name('materials.getAdjustment');
    Route::get('materials/get-info-adjustment', 'MaterialsController@getInfoAdjustment')->name('materials.getInfoAdjustment');
    Route::post('materials/jangka', 'MaterialsController@jangka')->name('materials.jangka');
    Route::resource('materials', 'MaterialsController');

    // Stock Adjustment Detail
    Route::delete('stock-adjustment-details/destroy', 'StockAdjustmentDetailController@massDestroy')->name('stock-adjustment-details.massDestroy');
    Route::resource('stock-adjustment-details', 'StockAdjustmentDetailController');

    // Stock Saldo
    Route::delete('stock-saldos/destroy', 'StockSaldoController@massDestroy')->name('stock-saldos.massDestroy');
    Route::post('stock-saldos/jangka', 'StockSaldoController@jangka')->name('stock-saldos.jangka');
    Route::resource('stock-saldos', 'StockSaldoController');

    // Estimasi Saldo
    Route::delete('estimasi-saldos/destroy', 'EstimasiSaldoController@massDestroy')->name('estimasi-saldos.massDestroy');
    Route::resource('estimasi-saldos', 'EstimasiSaldoController');

    // Sales Order
    Route::delete('sales-orders/destroy', 'SalesOrderController@massDestroy')->name('sales-orders.massDestroy');
    Route::get('sales-orders/show', 'SalesOrderController@show')->name('sales-orders.show');
    Route::get('sales-orders/edit', 'SalesOrderController@edit')->name('sales-orders.edit');
    Route::get('sales-orders/estimasi', 'SalesOrderController@estimasi')->name('sales-orders.estimasi');
    Route::post('sales-orders/import', 'SalesOrderController@import')->name('sales-orders.import');
    Route::get('sales-orders/template-import', 'SalesOrderController@template_import')->name('sales-orders.templateImport');
    Route::resource('sales-orders', 'SalesOrderController', ['except' => ['edit', 'show']]);

    // Estimation
    Route::delete('estimations/destroy', 'EstimationController@massDestroy')->name('estimations.massDestroy');
    Route::post('estimations/import', 'EstimationController@import')->name('estimations.import');
    Route::get('estimations/{estimation}/adjust', 'EstimationController@adjust')->name('estimations.adjust');
    Route::post('estimations/adjust', 'EstimationController@adjustSave')->name('estimations.adjustSave');
    Route::resource('estimations', 'EstimationController');

    // Delivery Order
    Route::delete('delivery-orders/destroy', 'DeliveryOrderController@massDestroy')->name('delivery-orders.massDestroy');
    Route::get('delivery-orders/print-sj/{deliveryOrder}', 'DeliveryOrderController@printSj')->name('delivery-orders.printSj');
    Route::get('delivery-orders/get-delivery-order', 'DeliveryOrderController@getDeliveryOrder')->name('delivery-orders.getDeliveryOrder');
    Route::get('delivery-orders/get-estimasi', 'DeliveryOrderController@getEstimasi')->name('delivery-orders.getEstimasi');
    Route::get('delivery-orders/get-info-estimasi', 'DeliveryOrderController@getInfoEstimasi')->name('delivery-orders.getInfoEstimasi');
    Route::get('delivery-orders/{delivery_order}/adjust', 'DeliveryOrderController@adjust')->name('delivery-orders.adjust');
    Route::post('delivery-orders/adjust', 'DeliveryOrderController@adjustSave')->name('delivery-orders.adjustSave');
    Route::resource('delivery-orders', 'DeliveryOrderController');

    // Delivery Order Item
    Route::delete('delivery-order-items/destroy', 'DeliveryOrderItemController@massDestroy')->name('delivery-order-items.massDestroy');
    Route::resource('delivery-order-items', 'DeliveryOrderItemController');

    // Invoice
    Route::delete('invoices/destroy', 'InvoiceController@massDestroy')->name('invoices.massDestroy');
    Route::get('invoices/generate/{delivery}', 'InvoiceController@generate')->name('invoices.generate');
    Route::get('invoices/print-faktur/{invoice}', 'InvoiceController@printFaktur')->name('invoices.print-faktur');
    Route::get('invoices/editInvoice/{invoice}', 'InvoiceController@editInvoice')->name('invoices.editInvoice');
    Route::put('invoices/updateInvoice/{invoice}', 'InvoiceController@updateInvoice')->name('invoices.updateInvoice');
    Route::post('invoices/storeInvoice', 'InvoiceController@storeInvoice')->name('invoices.storeInvoice');
    Route::resource('invoices', 'InvoiceController');

    // Invoice Item
    Route::delete('invoice-items/destroy', 'InvoiceItemController@massDestroy')->name('invoice-items.massDestroy');
    Route::resource('invoice-items', 'InvoiceItemController');

    // Return Good
    Route::delete('return-goods/destroy', 'ReturnGoodController@massDestroy')->name('return-goods.massDestroy');
    Route::get('return-goods/print-faktur/{retur}', 'ReturnGoodController@printFaktur')->name('return-goods.print-faktur');
    Route::resource('return-goods', 'ReturnGoodController');

    // Return Good Item
    Route::delete('return-good-items/destroy', 'ReturnGoodItemController@massDestroy')->name('return-good-items.massDestroy');
    Route::resource('return-good-items', 'ReturnGoodItemController');

    // Rekap Billing
    Route::get('rekap-billings/billing', 'RekapBillingController@billing')->name('rekap-billings.billing');
    Route::resource('rekap-billings', 'RekapBillingController', ['only' => ['index']]);

    // Bill
    Route::post('bills/generate', 'BillController@generate')->name('bills.generate');
    Route::post('bills/jangka', 'BillController@jangka')->name('bills.jangka');
    Route::get('bills/billing', 'BillController@billing')->name('bills.billing');
    Route::get('bills/print-billing', 'BillController@cetakBilling')->name('bills.cetakBilling');
    Route::get('bills/export-billing', 'BillController@eksportRekapBilling')->name('bills.eksportRekapBilling');
    Route::resource('bills', 'BillController', ['except' => ['destroy']]);

    // Plate Print
    Route::delete('plate-prints/destroy', 'PlatePrintController@massDestroy')->name('plate-prints.massDestroy');
    Route::get('plate-prints/print-spk/{plate}', 'PlatePrintController@printSpk')->name('plate-prints.printSpk');
    Route::resource('plate-prints', 'PlatePrintController');

    // Aquarium
    Route::get('aquarium/realisasi/{plate}', 'AquariumController@realisasi')->name('aquarium.realisasi');
    Route::put('aquarium/realisasi/{plate}', 'AquariumController@realisasiStore')->name('aquarium.realisasiStore');
    Route::get('aquarium/task', 'AquariumController@task')->name('aquarium.task');
    Route::get('aquarium/working', 'AquariumController@working')->name('aquarium.working');
    Route::resource('aquarium', 'AquariumController', ['except' => ['create', 'store', 'destroy']]);

    // Delivery Plate
    Route::delete('delivery-plates/destroy', 'DeliveryPlateController@massDestroy')->name('delivery-plates.massDestroy');
    Route::get('delivery-plates/print-sj/{deliveryPlate}', 'DeliveryPlateController@printSj')->name('delivery-plates.printSj');
    Route::get('delivery-plates/get-plateitems', 'DeliveryPlateController@getPlateItems')->name('delivery-plates.getPlateItems');
    Route::get('delivery-plates/get-info-plateitem', 'DeliveryPlateController@getInfoPlateItem')->name('delivery-plates.getInfoPlateItem');
    Route::resource('delivery-plates', 'DeliveryPlateController');

    // Sales Billing
    Route::resource('sales-billings', 'SalesBillingController');

    // Bill Adjustment
    Route::delete('bill-adjustments/destroy', 'BillAdjustmentController@massDestroy')->name('bill-adjustments.massDestroy');
    Route::resource('bill-adjustments', 'BillAdjustmentController');

    // Payment
    Route::delete('payments/destroy', 'PaymentController@massDestroy')->name('payments.massDestroy');
    Route::get('payments/get-tagihan', 'PaymentController@getTagihan')->name('payments.getTagihan');
    Route::get('payments/kwitansi/{payment}', 'PaymentController@kwitansi')->name('payments.kwitansi');
    Route::resource('payments', 'PaymentController');

    // Transaction
    Route::delete('transactions/destroy', 'TransactionController@massDestroy')->name('transactions.massDestroy');
    Route::resource('transactions', 'TransactionController');

    // Production Transaction
    Route::delete('production-transactions/destroy', 'ProductionTransactionController@massDestroy')->name('production-transactions.massDestroy');
    Route::resource('production-transactions', 'ProductionTransactionController');

    // Transaction Total
    Route::resource('transaction-totals', 'TransactionTotalController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // Sales Report //Udah tak delete model e, nanti buat laporan aja ini
    Route::delete('sales-reports/destroy', 'SalesReportController@massDestroy')->name('sales-reports.massDestroy');
    Route::resource('sales-reports', 'SalesReportController');

    // Production Estimation
    Route::delete('production-estimations/destroy', 'ProductionEstimationController@massDestroy')->name('production-estimations.massDestroy');
    Route::post('production-estimations/jangka', 'ProductionEstimationController@jangka')->name('production-estimations.jangka');
    Route::get('production-estimations/cover-export', 'ProductionEstimationController@coverExport')->name('production-estimations.coverExport');
    Route::resource('production-estimations', 'ProductionEstimationController');

    // Production Payment
    Route::delete('production-payments/destroy', 'ProductionPaymentController@massDestroy')->name('production-payments.massDestroy');
    Route::get('production-payments/get-tagihan', 'ProductionPaymentController@getTagihan')->name('production-payments.getTagihan');
    Route::get('production-payments/kwitansi/{productionPayment}', 'ProductionPaymentController@kwitansi')->name('production-payments.kwitansi');
    Route::resource('production-payments', 'ProductionPaymentController');

    // Production Fee
    Route::post('fees/jangka', 'ProductionFeeController@jangka')->name('fees.jangka');
    Route::resource('fees', 'ProductionFeeController', ['only' => ['index']]);

    // Vendor
    Route::delete('vendors/destroy', 'VendorController@massDestroy')->name('vendors.massDestroy');
    Route::post('vendors/parse-csv-import', 'VendorController@parseCsvImport')->name('vendors.parseCsvImport');
    Route::post('vendors/process-csv-import', 'VendorController@processCsvImport')->name('vendors.processCsvImport');
    Route::resource('vendors', 'VendorController');

    // Vendor Cost
    Route::delete('vendor-costs/destroy', 'VendorCostController@massDestroy')->name('vendor-costs.massDestroy');
    Route::post('vendor-costs/parse-csv-import', 'VendorCostController@parseCsvImport')->name('vendor-costs.parseCsvImport');
    Route::post('vendor-costs/process-csv-import', 'VendorCostController@processCsvImport')->name('vendor-costs.processCsvImport');
    Route::resource('vendor-costs', 'VendorCostController');

    // Cetak
    Route::delete('cetaks/destroy', 'CetakController@massDestroy')->name('cetaks.massDestroy');
    Route::get('cetaks/{cetak}/realisasi', 'CetakController@realisasi')->name('cetaks.realisasi');
    Route::put('cetaks/realisasi/{cetak}', 'CetakController@realisasiStore')->name('cetaks.realiasasiStore');
    Route::get('cetaks/print-spc/{cetak}', 'CetakController@printSpc')->name('cetaks.printSpc');
    Route::get('cetaks/isi-cover', 'CetakController@getIsiCover')->name('cetaks.getIsiCover');
    Route::post('cetaks/rekap', 'CetakController@rekap')->name('cetaks.rekap');
    Route::resource('cetaks', 'CetakController');

    // Cetak Item
    Route::delete('cetak-items/destroy', 'CetakItemController@massDestroy')->name('cetak-items.massDestroy');
    Route::resource('cetak-items', 'CetakItemController');

    // Finishing
    Route::delete('finishings/destroy', 'FinishingController@massDestroy')->name('finishings.massDestroy');
    Route::get('finishings/masuk', 'FinishingController@masuk')->name('finishings.masuk');
    Route::post('finishings/masuk', 'FinishingController@masukStore')->name('finishings.masukstore');
    Route::get('finishings/{finishing}/realisasi', 'FinishingController@realisasi')->name('finishings.realisasi');
    Route::put('finishings/realisasi/{finishing}', 'FinishingController@realisasiStore')->name('finishings.realiasasiStore');
    Route::get('finishings/print-spk/{finishing}', 'FinishingController@printSpk')->name('finishings.printSpk');
    Route::post('finishings/rekap', 'FinishingController@rekap')->name('finishings.rekap');
    Route::resource('finishings', 'FinishingController');

    // Finishing Item
    Route::delete('finishing-masuks/destroy', 'FinishingMasukController@massDestroy')->name('finishing-masuks.massDestroy');
    // Route::get('finishing-masuks/show', 'FinishingMasukController@show')->name('finishing-masuks.show');
    Route::resource('finishing-masuks', 'FinishingMasukController');

    // Finishing Item
    Route::delete('finishing-items/destroy', 'FinishingItemController@massDestroy')->name('finishing-items.massDestroy');
    Route::resource('finishing-items', 'FinishingItemController');

    // Estimation Movement
    Route::resource('estimation-movements', 'EstimationMovementController', ['except' => ['create', 'store', 'edit', 'update', 'show', 'destroy']]);

    // Setting
    Route::delete('settings/destroy', 'SettingController@massDestroy')->name('settings.massDestroy');
    Route::resource('settings', 'SettingController');

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
