<?php

namespace App\Service\IteExport;

use App\Entity\Enums\IteQuality;
use App\Entity\Enums\IteType;
use App\Entity\Ite;
use App\Repository\IteRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IteExportService
{
    public function __construct(
        private readonly IteRepository $iteRepository,
    ) {
    }

    /**
     * Exporta ITEs filtrados por tipo (National o International).
     * Devuelve un StreamedResponse listo para el navegador.
     */
    public function export(IteType $type): StreamedResponse
    {
        $ites = (array) $this->iteRepository->findByType($type); // Ver método del repo abajo

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(IteType::National === $type ? 'ITE Nacional' : 'ITE Internacional');

        $isNational = IteType::National === $type;
        $headers = $this->getHeaders($isNational);

        // Cabeceras
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col.'1', $header);
            ++$col;
        }

        // Estilo cabecera
        $lastCol = chr(64 + count($headers)); // A, B, C...
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E79'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Datos
        $row = 2;
        /** @var Ite $ite */
        foreach ($ites as $ite) {
            $data = $this->mapIteToRow($ite, $isNational);
            $col = 'A';
            foreach ($data as $value) {
                $sheet->setCellValue($col.$row, $value);
                ++$col;
            }
            ++$row;
        }

        // Auto-size columnas
        foreach (range('A', $lastCol) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Formato numérico para Min/Max
        $sheet->getStyle('B2:C'.($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $filename = sprintf(
            'ite_%s_%s.xlsx',
            $isNational ? 'nacional' : 'internacional',
            (new \DateTimeImmutable())->format('Y-m-d_H-i')
        );

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename.'"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * @return string[]
     */
    private function getHeaders(bool $isNational): array
    {
        $headers = [
            'Calidad',
            'Min',
            'Max',
            'Unidad de Medida',
            'Fuente',
            'Año de referencia',
        ];

        if (!$isNational) {
            $headers[] = 'Ciudad';
            $headers[] = 'País';
        }

        $headers[] = 'Tipo de proyecto';

        // Opcionales útiles (quítalos si no los quieres)
        $headers[] = 'Comentario';
        $headers[] = 'Acceso a fuente';

        return $headers;
    }

    /**
     * @return array<mixed>
     */
    private function mapIteToRow(Ite $ite, bool $isNational): array
    {
        $row = [
            IteQuality::getLabelFrom($ite->getQuality()),
            $ite->getMin(),
            $ite->getMax(),
            $ite->getMeasurementUnit()?->getCode() ?? '', // Ajusta al getter real
            $ite->getSource()?->getName() ?? '',
            $ite->getYearReference(),
        ];

        if (!$isNational) {
            $city = $ite->getCity();
            $row[] = $city?->getName() ?? '';
            $row[] = $city?->getCountry()?->getName() ?? '';
        }

        $row[] = $ite->getProjectType()?->getName() ?? '';

        // Campos extra
        $row[] = $ite->getComment() ?? '';
        $row[] = $ite->getSourceAccess() ?? '';

        return $row;
    }
}
