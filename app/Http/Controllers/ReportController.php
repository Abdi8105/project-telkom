<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function form()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $path = $request->file('image')->store('uploads', 'public');
        session(['uploaded_file' => $path]);

        return back()->with('uploaded', true);
    }

    public function generateWord(Request $request)
    {
        $lokasi = $request->query('lokasi');
        $file = session('uploaded_file');

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Header Logo
        $header = $section->addTable();
        $header->addRow();
        $header->addCell(4500)->addImage(public_path('images/logo-kiri.png'), [
            'width' => 100,
            'height' => 60,
            'alignment' => Jc::LEFT
        ]);
        $header->addCell(4500)->addImage(public_path('images/logo-kanan.png'), [
            'width' => 100,
            'height' => 60,
            'alignment' => Jc::RIGHT
        ]);

        $section->addTextBreak(1);

        // Judul
        $section->addText('EVIDENCE PEKERJAAN', ['bold' => true, 'size' => 16], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        // Tabel Info Proyek
        $info = $section->addTable();

        $addBoldRow = function($table, $label, $value) {
            $table->addRow();
            $table->addCell(2000)->addText($label, ['bold' => true]);

            $cell = $table->addCell(8000);
            $textRun = $cell->addTextRun();
            $textRun->addText(': ', ['bold' => true]);
            $textRun->addText($value, ['bold' => true]);
        };

        $addBoldRow($info, 'PROYEK', 'PENGADAAN PEKERJAAN OUTSIDE PLANT FIBER TO THE HOME (OSP - FTTH) TAHUN 2025 TELKOM REGIONAL IV KALIMANTAN');
        $addBoldRow($info, 'KONTRAK', '...................................................');
        $addBoldRow($info, 'AREA', 'BANJARMASIN');
        $addBoldRow($info, 'LOKASI', strtoupper($lokasi));
        $addBoldRow($info, 'PELAKSANA', 'PT. TELKOM AKSES');

        $section->addTextBreak(1);

        // Gambar Evidence
        if ($file) {
            $section->addImage(public_path('storage/' . $file), [
                'width' => 400,
                'height' => 300,
                'alignment' => Jc::CENTER
            ]);
        }

        // Simpan File Word
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $filename = 'laporan_evidence.docx';
        $path = storage_path($filename);

        $writer->save($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function generatePDF(Request $request)
    {
        $lokasi = $request->query('lokasi');
        $file = session('uploaded_file');

        $data = [
            'lokasi' => $lokasi,
            'file' => $file,
        ];

        $pdf = Pdf::loadView('pdf_report', compact('data'));
        return $pdf->download('laporan_evidence.pdf');
    }
}
