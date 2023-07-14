<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ trans('panel.site_title') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs("admin.home") ? "active" : "" }}" href="{{ route("admin.home") }}">
                        <i class="fas fa-fw fa-tachometer-alt nav-icon">
                        </i>
                        <p>
                            {{ trans('global.dashboard') }}
                        </p>
                    </a>
                </li>
                @can('user_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/permissions*") ? "menu-open" : "" }} {{ request()->is("admin/roles*") ? "menu-open" : "" }} {{ request()->is("admin/users*") ? "menu-open" : "" }} {{ request()->is("admin/audit-logs*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/permissions*") ? "active" : "" }} {{ request()->is("admin/roles*") ? "active" : "" }} {{ request()->is("admin/users*") ? "active" : "" }} {{ request()->is("admin/audit-logs*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-users">

                            </i>
                            <p>
                                {{ trans('cruds.userManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-unlock-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.permission.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-briefcase">

                                        </i>
                                        <p>
                                            {{ trans('cruds.role.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user">

                                        </i>
                                        <p>
                                            {{ trans('cruds.user.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('audit_log_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.audit-logs.index") }}" class="nav-link {{ request()->is("admin/audit-logs") || request()->is("admin/audit-logs/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.auditLog.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('general_master_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/units*") ? "menu-open" : "" }} {{ request()->is("admin/settings*") ? "menu-open" : "" }} {{ request()->is("admin/warehouses*") ? "menu-open" : "" }} {{ request()->is("admin/vendors*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/units*") ? "active" : "" }} {{ request()->is("admin/settings*") ? "active" : "" }} {{ request()->is("admin/warehouses*") ? "active" : "" }} {{ request()->is("admin/vendors*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.generalMaster.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('setting_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.settings.index") }}" class="nav-link {{ request()->is("admin/settings") || request()->is("admin/settings/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.setting.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('unit_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.units.index") }}" class="nav-link {{ request()->is("admin/units") || request()->is("admin/units/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-puzzle-piece">

                                        </i>
                                        <p>
                                            {{ trans('cruds.unit.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('warehouse_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.warehouses.index") }}" class="nav-link {{ request()->is("admin/warehouses") || request()->is("admin/warehouses/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-warehouse">

                                        </i>
                                        <p>
                                            {{ trans('cruds.warehouse.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('master_buku_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/isis*") ? "menu-open" : "" }} {{ request()->is("admin/semesters*") ? "menu-open" : "" }} {{ request()->is("admin/covers*") ? "menu-open" : "" }} {{ request()->is("admin/jenjangs*") ? "menu-open" : "" }} {{ request()->is("admin/kurikulums*") ? "menu-open" : "" }} {{ request()->is("admin/mapels*") ? "menu-open" : "" }} {{ request()->is("admin/kelas*") ? "menu-open" : "" }} {{ request()->is("admin/halaman*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/isis*") ? "active" : "" }} {{ request()->is("admin/semesters*") ? "active" : "" }} {{ request()->is("admin/covers*") ? "active" : "" }} {{ request()->is("admin/jenjangs*") ? "active" : "" }} {{ request()->is("admin/kurikulums*") ? "active" : "" }} {{ request()->is("admin/mapels*") ? "active" : "" }} {{ request()->is("admin/kelas*") ? "active" : "" }} {{ request()->is("admin/halaman*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.masterBuku.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('semester_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.semesters.index") }}" class="nav-link {{ request()->is("admin/semesters") || request()->is("admin/semesters/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon far fa-clock">

                                        </i>
                                        <p>
                                            {{ trans('cruds.semester.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('isi_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.isis.index") }}" class="nav-link {{ request()->is("admin/isis") || request()->is("admin/isis/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book">

                                        </i>
                                        <p>
                                            {{ trans('cruds.isi.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('cover_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.covers.index") }}" class="nav-link {{ request()->is("admin/covers") || request()->is("admin/covers/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book-open">

                                        </i>
                                        <p>
                                            {{ trans('cruds.cover.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('jenjang_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.jenjangs.index") }}" class="nav-link {{ request()->is("admin/jenjangs") || request()->is("admin/jenjangs/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-school">

                                        </i>
                                        <p>
                                            {{ trans('cruds.jenjang.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('kurikulum_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.kurikulums.index") }}" class="nav-link {{ request()->is("admin/kurikulums") || request()->is("admin/kurikulums/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-i-cursor">

                                        </i>
                                        <p>
                                            {{ trans('cruds.kurikulum.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('mapel_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.mapels.index") }}" class="nav-link {{ request()->is("admin/mapels") || request()->is("admin/mapels/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book">

                                        </i>
                                        <p>
                                            {{ trans('cruds.mapel.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('kela_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.kelas.index") }}" class="nav-link {{ request()->is("admin/kelas") || request()->is("admin/kelas/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-list-ol">

                                        </i>
                                        <p>
                                            {{ trans('cruds.kela.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('halaman_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.halaman.index") }}" class="nav-link {{ request()->is("admin/halaman") || request()->is("admin/halaman/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file">

                                        </i>
                                        <p>
                                            {{ trans('cruds.halaman.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('vendor_menu_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/vendors*") ? "menu-open" : "" }} {{ request()->is("admin/vendor-costs*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/vendors*") ? "active" : "" }} {{ request()->is("admin/vendor-costs*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-user-secret">

                            </i>
                            <p>
                                {{ trans('cruds.vendorMenu.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('vendor_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.vendors.index") }}" class="nav-link {{ request()->is("admin/vendors") || request()->is("admin/vendors/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user">

                                        </i>
                                        <p>
                                            {{ trans('cruds.vendor.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('vendor_cost_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.vendor-costs.index") }}" class="nav-link {{ request()->is("admin/vendor-costs") || request()->is("admin/vendor-costs/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-dollar-sign">

                                        </i>
                                        <p>
                                            {{ trans('cruds.vendorCost.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('sale_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/marketing-areas*") ? "menu-open" : "" }} {{ request()->is("admin/salespeople*") ? "menu-open" : "" }} {{ request()->is("admin/addresses*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/marketing-areas*") ? "active" : "" }} {{ request()->is("admin/salespeople*") ? "active" : "" }} {{ request()->is("admin/addresses*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-users">

                            </i>
                            <p>
                                {{ trans('cruds.sale.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('marketing_area_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.marketing-areas.index") }}" class="nav-link {{ request()->is("admin/marketing-areas") || request()->is("admin/marketing-areas/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-globe-americas">

                                        </i>
                                        <p>
                                            {{ trans('cruds.marketingArea.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('salesperson_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.salespeople.index") }}" class="nav-link {{ request()->is("admin/salespeople") || request()->is("admin/salespeople/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.salesperson.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('address_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.addresses.index") }}" class="nav-link {{ request()->is("admin/addresses") || request()->is("admin/addresses/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-map-marker-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.address.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('material_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.materials.index") }}" class="nav-link {{ request()->is("admin/materials") || request()->is("admin/materials/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-bong">

                            </i>
                            <p>
                                {{ trans('cruds.material.title') }}
                            </p>
                        </a>
                    </li>
                @endcan
                @can('buku_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/books*") ? "menu-open" : "" }} {{ request()->is("admin/book-components*") ? "menu-open" : "" }} {{ request()->is("admin/book-variants*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/books*") ? "active" : "" }} {{ request()->is("admin/book-components*") ? "active" : "" }} {{ request()->is("admin/book-variants*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-book">

                            </i>
                            <p>
                                {{ trans('cruds.buku.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('book_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.books.index") }}" class="nav-link {{ request()->is("admin/books") || request()->is("admin/books/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book">

                                        </i>
                                        <p>
                                            {{ trans('cruds.book.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('book_variant_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.book-variants.index") }}" class="nav-link {{ request()->is("admin/book-variants") || request()->is("admin/book-variants/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book">

                                        </i>
                                        <p>
                                            {{ trans('cruds.bookVariant.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('book_component_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.book-components.index") }}" class="nav-link {{ request()->is("admin/book-components") || request()->is("admin/book-components/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book">

                                        </i>
                                        <p>
                                            {{ trans('cruds.bookComponent.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('stock_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/stock-movements*") ? "menu-open" : "" }} {{ request()->is("admin/stock-saldos*") ? "menu-open" : "" }} {{ request()->is("admin/stock-opnames*") ? "menu-open" : "" }} {{ request()->is("admin/stock-adjustments*") ? "menu-open" : "" }} {{ request()->is("admin/stock-adjustment-details*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/stock-movements*") ? "active" : "" }} {{ request()->is("admin/stock-saldos*") ? "active" : "" }} {{ request()->is("admin/stock-opnames*") ? "active" : "" }} {{ request()->is("admin/stock-adjustments*") ? "active" : "" }} {{ request()->is("admin/stock-adjustment-details*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-boxes">

                            </i>
                            <p>
                                {{ trans('cruds.stock.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('stock_movement_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.stock-movements.index") }}" class="nav-link {{ request()->is("admin/stock-movements") || request()->is("admin/stock-movements/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-parachute-box">

                                        </i>
                                        <p>
                                            {{ trans('cruds.stockMovement.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('stock_saldo_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.stock-saldos.index") }}" class="nav-link {{ request()->is("admin/stock-saldos") || request()->is("admin/stock-saldos/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-boxes">

                                        </i>
                                        <p>
                                            {{ trans('cruds.stockSaldo.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('stock_opname_access')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is("admin/stock-opnames") || request()->is("admin/stock-opnames/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-archive">

                                        </i>
                                        <p>
                                            {{ trans('cruds.stockOpname.title') }}
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                          <a href="{{ route("admin.stock-opnames.summary") }}" class="nav-link {{ request()->is("admin/stock-opnames/summary") ? "active" : "" }}">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Summary</p>
                                          </a>
                                        </li>
                                        <li class="nav-item">
                                          <a href="{{ route("admin.stock-opnames.index") }}" class="nav-link {{ request()->is("admin/stock-opnames/index") ? "active" : "" }}">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Stock</p>
                                          </a>
                                        </li>
                                      </ul>
                                </li>
                            @endcan
                            @can('stock_adjustment_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.stock-adjustments.index") }}" class="nav-link {{ request()->is("admin/stock-adjustments") || request()->is("admin/stock-adjustments/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-box">

                                        </i>
                                        <p>
                                            {{ trans('cruds.stockAdjustment.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('estimasi_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/sales-orders*") ? "menu-open" : "" }} {{ request()->is("admin/estimasi-saldos*") ? "menu-open" : "" }} {{ request()->is("admin/production-estimations*") ? "menu-open" : "" }} {{ request()->is("admin/estimation-movements*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/sales-orders*") ? "active" : "" }} {{ request()->is("admin/estimasi-saldos*") ? "active" : "" }} {{ request()->is("admin/production-estimations*") ? "active" : "" }} {{ request()->is("admin/estimation-movements*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-weight">

                            </i>
                            <p>
                                {{ trans('cruds.estimasi.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('sales_order_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.sales-orders.index") }}" class="nav-link {{ request()->is("admin/sales-orders") || request()->is("admin/sales-orders/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-box">

                                        </i>
                                        <p>
                                            {{ trans('cruds.salesOrder.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('estimasi_saldo_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.estimasi-saldos.index") }}" class="nav-link {{ request()->is("admin/estimasi-saldos") || request()->is("admin/estimasi-saldos/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file-invoice-dollar">

                                        </i>
                                        <p>
                                            {{ trans('cruds.estimasiSaldo.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('production_estimation_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.production-estimations.index") }}" class="nav-link {{ request()->is("admin/production-estimations") || request()->is("admin/production-estimations/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fab fa-accessible-icon">

                                        </i>
                                        <p>
                                            {{ trans('cruds.productionEstimation.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('estimation_movement_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.estimation-movements.index") }}" class="nav-link {{ request()->is("admin/estimation-movements") || request()->is("admin/estimation-movements/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-arrows-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.estimationMovement.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('pengiriman_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/delivery-orders*") ? "menu-open" : "" }} {{ request()->is("admin/delivery-order-items*") ? "menu-open" : "" }} {{ request()->is("admin/return-goods*") ? "menu-open" : "" }} {{ request()->is("admin/return-good-items*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/delivery-orders*") ? "active" : "" }} {{ request()->is("admin/delivery-order-items*") ? "active" : "" }} {{ request()->is("admin/return-goods*") ? "active" : "" }} {{ request()->is("admin/return-good-items*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-truck">

                            </i>
                            <p>
                                {{ trans('cruds.pengiriman.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('delivery_order_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.delivery-orders.index") }}" class="nav-link {{ request()->is("admin/delivery-orders") || request()->is("admin/delivery-orders/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-truck">

                                        </i>
                                        <p>
                                            {{ trans('cruds.deliveryOrder.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('return_good_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.return-goods.index") }}" class="nav-link {{ request()->is("admin/return-goods") || request()->is("admin/return-goods/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-boxes">

                                        </i>
                                        <p>
                                            {{ trans('cruds.returnGood.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('tagihan_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/invoices*") ? "menu-open" : "" }} {{ request()->is("admin/invoice-items*") ? "menu-open" : "" }} {{ request()->is("admin/rekap-billings*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/invoices*") ? "active" : "" }} {{ request()->is("admin/invoice-items*") ? "active" : "" }} {{ request()->is("admin/rekap-billings*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-file-invoice">

                            </i>
                            <p>
                                {{ trans('cruds.tagihan.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('invoice_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.invoices.index") }}" class="nav-link {{ request()->is("admin/invoices") || request()->is("admin/invoices/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-money-check-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.invoice.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('rekap_billing_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.rekap-billings.index") }}" class="nav-link {{ request()->is("admin/rekap-billings") || request()->is("admin/rekap-billings/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-dollar-sign">

                                        </i>
                                        <p>
                                            {{ trans('cruds.rekapBilling.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('menu_pembayaran_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/payments*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/payments*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-hand-holding-usd">

                            </i>
                            <p>
                                {{ trans('cruds.menuPembayaran.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('payment_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.payments.index") }}" class="nav-link {{ request()->is("admin/payments") || request()->is("admin/payments/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-hand-holding-usd">

                                        </i>
                                        <p>
                                            {{ trans('cruds.payment.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('transaksi_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/transactions*") ? "menu-open" : "" }} {{ request()->is("admin/sales-reports*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/transactions*") ? "active" : "" }} {{ request()->is("admin/sales-reports*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-file-signature">

                            </i>
                            <p>
                                {{ trans('cruds.transaksi.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('transaction_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.transactions.index") }}" class="nav-link {{ request()->is("admin/transactions") || request()->is("admin/transactions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file-signature">

                                        </i>
                                        <p>
                                            {{ trans('cruds.transaction.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('sales_report_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.sales-reports.index") }}" class="nav-link {{ request()->is("admin/sales-reports") || request()->is("admin/sales-reports/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-flag">

                                        </i>
                                        <p>
                                            {{ trans('cruds.salesReport.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('produksi_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/cetaks*") ? "menu-open" : "" }} {{ request()->is("admin/cetak-items*") ? "menu-open" : "" }} {{ request()->is("admin/finishings*") ? "menu-open" : "" }} {{ request()->is("admin/finishing-items*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/cetaks*") ? "active" : "" }} {{ request()->is("admin/cetak-items*") ? "active" : "" }} {{ request()->is("admin/finishings*") ? "active" : "" }} {{ request()->is("admin/finishing-items*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-print">

                            </i>
                            <p>
                                {{ trans('cruds.produksi.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('cetak_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.cetaks.index") }}" class="nav-link {{ request()->is("admin/cetaks") || request()->is("admin/cetaks/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-print">

                                        </i>
                                        <p>
                                            {{ trans('cruds.cetak.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @can('cetak_item_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.cetak-items.index") }}" class="nav-link {{ request()->is("admin/cetak-items") || request()->is("admin/cetak-items/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon far fa-dot-circle">

                                        </i>
                                        <p>
                                            {{ trans('cruds.cetakItem.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan --}}
                            @can('finishing_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.finishings.index") }}" class="nav-link {{ request()->is("admin/finishings") || request()->is("admin/finishings/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-book-open">

                                        </i>
                                        <p>
                                            {{ trans('cruds.finishing.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @can('finishing_item_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.finishing-items.index") }}" class="nav-link {{ request()->is("admin/finishing-items") || request()->is("admin/finishing-items/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-bullseye">

                                        </i>
                                        <p>
                                            {{ trans('cruds.finishingItem.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan --}}
                        </ul>
                    </li>
                @endcan
                <li class="nav-item">
                    <a href="{{ route("admin.systemCalendar") }}" class="nav-link {{ request()->is("admin/system-calendar") || request()->is("admin/system-calendar/*") ? "active" : "" }}">
                        <i class="fas fa-fw fa-calendar nav-icon">

                        </i>
                        <p>
                            {{ trans('global.systemCalendar') }}
                        </p>
                    </a>
                </li>
                @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                    @can('profile_password_edit')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                                <i class="fa-fw fas fa-key nav-icon">
                                </i>
                                <p>
                                    {{ trans('global.change_password') }}
                                </p>
                            </a>
                        </li>
                    @endcan
                @endif
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <p>
                            <i class="fas fa-fw fa-sign-out-alt nav-icon">

                            </i>
                            <p>{{ trans('global.logout') }}</p>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
