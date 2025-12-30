<?php
namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Helper
{
    protected function beginTransaction()
    {
        DB::beginTransaction();
    }

    protected function commitTransaction()
    {
        DB::commit();
    }

    protected function generateFileName(UploadedFile $file, string $fileName = '') :string
    {
        $fileName = empty($fileName) ? 'FILE_' . date('Ymdhis') : $fileName;

        return $fileName . '.' . $file->extension();
    }

    protected function loadPdf($filePath, $data, $paperSize = ['paper' => 'a4', 'orientation' => 'potrait'])
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('generate/pdf/' . $filePath, compact('data'));
        $pdf->setPaper($paperSize['paper'], $paperSize['orientation']);

        return $pdf;
    }

    protected function pdfDownload($filePath, $data, $title = 'no-title.pdf', $paperSize = ['paper' => 'a4', 'orientation' => 'potrait'])
    {
        return self::loadPdf($filePath, $data, $title, $paperSize)->download($title);
    }

    protected function pdfView($filePath, $data, $title = 'no-title.pdf', $paperSize = ['paper' => 'a4', 'orientation' => 'potrait'])
    {
        return self::loadPdf($filePath, $data, $title, $paperSize)->stream($title);
    }

    protected function print($filePath, $data)
    {
        $view = (string) view('generate/' . $filePath, compact('data'));
        $view .= '<script>window.print();</script>';

        return $view;
    }

    protected function rollbackTransaction()
    {
        DB::rollBack();
    }
}

