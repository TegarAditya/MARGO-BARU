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

class Book extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable, HasFactory, CreatedUpdatedBy;

    public $table = 'books';

    protected $appends = [
        'photo'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'code',
        'name',
        'description',
        'jenjang_id',
        'kurikulum_id',
        'mapel_id',
        'kelas_id',
        'isi_id',
        'cover_id',
        'semester_id',
        'created_by_id',
        'updated_by_id',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'jenjang_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function isi()
    {
        return $this->belongsTo(Isi::class, 'isi_id');
    }

    public function cover()
    {
        return $this->belongsTo(Cover::class, 'cover_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function buku()
    {
        return $this->hasOne(BookVariant::class, 'book_id')->where('type', 'L');
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

    public static function generateCode($jenjang_id, $kurikulum_id, $mapel_id, $kelas_id, $semester_id, $isi_id, $cover_id)
    {
        $jenjang = Jenjang::find($jenjang_id)->code ?? '000';
        $kurikulum = Kurikulum::find($kurikulum_id)->code ?? '00';
        $mapel = Mapel::find($mapel_id)->code ?? '000';
        $kelas = Kelas::find($kelas_id)->code ?? '00';
        $semester = Semester::find($semester_id)->code ?? '0000';
        $isi = Isi::find($isi_id)->code ?? '000';
        $cover = Cover::find($cover_id)->code ?? '000';

        if ($isi == $cover) {
            $penerbit = $isi;
        } else {
            $penerbit = $isi. '' .$cover;
        }

        return $jenjang. ''. $kurikulum. ''. $mapel. '' .$kelas. ''. $semester. '/'. $penerbit;
    }

    public static function generateName($jenjang_id, $kurikulum_id, $mapel_id, $kelas_id, $semester_id, $isi_id, $cover_id)
    {
        $jenjang = Jenjang::find($jenjang_id)->name ?? 'Tidak Ada';
        $kurikulum = Kurikulum::find($kurikulum_id)->name ?? 'Tidak Ada';
        $mapel = Mapel::find($mapel_id)->name ?? 'Tidak Ada';
        $kelas = Kelas::find($kelas_id)->name ?? 'Tidak Ada';
        $semester = Semester::find($semester_id)->name ?? 'Tidak Ada';
        $isi = Isi::find($isi_id)->name ?? 'Tidak Ada';
        $cover = Cover::find($cover_id)->name ?? 'Tidak Ada';

        if ($isi == $cover) {
            $penerbit = $isi;
        } else {
            $penerbit = $isi. ' - ' .$cover;
        }

        return $jenjang. ' - '. $kurikulum. ' - '. $mapel. ' - ' .$kelas. ' - '. $semester. ' - ('. $penerbit .') ';
    }
}
