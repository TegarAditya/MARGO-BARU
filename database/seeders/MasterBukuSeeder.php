<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class MasterBukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $halaman = "INSERT INTO `halamen` (`CODE`, `NAME`) VALUES
        ('48', 'HALAMAN 48'),
        ('64', 'HALAMAN 64'),
        ('72', 'HALAMAN 72'),
        ('80', 'HALAMAN 80'),
        ('88', 'HALAMAN 88'),
        ('96', 'HALAMAN 96'),
        ('112', 'HALAMAN 112'),
        ('112(BENDING)', 'HALAMAN 112(BENDING)'),
        ('120', 'HALAMAN 120'),
        ('128', 'HALAMAN 128');";

        DB::unprepared($halaman);

        $jenjang = "INSERT INTO `jenjangs` (`CODE`, `NAME`) VALUES
        ('SDD', 'SD'),
        ('SMP', 'SMP'),
        ('SMA', 'SMA'),
        ('MI', 'MII'),
        ('MTS', 'MTS'),
        ('MAA', 'MA'),
        ('SMK', 'SMK'),
        ('SMW', 'SMA WAJIB'),
        ('SMM', 'SMA PEMINATAN'),
        ('SKT', 'SMK 1 TAHUN');";

        DB::unprepared($jenjang);

        $kelas = "INSERT INTO `kelas` (`CODE`, `NAME`) VALUES
        ('01', 'KELAS 1'),
        ('02', 'KELAS 2'),
        ('03', 'KELAS 3'),
        ('04', 'KELAS 4'),
        ('05', 'KELAS 5'),
        ('06', 'KELAS 6'),
        ('07', 'KELAS 7'),
        ('08', 'KELAS 8'),
        ('09', 'KELAS 9'),
        ('10', 'KELAS 10'),
        ('11', 'KELAS 11'),
        ('12', 'KELAS 12');";

        DB::unprepared($kelas);

        $isi = "INSERT INTO `isis` (`CODE`, `NAME`) VALUES
        ('MMJ', 'MARGO MITRO JOYO'),
        ('MMP', 'MATRA MEDIA PRESINDO'),
        ('SRG', 'MGMP SRAGEN'),
        ('CLP', 'MGMP CILACAP'),
        ('BGR', 'MGMP BOGOR'),
        ('JRA', 'JUARA'),
        ('SPT', 'SIPINTAR'),
        ('PDW', 'PANDAWA')";

        DB::unprepared($isi);

        $kurikulum = "INSERT INTO `kurikulums` (`CODE`, `NAME`) VALUES
        ('KM', 'KURIKULUM MERDEKA'),
        ('13', 'K13'),
        ('83', 'KMA 183');";

        DB::unprepared($kurikulum);

        $mapel = "INSERT INTO `mapels` (`CODE`, `NAME`) VALUES
        ('PAI', 'PENDIDIKAN AGAMA ISLAM'),
        ('PKN', 'PENDIDIKAN PANCASILA / PPKN'),
        ('IDN', 'BAHASA INDONESIA'),
        ('MTK', 'MATEMATIKA'),
        ('PJO', 'PENJAS'),
        ('IGG', 'BAHASA INGGRIS'),
        ('SMU', 'SENI MUSIK'),
        ('SRU', 'SENI RUPA'),
        ('IFM', 'INFORMATIKA'),
        ('SJA', 'SEJARAH'),
        ('SJW', 'SEJARAH WAJIB'),
        ('SJI', 'SEJARAH INDONESIA'),
        ('JTE', 'BAHASA JAWA TENGAH'),
        ('JTI', 'BAHASA JAWA TIMUR'),
        ('SND', 'BAHASA SUNDA'),
        ('AAK', 'AKIDAH AKHLAK'),
        ('ARB', 'BAHASA ARAB'),
        ('FKH', 'FIKIH'),
        ('QRD', 'QURDIST'),
        ('SKI', 'SKI'),
        ('BTQ', 'BTQ'),
        ('PAS', 'IPAS'),
        ('IPA', 'IPA'),
        ('IPS', 'IPS'),
        ('PAF', 'IPA FISIKA'),
        ('PAK', 'IPA KIMIA'),
        ('PAB', 'IPA BIOLOGI'),
        ('PSE', 'IPS EKONOMI'),
        ('PSG', 'IPS GEOGRAFI'),
        ('PSS', 'IPS SEJARAH'),
        ('PSO', 'IPS SOSIOLOGI'),
        ('PML', 'IPA MATEMATIKA LANJUTAN'),
        ('PIF', 'IPA INFORMATIKA'),
        ('PSA', 'IPS ANTROPOLOGI'),
        ('BLG', 'BIOLOGI'),
        ('FSK', 'FISIKA'),
        ('KMI', 'KIMIA'),
        ('EKM', 'EKONOMI'),
        ('GGF', 'GEOGRAFI'),
        ('SSL', 'SOSIOLOGI'),
        ('TM1', 'TEMA 1'),
        ('TM2', 'TEMA 2'),
        ('TM3', 'TEMA 3'),
        ('TM4', 'TEMA 4'),
        ('TM5', 'TEMA 5'),
        ('TM6', 'TEMA 6'),
        ('TM7', 'TEMA 7'),
        ('TM8', 'TEMA 8'),
        ('TM9', 'TEMA 9'),
        ('PRK', 'PRAKARYA'),
        ('PKK', 'PKK'),
        ('BKO', 'BIMBINGAN KONSELING'),
        ('PKW', 'PKWU'),
        ('SBD', 'SENI BUDAYA');";

        DB::unprepared($mapel);
    }
}
