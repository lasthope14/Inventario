<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Inventario;
use Illuminate\Support\Facades\DB;

class DocumentosDuplicadosExport
{
    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Configurar encabezados - AGREGANDO SERIAL
        $headers = [
            'A1' => 'CODIGO_ELEMENTO',
            'B1' => 'SERIAL',
            'C1' => 'NOMBRE_ELEMENTO', 
            'D1' => 'CATEGORIA',
            'E1' => 'NOMBRE_DOCUMENTO',
            'F1' => 'ARCHIVO_ACTUAL',
            'G1' => 'ARCHIVO_DOCUMENTO',
            'H1' => 'OBSERVACIONES'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // Estilo para encabezados
        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:H1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E3F2FD');
        
        // Configurar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(20); // CODIGO_ELEMENTO
        $sheet->getColumnDimension('B')->setWidth(20); // SERIAL
        $sheet->getColumnDimension('C')->setWidth(30); // NOMBRE_ELEMENTO
        $sheet->getColumnDimension('D')->setWidth(20); // CATEGORIA
        $sheet->getColumnDimension('E')->setWidth(25); // NOMBRE_DOCUMENTO
        $sheet->getColumnDimension('F')->setWidth(30); // ARCHIVO_ACTUAL
        $sheet->getColumnDimension('G')->setWidth(30); // ARCHIVO_DOCUMENTO
        $sheet->getColumnDimension('H')->setWidth(40); // OBSERVACIONES
        
        // Obtener datos
        $data = $this->getData();
        
        // Llenar datos
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item['codigo_elemento']);
            $sheet->setCellValue('B' . $row, $item['serial']);
            $sheet->setCellValue('C' . $row, $item['nombre_elemento']);
            $sheet->setCellValue('D' . $row, $item['categoria']);
            $sheet->setCellValue('E' . $row, $item['nombre_documento']);
            $sheet->setCellValue('F' . $row, $item['archivo_actual']);
            $sheet->setCellValue('G' . $row, ''); // Campo vacío para completar
            $sheet->setCellValue('H' . $row, ''); // Campo vacío para observaciones
            $row++;
        }
        
        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator('Sistema de Inventario Hidroobras')
            ->setTitle('Template Documentos Duplicados')
            ->setDescription('Plantilla para importación masiva de documentos duplicados');
        
        return $spreadsheet;
    }
    
    private function getData()
    {
        // Obtener elementos con documentos duplicados
        $duplicados = DB::table('documentos')
            ->select('nombre', DB::raw('COUNT(*) as total'))
            ->groupBy('nombre')
            ->having('total', '>', 1)
            ->pluck('nombre');

        $elementos = Inventario::whereHas('documentos', function($query) use ($duplicados) {
            $query->whereIn('nombre', $duplicados);
        })
        ->with(['categoria', 'documentos' => function($query) use ($duplicados) {
            $query->whereIn('nombre', $duplicados);
        }])
        ->get();

        $data = [];
        
        foreach ($elementos as $elemento) {
            foreach ($elemento->documentos as $documento) {
                if ($duplicados->contains($documento->nombre)) {
                    $data[] = [
                        'codigo_elemento' => $elemento->codigo_unico,
                        'serial' => $elemento->numero_serie ?? 'N/A',
                        'nombre_elemento' => $elemento->nombre,
                        'categoria' => $elemento->categoria->nombre,
                        'nombre_documento' => $documento->nombre,
                        'archivo_actual' => $documento->archivo ?? $documento->ruta,
                    ];
                }
            }
        }

        return $data;
    }
    
    public function download($filename = 'documentos_duplicados_template.xlsx')
    {
        $spreadsheet = $this->export();
        
        // Crear writer
        $writer = new Xlsx($spreadsheet);
        
        // Configurar headers para descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Escribir a output
        $writer->save('php://output');
        exit;
    }
} 