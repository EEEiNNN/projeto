<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\EpsResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final readonly class EpsWriter implements WriterInterface
{
    public const DECIMAL_PRECISION = 10;

    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []): ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $lines = [
            '%!PS-Adobe-3.0 EPSF-3.0',
            '%%BoundingBox: 0 0 '.$matrix->getOuterSize().' '.$matrix->getOuterSize(),
            '/F { rectfill } def',
            number_format($qrCode->getBackgroundColor()->getRed() / 100, 2, '.', ',').' '.number_format($qrCode->getBackgroundColor()->getGreen() / 100, 2, '.', ',').' '.number_format($qrCode->getBackgroundColor()->getBlue() / 100, 2, '.', ',').' setrgbcolor',
            '0 0 '.$matrix->getOuterSize().' '.$matrix->getOuterSize().' F',
            number_format($qrCode->getForegroundColor()->getRed() / 100, 2, '.', ',').' '.number_format($qrCode->getForegroundColor()->getGreen() / 100, 2, '.', ',').' '.number_format($qrCode->getForegroundColor()->getBlue() / 100, 2, '.', ',').' setrgbcolor',
        ];

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($matrix->getBlockCount() - 1 - $rowIndex, $columnIndex)) {
                    $x = $matrix->getMarginLeft() + $matrix->getBlockSize() * $columnIndex;
                    $y = $matrix->getMarginLeft() + $matrix->getBlockSize() * $rowIndex;
                    $lines[] = number_format($x, self::DECIMAL_PRECISION, '.', '').' '.number_format($y, self::DECIMAL_PRECISION, '.', '').' '.number_format($matrix->getBlockSize(), self::DECIMAL_PRECISION, '.', '').' '.number_format($matrix->getBlockSize(), self::DECIMAL_PRECISION, '.', '').' F';
=======
/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;

class EpsWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {
        $data = $qrCode->getData();

        $epsData = [];
        $epsData[] = '%!PS-Adobe-3.0 EPSF-3.0';
        $epsData[] = '%%BoundingBox: 0 0 '.$data['outer_width'].' '.$data['outer_height'];
        $epsData[] = '/F { rectfill } def';
        $epsData[] = number_format($qrCode->getBackgroundColor()['r'] / 100, 2, '.', ',').' '.number_format($qrCode->getBackgroundColor()['g'] / 100, 2, '.', ',').' '.number_format($qrCode->getBackgroundColor()['b'] / 100, 2, '.', ',').' setrgbcolor';
        $epsData[] = '0 0 '.$data['outer_width'].' '.$data['outer_height'].' F';
        $epsData[] = number_format($qrCode->getForegroundColor()['r'] / 100, 2, '.', ',').' '.number_format($qrCode->getForegroundColor()['g'] / 100, 2, '.', ',').' '.number_format($qrCode->getForegroundColor()['b'] / 100, 2, '.', ',').' setrgbcolor';

        // Please note an EPS has a reversed Y axis compared to PNG and SVG
        $data['matrix'] = array_reverse($data['matrix']);
        foreach ($data['matrix'] as $row => $values) {
            foreach ($values as $column => $value) {
                if (1 === $value) {
                    $x = $data['margin_left'] + $data['block_size'] * $column;
                    $y = $data['margin_left'] + $data['block_size'] * $row;
                    $epsData[] = $x.' '.$y.' '.$data['block_size'].' '.$data['block_size'].' F';
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
                }
            }
        }

<<<<<<< HEAD
        return new EpsResult($matrix, $lines);
=======
        return implode("\n", $epsData);
    }

    public static function getContentType(): string
    {
        return 'image/eps';
    }

    public static function getSupportedExtensions(): array
    {
        return ['eps'];
    }

    public function getName(): string
    {
        return 'eps';
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
    }
}
