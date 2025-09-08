<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;

interface WriterInterface
{
    /** @param array<string, mixed> $options */
    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []): ResultInterface;
=======
/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;

interface WriterInterface
{
    public function writeString(QrCodeInterface $qrCode): string;

    public function writeDataUri(QrCodeInterface $qrCode): string;

    public function writeFile(QrCodeInterface $qrCode, string $path): void;

    public static function getContentType(): string;

    public static function supportsExtension(string $extension): bool;

    /** @return array<string> */
    public static function getSupportedExtensions(): array;

    public function getName(): string;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
}
