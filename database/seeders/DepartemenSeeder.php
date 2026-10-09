<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\ProgramKerja;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        // Struktur departemen yang benar (5 departemen)
        $departemenData = [
            [
                'nama' => 'Kaderisasi dan Organisasi',
                'kepanjangan' => null,
                'deskripsi' => 'Departemen yang membina kader pengurus, menjaga tata kelola organisasi, serta mengawal regenerasi kepengurusan BEM SAINTEK.',
                'icon' => '🌱',
                'tujuan' => "Membina kader pengurus yang berintegritas dan profesional\nMenjaga tata kelola dan regenerasi organisasi\nMenyelenggarakan pengkaderan dan upgrading pengurus",
            ],
            [
                'nama' => 'Pengembangan Nalar dan Bakat',
                'kepanjangan' => null,
                'deskripsi' => 'Departemen yang mewadahi pengembangan penalaran ilmiah, kreativitas, serta minat dan bakat mahasiswa Saintek.',
                'icon' => '💡',
                'tujuan' => "Mengembangkan budaya riset dan penalaran ilmiah mahasiswa\nMemfasilitasi penyaluran minat dan bakat mahasiswa\nMendorong prestasi mahasiswa di kompetisi dan karya ilmiah",
            ],
            [
                'nama' => 'Informasi dan Komunikasi',
                'kepanjangan' => null,
                'deskripsi' => 'Departemen yang mengelola arus informasi, media, dan komunikasi publik BEM SAINTEK.',
                'icon' => '📣',
                'tujuan' => "Mengelola media sosial dan kanal informasi sebagai wajah organisasi\nMendokumentasikan dan mempublikasikan setiap kegiatan\nMenyajikan informasi yang cepat, akurat, dan menarik",
            ],
            [
                'nama' => 'Ekonomi',
                'kepanjangan' => null,
                'deskripsi' => 'Departemen yang mengelola kemandirian ekonomi organisasi serta pembinaan kewirausahaan mahasiswa.',
                'icon' => '💰',
                'tujuan' => "Membangun kemandirian ekonomi organisasi\nMembina jiwa kewirausahaan mahasiswa Saintek\nMenggalang pendanaan dan sponsorship untuk keberlanjutan proker",
            ],
            [
                'nama' => 'Relasi Mahasiswa',
                'kepanjangan' => null,
                'deskripsi' => 'Departemen yang menjalin dan merawat hubungan dengan mahasiswa, HIMA, serta pihak eksternal.',
                'icon' => '🤝',
                'tujuan' => "Menjalin relasi harmonis dengan HIMA dan mahasiswa\nMembangun jejaring dengan pihak eksternal dan mitra\nMenampung dan menyalurkan aspirasi mahasiswa",
            ],
        ];

        foreach ($departemenData as $data) {
            Departemen::updateOrCreate(['nama' => $data['nama']], $data);
        }

        // Pindahkan proker data asli milik 'INFOKOM' lama ke 'Informasi dan Komunikasi' yang baru
        $baru = Departemen::where('nama', 'Informasi dan Komunikasi')->first();
        $lamaInfokom = Departemen::where('nama', 'INFOKOM')->first();
        if ($baru && $lamaInfokom && $baru->id !== $lamaInfokom->id) {
            ProgramKerja::where('departemen_id', $lamaInfokom->id)
                ->update(['departemen_id' => $baru->id]);
        }

        // Hapus proker contoh bawaan seeder lama (terikat departemen yang salah)
        ProgramKerja::whereIn('judul', [
            'Lomba Olimpiade Sains',
            'Bakti Sosial',
            'Rohis Virtual',
            'Turnamen E-Sports',
            'Sainpreneur',
            'Seminar Nasional',
        ])->delete();

        // Hapus departemen lama yang salah
        Departemen::whereIn('nama', [
            'PSDM',
            'PSDO',
            'Dikti',
            'Eksternal',
            'Internal',
            'Medinfo',
            'INFOKOM',
        ])->delete();
    }
}
