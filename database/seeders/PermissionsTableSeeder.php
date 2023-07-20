<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'audit_log_show',
            ],
            [
                'id'    => 18,
                'title' => 'audit_log_access',
            ],
            [
                'id'    => 19,
                'title' => 'sale_access',
            ],
            [
                'id'    => 20,
                'title' => 'marketing_area_create',
            ],
            [
                'id'    => 21,
                'title' => 'marketing_area_edit',
            ],
            [
                'id'    => 22,
                'title' => 'marketing_area_show',
            ],
            [
                'id'    => 23,
                'title' => 'marketing_area_delete',
            ],
            [
                'id'    => 24,
                'title' => 'marketing_area_access',
            ],
            [
                'id'    => 25,
                'title' => 'salesperson_create',
            ],
            [
                'id'    => 26,
                'title' => 'salesperson_edit',
            ],
            [
                'id'    => 27,
                'title' => 'salesperson_show',
            ],
            [
                'id'    => 28,
                'title' => 'salesperson_delete',
            ],
            [
                'id'    => 29,
                'title' => 'salesperson_access',
            ],
            [
                'id'    => 30,
                'title' => 'address_create',
            ],
            [
                'id'    => 31,
                'title' => 'address_edit',
            ],
            [
                'id'    => 32,
                'title' => 'address_show',
            ],
            [
                'id'    => 33,
                'title' => 'address_delete',
            ],
            [
                'id'    => 34,
                'title' => 'address_access',
            ],
            [
                'id'    => 35,
                'title' => 'master_buku_access',
            ],
            [
                'id'    => 36,
                'title' => 'semester_create',
            ],
            [
                'id'    => 37,
                'title' => 'semester_edit',
            ],
            [
                'id'    => 38,
                'title' => 'semester_show',
            ],
            [
                'id'    => 39,
                'title' => 'semester_delete',
            ],
            [
                'id'    => 40,
                'title' => 'semester_access',
            ],
            [
                'id'    => 41,
                'title' => 'cover_create',
            ],
            [
                'id'    => 42,
                'title' => 'cover_edit',
            ],
            [
                'id'    => 43,
                'title' => 'cover_show',
            ],
            [
                'id'    => 44,
                'title' => 'cover_delete',
            ],
            [
                'id'    => 45,
                'title' => 'cover_access',
            ],
            [
                'id'    => 46,
                'title' => 'jenjang_create',
            ],
            [
                'id'    => 47,
                'title' => 'jenjang_edit',
            ],
            [
                'id'    => 48,
                'title' => 'jenjang_show',
            ],
            [
                'id'    => 49,
                'title' => 'jenjang_delete',
            ],
            [
                'id'    => 50,
                'title' => 'jenjang_access',
            ],
            [
                'id'    => 51,
                'title' => 'kurikulum_create',
            ],
            [
                'id'    => 52,
                'title' => 'kurikulum_edit',
            ],
            [
                'id'    => 53,
                'title' => 'kurikulum_show',
            ],
            [
                'id'    => 54,
                'title' => 'kurikulum_delete',
            ],
            [
                'id'    => 55,
                'title' => 'kurikulum_access',
            ],
            [
                'id'    => 56,
                'title' => 'mapel_create',
            ],
            [
                'id'    => 57,
                'title' => 'mapel_edit',
            ],
            [
                'id'    => 58,
                'title' => 'mapel_show',
            ],
            [
                'id'    => 59,
                'title' => 'mapel_delete',
            ],
            [
                'id'    => 60,
                'title' => 'mapel_access',
            ],
            [
                'id'    => 61,
                'title' => 'kela_create',
            ],
            [
                'id'    => 62,
                'title' => 'kela_edit',
            ],
            [
                'id'    => 63,
                'title' => 'kela_show',
            ],
            [
                'id'    => 64,
                'title' => 'kela_delete',
            ],
            [
                'id'    => 65,
                'title' => 'kela_access',
            ],
            [
                'id'    => 66,
                'title' => 'halaman_create',
            ],
            [
                'id'    => 67,
                'title' => 'halaman_edit',
            ],
            [
                'id'    => 68,
                'title' => 'halaman_show',
            ],
            [
                'id'    => 69,
                'title' => 'halaman_delete',
            ],
            [
                'id'    => 70,
                'title' => 'halaman_access',
            ],
            [
                'id'    => 71,
                'title' => 'general_master_access',
            ],
            [
                'id'    => 72,
                'title' => 'unit_create',
            ],
            [
                'id'    => 73,
                'title' => 'unit_edit',
            ],
            [
                'id'    => 74,
                'title' => 'unit_show',
            ],
            [
                'id'    => 75,
                'title' => 'unit_delete',
            ],
            [
                'id'    => 76,
                'title' => 'unit_access',
            ],
            [
                'id'    => 77,
                'title' => 'buku_access',
            ],
            [
                'id'    => 78,
                'title' => 'book_create',
            ],
            [
                'id'    => 79,
                'title' => 'book_edit',
            ],
            [
                'id'    => 80,
                'title' => 'book_show',
            ],
            [
                'id'    => 81,
                'title' => 'book_delete',
            ],
            [
                'id'    => 82,
                'title' => 'book_access',
            ],
            [
                'id'    => 83,
                'title' => 'book_variant_create',
            ],
            [
                'id'    => 84,
                'title' => 'book_variant_edit',
            ],
            [
                'id'    => 85,
                'title' => 'book_variant_show',
            ],
            [
                'id'    => 86,
                'title' => 'book_variant_delete',
            ],
            [
                'id'    => 87,
                'title' => 'book_variant_access',
            ],
            [
                'id'    => 88,
                'title' => 'warehouse_create',
            ],
            [
                'id'    => 89,
                'title' => 'warehouse_edit',
            ],
            [
                'id'    => 90,
                'title' => 'warehouse_show',
            ],
            [
                'id'    => 91,
                'title' => 'warehouse_delete',
            ],
            [
                'id'    => 92,
                'title' => 'warehouse_access',
            ],
            [
                'id'    => 93,
                'title' => 'stock_access',
            ],
            [
                'id'    => 94,
                'title' => 'stock_movement_create',
            ],
            [
                'id'    => 95,
                'title' => 'stock_movement_edit',
            ],
            [
                'id'    => 96,
                'title' => 'stock_movement_show',
            ],
            [
                'id'    => 97,
                'title' => 'stock_movement_delete',
            ],
            [
                'id'    => 98,
                'title' => 'stock_movement_access',
            ],
            [
                'id'    => 99,
                'title' => 'stock_opname_create',
            ],
            [
                'id'    => 100,
                'title' => 'stock_opname_edit',
            ],
            [
                'id'    => 101,
                'title' => 'stock_opname_show',
            ],
            [
                'id'    => 102,
                'title' => 'stock_opname_delete',
            ],
            [
                'id'    => 103,
                'title' => 'stock_opname_access',
            ],
            [
                'id'    => 104,
                'title' => 'stock_adjustment_create',
            ],
            [
                'id'    => 105,
                'title' => 'stock_adjustment_edit',
            ],
            [
                'id'    => 106,
                'title' => 'stock_adjustment_show',
            ],
            [
                'id'    => 107,
                'title' => 'stock_adjustment_delete',
            ],
            [
                'id'    => 108,
                'title' => 'stock_adjustment_access',
            ],
            [
                'id'    => 109,
                'title' => 'material_create',
            ],
            [
                'id'    => 110,
                'title' => 'material_edit',
            ],
            [
                'id'    => 111,
                'title' => 'material_show',
            ],
            [
                'id'    => 112,
                'title' => 'material_delete',
            ],
            [
                'id'    => 113,
                'title' => 'material_access',
            ],
            [
                'id'    => 114,
                'title' => 'stock_adjustment_detail_create',
            ],
            [
                'id'    => 115,
                'title' => 'stock_adjustment_detail_edit',
            ],
            [
                'id'    => 116,
                'title' => 'stock_adjustment_detail_show',
            ],
            [
                'id'    => 117,
                'title' => 'stock_adjustment_detail_delete',
            ],
            [
                'id'    => 118,
                'title' => 'stock_adjustment_detail_access',
            ],
            [
                'id'    => 119,
                'title' => 'stock_saldo_create',
            ],
            [
                'id'    => 120,
                'title' => 'stock_saldo_edit',
            ],
            [
                'id'    => 121,
                'title' => 'stock_saldo_show',
            ],
            [
                'id'    => 122,
                'title' => 'stock_saldo_delete',
            ],
            [
                'id'    => 123,
                'title' => 'stock_saldo_access',
            ],
            [
                'id'    => 124,
                'title' => 'estimasi_access',
            ],
            [
                'id'    => 125,
                'title' => 'estimasi_saldo_create',
            ],
            [
                'id'    => 126,
                'title' => 'estimasi_saldo_edit',
            ],
            [
                'id'    => 127,
                'title' => 'estimasi_saldo_show',
            ],
            [
                'id'    => 128,
                'title' => 'estimasi_saldo_delete',
            ],
            [
                'id'    => 129,
                'title' => 'estimasi_saldo_access',
            ],
            [
                'id'    => 130,
                'title' => 'sales_order_create',
            ],
            [
                'id'    => 131,
                'title' => 'sales_order_edit',
            ],
            [
                'id'    => 132,
                'title' => 'sales_order_show',
            ],
            [
                'id'    => 133,
                'title' => 'sales_order_delete',
            ],
            [
                'id'    => 134,
                'title' => 'sales_order_access',
            ],
            [
                'id'    => 135,
                'title' => 'pengiriman_access',
            ],
            [
                'id'    => 136,
                'title' => 'delivery_order_create',
            ],
            [
                'id'    => 137,
                'title' => 'delivery_order_edit',
            ],
            [
                'id'    => 138,
                'title' => 'delivery_order_show',
            ],
            [
                'id'    => 139,
                'title' => 'delivery_order_delete',
            ],
            [
                'id'    => 140,
                'title' => 'delivery_order_access',
            ],
            [
                'id'    => 141,
                'title' => 'tagihan_access',
            ],
            [
                'id'    => 142,
                'title' => 'delivery_order_item_create',
            ],
            [
                'id'    => 143,
                'title' => 'delivery_order_item_edit',
            ],
            [
                'id'    => 144,
                'title' => 'delivery_order_item_show',
            ],
            [
                'id'    => 145,
                'title' => 'delivery_order_item_delete',
            ],
            [
                'id'    => 146,
                'title' => 'delivery_order_item_access',
            ],
            [
                'id'    => 147,
                'title' => 'invoice_create',
            ],
            [
                'id'    => 148,
                'title' => 'invoice_edit',
            ],
            [
                'id'    => 149,
                'title' => 'invoice_show',
            ],
            [
                'id'    => 150,
                'title' => 'invoice_delete',
            ],
            [
                'id'    => 151,
                'title' => 'invoice_access',
            ],
            [
                'id'    => 152,
                'title' => 'invoice_item_create',
            ],
            [
                'id'    => 153,
                'title' => 'invoice_item_edit',
            ],
            [
                'id'    => 154,
                'title' => 'invoice_item_show',
            ],
            [
                'id'    => 155,
                'title' => 'invoice_item_delete',
            ],
            [
                'id'    => 156,
                'title' => 'invoice_item_access',
            ],
            [
                'id'    => 157,
                'title' => 'return_good_create',
            ],
            [
                'id'    => 158,
                'title' => 'return_good_edit',
            ],
            [
                'id'    => 159,
                'title' => 'return_good_show',
            ],
            [
                'id'    => 160,
                'title' => 'return_good_delete',
            ],
            [
                'id'    => 161,
                'title' => 'return_good_access',
            ],
            [
                'id'    => 162,
                'title' => 'return_good_item_create',
            ],
            [
                'id'    => 163,
                'title' => 'return_good_item_edit',
            ],
            [
                'id'    => 164,
                'title' => 'return_good_item_show',
            ],
            [
                'id'    => 165,
                'title' => 'return_good_item_delete',
            ],
            [
                'id'    => 166,
                'title' => 'return_good_item_access',
            ],
            [
                'id'    => 167,
                'title' => 'rekap_billing_create',
            ],
            [
                'id'    => 168,
                'title' => 'rekap_billing_edit',
            ],
            [
                'id'    => 169,
                'title' => 'rekap_billing_show',
            ],
            [
                'id'    => 170,
                'title' => 'rekap_billing_delete',
            ],
            [
                'id'    => 171,
                'title' => 'rekap_billing_access',
            ],
            [
                'id'    => 172,
                'title' => 'menu_pembayaran_access',
            ],
            [
                'id'    => 173,
                'title' => 'payment_create',
            ],
            [
                'id'    => 174,
                'title' => 'payment_edit',
            ],
            [
                'id'    => 175,
                'title' => 'payment_show',
            ],
            [
                'id'    => 176,
                'title' => 'payment_delete',
            ],
            [
                'id'    => 177,
                'title' => 'payment_access',
            ],
            [
                'id'    => 178,
                'title' => 'transaction_create',
            ],
            [
                'id'    => 179,
                'title' => 'transaction_edit',
            ],
            [
                'id'    => 180,
                'title' => 'transaction_show',
            ],
            [
                'id'    => 181,
                'title' => 'transaction_delete',
            ],
            [
                'id'    => 182,
                'title' => 'transaction_access',
            ],
            [
                'id'    => 183,
                'title' => 'transaksi_access',
            ],
            [
                'id'    => 184,
                'title' => 'sales_report_create',
            ],
            [
                'id'    => 185,
                'title' => 'sales_report_edit',
            ],
            [
                'id'    => 186,
                'title' => 'sales_report_show',
            ],
            [
                'id'    => 187,
                'title' => 'sales_report_delete',
            ],
            [
                'id'    => 188,
                'title' => 'sales_report_access',
            ],
            [
                'id'    => 189,
                'title' => 'production_estimation_create',
            ],
            [
                'id'    => 190,
                'title' => 'production_estimation_edit',
            ],
            [
                'id'    => 191,
                'title' => 'production_estimation_show',
            ],
            [
                'id'    => 192,
                'title' => 'production_estimation_delete',
            ],
            [
                'id'    => 193,
                'title' => 'production_estimation_access',
            ],
            [
                'id'    => 194,
                'title' => 'produksi_access',
            ],
            [
                'id'    => 195,
                'title' => 'vendor_create',
            ],
            [
                'id'    => 196,
                'title' => 'vendor_edit',
            ],
            [
                'id'    => 197,
                'title' => 'vendor_show',
            ],
            [
                'id'    => 198,
                'title' => 'vendor_delete',
            ],
            [
                'id'    => 199,
                'title' => 'vendor_access',
            ],
            [
                'id'    => 200,
                'title' => 'cetak_create',
            ],
            [
                'id'    => 201,
                'title' => 'cetak_edit',
            ],
            [
                'id'    => 202,
                'title' => 'cetak_show',
            ],
            [
                'id'    => 203,
                'title' => 'cetak_delete',
            ],
            [
                'id'    => 204,
                'title' => 'cetak_access',
            ],
            [
                'id'    => 205,
                'title' => 'cetak_item_create',
            ],
            [
                'id'    => 206,
                'title' => 'cetak_item_edit',
            ],
            [
                'id'    => 207,
                'title' => 'cetak_item_show',
            ],
            [
                'id'    => 208,
                'title' => 'cetak_item_delete',
            ],
            [
                'id'    => 209,
                'title' => 'cetak_item_access',
            ],
            [
                'id'    => 210,
                'title' => 'finishing_create',
            ],
            [
                'id'    => 211,
                'title' => 'finishing_edit',
            ],
            [
                'id'    => 212,
                'title' => 'finishing_show',
            ],
            [
                'id'    => 213,
                'title' => 'finishing_delete',
            ],
            [
                'id'    => 214,
                'title' => 'finishing_access',
            ],
            [
                'id'    => 215,
                'title' => 'finishing_item_create',
            ],
            [
                'id'    => 216,
                'title' => 'finishing_item_edit',
            ],
            [
                'id'    => 217,
                'title' => 'finishing_item_show',
            ],
            [
                'id'    => 218,
                'title' => 'finishing_item_delete',
            ],
            [
                'id'    => 219,
                'title' => 'finishing_item_access',
            ],
            [
                'id'    => 220,
                'title' => 'estimation_movement_access',
            ],
            [
                'id'    => 221,
                'title' => 'setting_create',
            ],
            [
                'id'    => 222,
                'title' => 'setting_edit',
            ],
            [
                'id'    => 223,
                'title' => 'setting_show',
            ],
            [
                'id'    => 224,
                'title' => 'setting_delete',
            ],
            [
                'id'    => 225,
                'title' => 'setting_access',
            ],
            [
                'id'    => 226,
                'title' => 'isi_create',
            ],
            [
                'id'    => 227,
                'title' => 'isi_edit',
            ],
            [
                'id'    => 228,
                'title' => 'isi_show',
            ],
            [
                'id'    => 229,
                'title' => 'isi_delete',
            ],
            [
                'id'    => 230,
                'title' => 'isi_access',
            ],
            [
                'id'    => 231,
                'title' => 'book_component_create',
            ],
            [
                'id'    => 232,
                'title' => 'book_component_edit',
            ],
            [
                'id'    => 233,
                'title' => 'book_component_show',
            ],
            [
                'id'    => 234,
                'title' => 'book_component_delete',
            ],
            [
                'id'    => 235,
                'title' => 'book_component_access',
            ],
            [
                'id'    => 236,
                'title' => 'vendor_cost_create',
            ],
            [
                'id'    => 237,
                'title' => 'vendor_cost_edit',
            ],
            [
                'id'    => 238,
                'title' => 'vendor_cost_show',
            ],
            [
                'id'    => 239,
                'title' => 'vendor_cost_delete',
            ],
            [
                'id'    => 240,
                'title' => 'vendor_cost_access',
            ],
            [
                'id'    => 241,
                'title' => 'vendor_menu_access',
            ],
            [
                'id'    => 242,
                'title' => 'group_area_create',
            ],
            [
                'id'    => 243,
                'title' => 'group_area_edit',
            ],
            [
                'id'    => 244,
                'title' => 'group_area_show',
            ],
            [
                'id'    => 245,
                'title' => 'group_area_delete',
            ],
            [
                'id'    => 246,
                'title' => 'group_area_access',
            ],
            [
                'id'    => 247,
                'title' => 'transaction_total_show',
            ],
            [
                'id'    => 248,
                'title' => 'transaction_total_access',
            ],
            [
                'id'    => 249,
                'title' => 'sales_billing_access',
            ],
            [
                'id'    => 250,
                'title' => 'profile_password_edit',
            ],
        ];

        Permission::insert($permissions);
    }
}
