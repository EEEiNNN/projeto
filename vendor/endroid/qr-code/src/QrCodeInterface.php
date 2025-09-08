<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace Endroid\QrCode;

use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Encoding\EncodingInterface;

interface QrCodeInterface
{
    public function getData(): string;

    public function getEncoding(): EncodingInterface;

    public function getErrorCorrectionLevel(): ErrorCorrectionLevel;
=======
/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode;

interface QrCodeInterface
{
    public function getText(): string;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb

    public function getSize(): int;

    public function getMargin(): int;

<<<<<<< HEAD
    public function getRoundBlockSizeMode(): RoundBlockSizeMode;

    public function getForegroundColor(): ColorInterface;

    public function getBackgroundColor(): ColorInterface;
=======
    /** @return array<int> */
    public function getForegroundColor(): array;

    /** @return array<int> */
    public function getBackgroundColor(): array;

    public function getEncoding(): string;

    public function getRoundBlockSize(): bool;

    public function getErrorCorrectionLevel(): ErrorCorrectionLevel;

    public function getLogoPath(): ?string;

    public function getLogoWidth(): ?int;

    public function getLogoHeight(): ?int;

    public function getLabel(): ?string;

    public function getLabelFontPath(): string;

    public function getLabelFontSize(): int;

    public function getLabelAlignment(): string;

    /** @return array<int> */
    public function getLabelMargin(): array;

    public function getValidateResult(): bool;

    /** @return array<mixed> */
    public function getWriterOptions(): array;

    public function getContentType(): string;

    public function setWriterRegistry(WriterRegistryInterface $writerRegistry): void;

    public function writeString(): string;

    public function writeDataUri(): string;

    public function writeFile(string $path): void;

    /** @return array<mixed> */
    public function getData(): array;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
}
