<?php

namespace Database\Seeders;

use App\Models\DataDictionary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DataDictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dictionaries = [
            // 1. Pendidikan
            'education' => [
                'Tidak Sekolah', 'Tidak Lulus SD', 'Tamat SD', 'Tamat SMP', 
                'Tamat SMA/SMK', 'Diploma', 'Setara S1', 'Setara S2', 'Setara S3', 'Professor'
            ],
            
            // 2. Status Rumah
            'house_status' => [
                'Menumpang', 'Sewa / Kontrak', 'Milik Sendiri', 'Lainnya'
            ],

            // 3. Kategori/Kondisi Rumah
            'house_category' => [
                'Darurat', 'Semi Permanen', 'Permanen', 'Lainnya'
            ],

            // 4. Posisi Keluarga
            'family_position' => [
                'Suami', 'Istri', 'Anak', 'Orang Tua', 'Keponakan', 'Saudara', 'Lainnya'
            ],

            // 5. Pekerjaan
            'occupation' => [
                'Buruh', 'Dokter', 'Pendeta', 'Petani', 'Pedagang', 'Pelajar', 'Putus Sekolah', 
                'Pegawai Kontrak / Honorer', 'Pembantu Rumah Tangga', 'PNS', 'TNI/POLRI', 
                'Pensiunan', 'Sopir', 'Wiraswasta', 'Ibu Rumah Tangga', 'Pegawai Swasta', 
                'Tidak Ada', 'Bidan', 'Perawat', 'Apoteker', 'Assisten Apoteker', 'Tukang', 
                'Pegawai Gereja', 'Mahasiswa'
            ],

            // 6. Status Keanggotaan (Tokoh Gereja)
            'membership_status' => [
                'Tidak Menjabat', 'Pendeta', 'Penatua', 'Diaken', 'Jemaat', 'Jemaat Tamu'
            ],

            // 7. Status Sidi
            'sidi_status' => [
                'Belum Katekisasi', 'Katekisasi', 'Sidi'
            ],

            // 8. Jabatan Gereja Struktural
            'church_role' => [
                'Tidak Menjabat', 'Ketua MJ', 'Wakil Ketua MJ', 'Sekretaris MJ', 
                'Wakil Sekretaris', 'Bendahara', 'Wakil Bendahara', 'Badan Pembantu Pelayanan Jemaat', 
                'Unit Pembantu Pelayanan Jemaat', 'Guru Sekolah Minggu', 'Pengajar Katekisasi', 'Ketua Rayon'
            ],

            // 9. Rentang Penghasilan
            'income_range' => [
                '0', '750000', '1500000', '2500000', '5000000', '10000000'
            ],

            // 10. Jenis Persembahan / Kontribusi
            'contribution_type' => [
                'Persembahan Persepuluhan', 'Persembahan Nazar', 'Persembahan Syukur', 'Persembahan Pembangunan'
            ],

            // 11. Jenis Persembahan / Kontribusi
            'worship' => [
                'Ibadah Raya', 'Persekutuan Wilayah', 'Persekutuan Kategorial'
            ],

            // 12. Jenis Persembahan / Kontribusi
            'worship_venue' => [
                'Onsite','Online','Hybrid'
            ],
        ];

        // Eksekusi untuk data dengan logic auto-slug
        foreach ($dictionaries as $category => $labels) {
            foreach ($labels as $index => $label) {
                DataDictionary::updateOrCreate(
                    ['category' => $category, 'label' => $label],
                    [
                        'code' => Str::slug($label),
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }

        // 10. Status Pernikahan (Custom Code untuk Business Logic Filament)
        $maritalStatuses = [
            ['label' => 'Belum Menikah', 'code' => 'single'],
            ['label' => 'Menikah Gereja', 'code' => 'married'],
            ['label' => 'Belum Menikah Gereja', 'code' => 'married-civil'],
            ['label' => 'Janda', 'code' => 'widow'],
            ['label' => 'Duda', 'code' => 'widower'],
        ];

        foreach ($maritalStatuses as $index => $status) {
            DataDictionary::updateOrCreate(
                ['category' => 'marital_status', 'code' => $status['code']], // Kunci di code agar aman
                [
                    'label' => $status['label'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}