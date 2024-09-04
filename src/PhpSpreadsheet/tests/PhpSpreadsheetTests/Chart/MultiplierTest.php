<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class MultiplierTest extends TestCase
{
    public function testMultiplier(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray(
            [
                ['', 2010, 2011, 2012],
                ['Q1', 12, 15, 21],
                ['Q2', 56, 73, 86],
                ['Q3', 52, 61, 69],
                ['Q4', 30, 32, 0],
            ]
        );

        // Set the Labels for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), // 2010
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$C$1', null, 1), // 2011
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$D$1', null, 1), // 2012
        ];
        // Set the X-Axis Labels
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$5', null, 4), // Q1 to Q4
        ];
        // Set the Data values for each data series we want to plot
        //     Datatype
        //     Cell reference for data
        //     Format Code
        //     Number of datapoints in series
        //     Data values
        //     Data Marker
        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$C$2:$C$5', null, 4),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$5', null, 4),
        ];

        // Build the dataseries
        $series = new DataSeries(
            DataSeries::TYPE_AREACHART, // plotType
            DataSeries::GROUPING_PERCENT_STACKED, // plotGrouping
            range(0, count($dataSeriesValues) - 1), // plotOrder
            $dataSeriesLabels, // plotLabel
            $xAxisTickValues, // plotCategory
            $dataSeriesValues          // plotValues
        );

        // Set the series in the plot area
        $plotArea = new PlotArea(null, [$series]);
        // Set the chart legend
        $legend = new ChartLegend(ChartLegend::POSITION_TOPRIGHT, null, false);

        $title = new Title('Test %age-Stacked Area Chart');
        $yAxisLabel = new Title('Value ($k)');

        // Create the chart
        $chart = new Chart(
            'chart1', // name
            $title, // title
            $legend, // legend
            $plotArea, // plotArea
            true, // plotVisibleOnly
            DataSeries::EMPTY_AS_GAP, // displayBlanksAs
            null, // xAxisLabel
            $yAxisLabel  // yAxisLabel
        );
        $xAxis = $chart->getChartAxisX();
        $expectedX = [
            'effect' => 'outerShdw',
            'algn' => 'bl',
            'blur' => 6,
            'direction' => 315,
            'distance' => 3,
            'rotWithShape' => 0,
            'size' => [
                'sx' => null,
                'sy' => 254,
                'kx' => -94,
                'ky' => null,
            ],
            'color' => [
                'type' => ChartColor::EXCEL_COLOR_TYPE_RGB,
                'value' => 'FF0000',
                'alpha' => 20,
            ],
        ];
        $expectedXmlX = [
            '<a:outerShdw ',
            ' algn="bl"',
            ' blurRad="76200"',
            ' dir="18900000"',
            ' dist="38100"',
            ' rotWithShape="0"',
            ' sy="25400000"',
            ' kx="-5640000"',
            '<a:srgbClr val="FF0000">',
            '<a:alpha val="80000"/>',
        ];
        $expectedXmlNoX = [
            ' sx=',
            ' ky=',
        ];
        foreach ($expectedX as $key => $value) {
            $xAxis->setShadowProperty($key, $value);
        }
        // Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition('A7');
        $chart->setBottomRightPosition('H20');

        // Add the chart to the worksheet
        $worksheet->addChart($chart);

        $writer = new XlsxWriter($spreadsheet);
        $writer->setIncludeCharts(true);
        $writerChart = new XlsxWriter\Chart($writer);
        $data = $writerChart->writeChart($chart);

        // confirm that file contains expected tags
        foreach ($expectedXmlX as $expected) {
            self::assertSame(1, substr_count($data, $expected), $expected);
        }
        foreach ($expectedXmlNoX as $expected) {
            self::assertSame(0, substr_count($data, $expected), $expected);
        }
        $spreadsheet->disconnectWorksheets();
    }
}
