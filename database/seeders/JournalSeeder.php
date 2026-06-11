<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Journal;
use App\Models\JournalItem;
use App\Models\Member;
use App\Models\MemberContribution;
use App\Models\DataDictionary;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        // Temukan ID Akun
        $kasKecil = Account::where('code', '111110')->first()->id ?? null;
        $brankas = Account::where('code', '111120')->first()->id ?? null;
        $bankMandiri = Account::where('code', '111210')->first()->id ?? null;
        $tanggukRutin = Account::where('code', '311101')->first()->id ?? null;
        $perpuluhan = Account::where('code', '314001')->first()->id ?? null;
        $bungaBank = Account::where('code', '318002')->first()->id ?? null;
        $honorPendeta = Account::where('code', '421100')->first()->id ?? null;
        $biayaListrik = Account::where('code', '430400')->first()->id ?? null;

        // Ambil Jenis Kontribusi
        $tithingType = DataDictionary::where('category', 'contribution_type')
            ->where('code', 'persembahan-persepuluhan')
            ->first()->id ?? null;

        // Ambil Member Yohanis Ndun untuk Kontribusi
        $yohanis = Member::where('first_name', 'Yohanis')->first();

        // Ambil User Admin
        $adminUser = \App\Models\User::where('email', 'admin@sig.test')->first();
        $adminId = $adminUser ? $adminUser->id : 1;

        // 1. Weekly Offering (Kolekte Mingguan)
        if ($brankas && $tanggukRutin) {
            $j1 = Journal::create([
                'transaction_date' => '2026-06-01',
                'description' => 'Penerimaan Kolekte Tangguk 1 Rutin Kebaktian Minggu I Juni 2026',
                'reference_number' => 'KM-01',
                'created_by' => $adminId,
            ]);

            JournalItem::create([
                'journal_id' => $j1->id,
                'account_id' => $brankas,
                'debit' => 2450000.00,
                'credit' => 0.00,
            ]);

            JournalItem::create([
                'journal_id' => $j1->id,
                'account_id' => $tanggukRutin,
                'debit' => 0.00,
                'credit' => 2450000.00,
            ]);
        }

        // 2. Member Tithing (Persepuluhan Anggota)
        if ($brankas && $perpuluhan) {
            $j2 = Journal::create([
                'transaction_date' => '2026-06-02',
                'description' => 'Penerimaan Persembahan Persepuluhan dari Anggota Jemaat Yohanis Ndun',
                'reference_number' => 'PA-02',
                'created_by' => $adminId,
            ]);

            JournalItem::create([
                'journal_id' => $j2->id,
                'account_id' => $brankas,
                'debit' => 550000.00,
                'credit' => 0.00,
            ]);

            JournalItem::create([
                'journal_id' => $j2->id,
                'account_id' => $perpuluhan,
                'debit' => 0.00,
                'credit' => 550000.00,
            ]);

            // Hubungkan dengan MemberContribution
            if ($yohanis && $tithingType) {
                MemberContribution::create([
                    'journal_id' => $j2->id,
                    'member_id' => $yohanis->id,
                    'contribution_type_id' => $tithingType,
                    'amount' => 550000.00,
                ]);
            }
        }

        // 3. Honorarium Pendeta
        if ($honorPendeta && $bankMandiri) {
            $j3 = Journal::create([
                'transaction_date' => '2026-06-03',
                'description' => 'Pembayaran Honorarium Pendeta Bulan Juni 2026',
                'reference_number' => 'HP-03',
                'created_by' => $adminId,
            ]);

            JournalItem::create([
                'journal_id' => $j3->id,
                'account_id' => $honorPendeta,
                'debit' => 3500000.00,
                'credit' => 0.00,
            ]);

            JournalItem::create([
                'journal_id' => $j3->id,
                'account_id' => $bankMandiri,
                'debit' => 0.00,
                'credit' => 3500000.00,
            ]);
        }

        // 4. Electricity Bill
        if ($biayaListrik && $kasKecil) {
            $j4 = Journal::create([
                'transaction_date' => '2026-06-04',
                'description' => 'Pembayaran Biaya Listrik Kantor Gereja Bulan Juni 2026',
                'reference_number' => 'OP-04',
                'created_by' => $adminId,
            ]);

            JournalItem::create([
                'journal_id' => $j4->id,
                'account_id' => $biayaListrik,
                'debit' => 450000.00,
                'credit' => 0.00,
            ]);

            JournalItem::create([
                'journal_id' => $j4->id,
                'account_id' => $kasKecil,
                'debit' => 0.00,
                'credit' => 450000.00,
            ]);
        }

        // 5. Bank Interest
        if ($bankMandiri && $bungaBank) {
            $j5 = Journal::create([
                'transaction_date' => '2026-06-05',
                'description' => 'Penerimaan Bunga Bank Mandiri Juni 2026',
                'reference_number' => 'BI-05',
                'created_by' => $adminId,
            ]);

            JournalItem::create([
                'journal_id' => $j5->id,
                'account_id' => $bankMandiri,
                'debit' => 125000.00,
                'credit' => 0.00,
            ]);

            JournalItem::create([
                'journal_id' => $j5->id,
                'account_id' => $bungaBank,
                'debit' => 0.00,
                'credit' => 125000.00,
            ]);
        }
    }
}
