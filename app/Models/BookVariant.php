<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\CreatedUpdatedBy;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BookVariant extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable, HasFactory, CreatedUpdatedBy;

    public $table = 'book_variants';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TYPE_SELECT = [
        'L' => 'LKS',
        'P' => 'Pegangan Guru',
        'K' => 'Kunci',
        'I' => 'Isi LKS',
        'C' => 'Cover LKS',
        'S' => 'Isi Pegangan Guru',
        'V' => 'Cover Pegangan Guru',
        'U' => 'Isi Kunci',
    ];

    public const LKS_TYPE = [
        'C' => 'Cover LKS',
        'I' => 'Isi LKS',
    ];

    public const PG_TYPE = [
        'V' => 'Cover PG',
        'S' => 'Isi PG',
    ];

    public const KUNCI_TYPE = [
        'U' => 'Isi Kunci',
    ];

    protected $fillable = [
        'book_id',
        'code',
        'name',
        'type',
        'jenjang_id',
        'kurikulum_id',
        'isi_id',
        'cover_id',
        'mapel_id',
        'kelas_id',
        'halaman_id',
        'semester_id',
        'warehouse_id',
        'stock',
        'unit_id',
        'price',
        'cost',
        'status',
        'created_by_id',
        'updated_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'short_name',
        'book_type',
        'photo',
        'nama_urut'
    ];

    protected $casts = [
        'price' => 'double',
        'cost' => 'double',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function components()
    {
        return $this->belongsToMany(BookVariant::class, 'book_parent_book_child','child_id', 'parent_id');
    }

    public function material_of()
    {
        return $this->belongsToMany(BookVariant::class, 'book_parent_book_child', 'parent_id', 'child_id');
    }

    public function getShortNameAttribute()
    {
        $name = $this->mapel?->name .' - '. $this->kelas?->name.' - '. $this->halaman?->name;

        if ($this->type !== 'L') {
            $tipe = BookVariant::TYPE_SELECT[$this->type] ?? '';
            return $tipe .' - '. $name;
        }

        return $name;
    }

    public function getKelengkapanNameAttribute()
    {
        $name = ($this->isi ? $this->isi->name : '') .''. ($this->isi && $this->cover ? ' - ' : ' ') .''. ($this->cover ? $this->cover->name : '');
        $halaman = $this->halaman ? $this->halaman->name : 'Halaman KOSONG';
        if ($this->type !== 'L') {
            $tipe = BookVariant::TYPE_SELECT[$this->type] ?? '';
            return $tipe .' - '.$halaman .' - '. $name;
        }

        return $name;
    }

    public function getBookTypeAttribute()
    {
        $name = BookVariant::TYPE_SELECT[$this->type] ?? '';

        return $name;
    }

    public function getJenKumAttribute()
    {
        $name = $this->jenjang->name. ' - '. $this->kurikulum->name;

        return $name;
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    public function isi()
    {
        return $this->belongsTo(Isi::class, 'isi_id');
    }

    public function cover()
    {
        return $this->belongsTo(Cover::class, 'cover_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function halaman()
    {
        return $this->belongsTo(Halaman::class, 'halaman_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function pengedit()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function estimasi()
    {
        return $this->hasMany(SalesOrder::class, 'product_id');
    }

    public function estimasi_items()
    {
        return $this->hasMany(EstimationItem::class, 'product_id');
    }

    public function dikirim()
    {
        return $this->hasMany(DeliveryOrderItem::class, 'product_id');
    }

    public function diretur()
    {
        return $this->hasMany(ReturnGoodItem::class, 'product_id');
    }

    public function adjustment()
    {
        return $this->hasMany(StockAdjustmentDetail::class, 'product_id');
    }

    public function movement()
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }

    public function finishing()
    {
        return $this->hasMany(FinishingItem::class, 'product_id');
    }

    public function estimasi_produksi()
    {
        return $this->hasOne(ProductionEstimation::class, 'product_id');
    }

    public function getPhotoAttribute()
    {
        $files = $this->getMedia('photo');
        $files->each(function ($item) {
            $item->url       = $item->getUrl();
            $item->thumbnail = $item->getUrl('thumb');
            $item->preview   = $item->getUrl('preview');
        });

        return $files;
    }

    public static function generateCode($key, $code)
    {
        $base = substr($code, 0, 14);
        $isi = substr($code, 15, 3);
        $cover = substr($code, 18, 3);

        if (!$cover) {
            $cover = $isi;
        }

        if ($key == 'P' || $key == 'I' || $key == 'S' || $key == 'K' || $key == 'U') {
            return $key. '-' . $base. '/'. $isi;
        }

        if ($key == 'C' || $key == 'V') {
            return $key. '-' . $base. '/'. $cover;
        }
    }

    public static function generateName($key, $jenjang_id, $kurikulum_id, $mapel_id, $kelas_id, $semester_id, $isi_id, $cover_id)
    {
        $jenjang = Jenjang::find($jenjang_id)->name ?? 'Tidak Ada';
        $kurikulum = Kurikulum::find($kurikulum_id)->name ?? 'Tidak Ada';
        $mapel = Mapel::find($mapel_id)->name ?? 'Tidak Ada';
        $kelas = Kelas::find($kelas_id)->name ?? 'Tidak Ada';
        $semester = Semester::find($semester_id)->name ?? 'Tidak Ada';
        $isi = Isi::find($isi_id)->name ?? 'Tidak Ada';
        $cover = Cover::find($cover_id)->name ?? 'Tidak Ada';

        if ($key == 'P' || $key == 'K' || $key == 'I' || $key == 'S' || $key == 'U') {
            return BookVariant::TYPE_SELECT[$key]. ' - '. $jenjang. ' - '. $kurikulum. ' - '. $mapel. ' - ' .$kelas. ' - '. $semester. ' - ('. $isi .') ';
        }

        if ($key == 'C' || $key == 'V') {
            return BookVariant::TYPE_SELECT[$key]. ' - '. $jenjang. ' - '. $kurikulum. ' - '. $mapel. ' - ' .$kelas. ' - '. $semester. ' - ('. $cover .') ';
        }
    }

    public function getNamaUrutAttribute()
    {
        if (str_contains($this->name, 'TEMA ') ) {
            return $this->kelas?->name . ' '. $this->mapel?->name;
        }
        return $this->mapel?->name. ' '. $this->kelas?->name;
    }
}
