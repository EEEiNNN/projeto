<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\BinaryResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final readonly class BinaryWriter implements WriterInterface
{
    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []): ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        return new BinaryResult($matrix);
=======
/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;

class BinaryWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {
        $rows = [];
        $data = $qrCode->getData();
        foreach ($data['matrix'] as $row) {
            $values = '';
            foreach ($row as $value) {
                $values .= $value;
            }
            $rows[] = $values;
        }

        return implode("\n", $rows);
    }

    public static function getContentType(): string
    {
        return 'text/plain';
    }

    public static function getSupportedExtensions(): array
    {
        return ['bin', 'txt'];
    }

    public function getName(): string
    {
        return 'binary';
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
    }
}
