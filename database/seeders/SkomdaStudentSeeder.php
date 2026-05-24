<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\SkomdaStudent;

class SkomdaStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/siswa.xlsx');

        $spreadsheet = IOFactory::load($path);


        // Proses data untuk kelas X
        $grade10 = $spreadsheet->getSheetByName('KELAS X');
        $rows10 = $grade10->toArray();
        array_shift($rows10);

        foreach ($rows10 as $row) {
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            $major = $row[1] === 'X SIJA 1' || $row[1] === 'X SIJA 2' ? 'SIJA' : 'TJAT';

            SkomdaStudent::updateOrCreate(
                ['nis' => $row[3]],
                [
                    'name' => $row[2],
                    'email' => $row[7] ?? null,
                    'phone' => null,
                    'class' => $row[1],
                    'major' => $major,
                ]
            );
        }

        // Proses data untuk kelas XI
        $grade11 = $spreadsheet->getSheetByName('KELAS XI');
        $rows11 = $grade11->toArray();

        array_shift($rows11);

        foreach ($rows11 as $row) {
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            $major = $row[1] === 'XI SIJA 1' || $row[1] === 'XI SIJA 2' ? 'SIJA' : 'TJAT';

            SkomdaStudent::updateOrCreate(
                ['nis' => $row[3]],
                [
                    'name' => $row[2],
                    'email' => $row[7] ?? null,
                    'phone' => null,
                    'class' => $row[1],
                    'major' => $major,
                ]
            );
        }

        // Proses data untuk kelas XII
        $grade12 = $spreadsheet->getSheetByName('KELAS XII');
        $rows12 = $grade12->toArray();
        array_shift($rows12);

        foreach ($rows12 as $row) {
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            $major = $row[0] === 'XII SIJA 1' || $row[0] === 'XII SIJA 2' ? 'SIJA' : 'TJAT';

            SkomdaStudent::updateOrCreate(
                ['nis' => $row[2]],
                [
                    'name' => $row[1],
                    'email' => $row[6] ?? null,
                    'phone' => null,
                    'class' => $row[0],
                    'major' => $major,
                ]
            );
        }

        // Proses data untuk kelas XIII
        $grade13 = $spreadsheet->getSheetByName('KELAS XIII');
        $rows13 = $grade13->toArray();
        array_shift($rows13);

        foreach ($rows13 as $row) {
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            $major = $row[1] === 'XIII SIJA 1' || $row[1] === 'XIII SIJA 2' ? 'SIJA' : 'TJAT';

            SkomdaStudent::updateOrCreate(
                ['nis' => $row[3]],
                [
                    'name' => $row[2],
                    'email' => $row[7] ?? null,
                    'phone' => null,
                    'class' => $row[1],
                    'major' => $major,
                ]
            );
        }
    }
}
