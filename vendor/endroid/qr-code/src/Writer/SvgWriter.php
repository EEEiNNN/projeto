<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\ImageData\LogoImageData;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\Matrix\MatrixInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\Result\SvgResult;

final readonly class SvgWriter implements WriterInterface
{
    public const DECIMAL_PRECISION = 2;
    public const WRITER_OPTION_COMPACT = 'compact';
    public const WRITER_OPTION_BLOCK_ID = 'block_id';
    public const WRITER_OPTION_EXCLUDE_XML_DECLARATION = 'exclude_xml_declaration';
    public const WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT = 'exclude_svg_width_and_height';
    public const WRITER_OPTION_FORCE_XLINK_HREF = 'force_xlink_href';

    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []): ResultInterface
    {
        if (!isset($options[self::WRITER_OPTION_COMPACT])) {
            $options[self::WRITER_OPTION_COMPACT] = true;
        }

        if (!isset($options[self::WRITER_OPTION_BLOCK_ID])) {
            $options[self::WRITER_OPTION_BLOCK_ID] = 'block';
        }

        if (!isset($options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION])) {
            $options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION] = false;
        }

        if (!isset($options[self::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT])) {
            $options[self::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT] = false;
        }

        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        $xml = new \SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
        $xml->addAttribute('version', '1.1');
        if (!$options[self::WRITER_OPTION_EXCLUDE_SVG_WIDTH_AND_HEIGHT]) {
            $xml->addAttribute('width', $matrix->getOuterSize().'px');
            $xml->addAttribute('height', $matrix->getOuterSize().'px');
        }
        $xml->addAttribute('viewBox', '0 0 '.$matrix->getOuterSize().' '.$matrix->getOuterSize());

        $background = $xml->addChild('rect');
        $background->addAttribute('x', '0');
        $background->addAttribute('y', '0');
        $background->addAttribute('width', strval($matrix->getOuterSize()));
        $background->addAttribute('height', strval($matrix->getOuterSize()));
        $background->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getBackgroundColor()->getRed(), $qrCode->getBackgroundColor()->getGreen(), $qrCode->getBackgroundColor()->getBlue()));
        $background->addAttribute('fill-opacity', strval($qrCode->getBackgroundColor()->getOpacity()));

        if ($options[self::WRITER_OPTION_COMPACT]) {
            $this->writePath($xml, $qrCode, $matrix);
        } else {
            $this->writeBlockDefinitions($xml, $qrCode, $matrix, $options);
        }

        $result = new SvgResult($matrix, $xml, boolval($options[self::WRITER_OPTION_EXCLUDE_XML_DECLARATION]));

        if ($logo instanceof LogoInterface) {
            $this->addLogo($logo, $result, $options);
        }

        return $result;
    }

    private function writePath(\SimpleXMLElement $xml, QrCodeInterface $qrCode, MatrixInterface $matrix): void
    {
        $path = '';
        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            $left = $matrix->getMarginLeft();
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    // When we are at the first column or when the previous column was 0 set new left
                    if (0 === $columnIndex || 0 === $matrix->getBlockValue($rowIndex, $columnIndex - 1)) {
                        $left = $matrix->getMarginLeft() + $matrix->getBlockSize() * $columnIndex;
                    }
                    // When we are at the
                    if ($columnIndex === $matrix->getBlockCount() - 1 || 0 === $matrix->getBlockValue($rowIndex, $columnIndex + 1)) {
                        $top = $matrix->getMarginLeft() + $matrix->getBlockSize() * $rowIndex;
                        $bottom = $matrix->getMarginLeft() + $matrix->getBlockSize() * ($rowIndex + 1);
                        $right = $matrix->getMarginLeft() + $matrix->getBlockSize() * ($columnIndex + 1);
                        $path .= 'M'.$this->formatNumber($left).','.$this->formatNumber($top);
                        $path .= 'L'.$this->formatNumber($right).','.$this->formatNumber($top);
                        $path .= 'L'.$this->formatNumber($right).','.$this->formatNumber($bottom);
                        $path .= 'L'.$this->formatNumber($left).','.$this->formatNumber($bottom).'Z';
                    }
=======
/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Exception\GenerateImageException;
use Endroid\QrCode\Exception\MissingLogoHeightException;
use Endroid\QrCode\Exception\ValidationException;
use Endroid\QrCode\QrCodeInterface;
use SimpleXMLElement;

class SvgWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {
        $options = $qrCode->getWriterOptions();

        if ($qrCode->getValidateResult()) {
            throw new ValidationException('Built-in validation reader can not check SVG images: please disable via setValidateResult(false)');
        }

        $data = $qrCode->getData();

        $svg = new SimpleXMLElement('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"/>');
        $svg->addAttribute('version', '1.1');
        $svg->addAttribute('width', $data['outer_width'].'px');
        $svg->addAttribute('height', $data['outer_height'].'px');
        $svg->addAttribute('viewBox', '0 0 '.$data['outer_width'].' '.$data['outer_height']);
        $svg->addChild('defs');

        // Block definition
        $block_id = isset($options['rect_id']) && $options['rect_id'] ? $options['rect_id'] : 'block';
        $blockDefinition = $svg->defs->addChild('rect');
        $blockDefinition->addAttribute('id', $block_id);
        $blockDefinition->addAttribute('width', strval($data['block_size']));
        $blockDefinition->addAttribute('height', strval($data['block_size']));
        $blockDefinition->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getForegroundColor()['r'], $qrCode->getForegroundColor()['g'], $qrCode->getForegroundColor()['b']));
        $blockDefinition->addAttribute('fill-opacity', strval($this->getOpacity($qrCode->getForegroundColor()['a'])));

        // Background
        $background = $svg->addChild('rect');
        $background->addAttribute('x', '0');
        $background->addAttribute('y', '0');
        $background->addAttribute('width', strval($data['outer_width']));
        $background->addAttribute('height', strval($data['outer_height']));
        $background->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getBackgroundColor()['r'], $qrCode->getBackgroundColor()['g'], $qrCode->getBackgroundColor()['b']));
        $background->addAttribute('fill-opacity', strval($this->getOpacity($qrCode->getBackgroundColor()['a'])));

        foreach ($data['matrix'] as $row => $values) {
            foreach ($values as $column => $value) {
                if (1 === $value) {
                    $block = $svg->addChild('use');
                    $block->addAttribute('x', strval($data['margin_left'] + $data['block_size'] * $column));
                    $block->addAttribute('y', strval($data['margin_left'] + $data['block_size'] * $row));
                    $block->addAttribute('xlink:href', '#'.$block_id, 'http://www.w3.org/1999/xlink');
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
                }
            }
        }

<<<<<<< HEAD
        $pathDefinition = $xml->addChild('path');
        $pathDefinition->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getForegroundColor()->getRed(), $qrCode->getForegroundColor()->getGreen(), $qrCode->getForegroundColor()->getBlue()));
        $pathDefinition->addAttribute('fill-opacity', strval($qrCode->getForegroundColor()->getOpacity()));
        $pathDefinition->addAttribute('d', $path);
    }

    /** @param array<string, mixed> $options */
    private function writeBlockDefinitions(\SimpleXMLElement $xml, QrCodeInterface $qrCode, MatrixInterface $matrix, array $options): void
    {
        $xml->addChild('defs');

        $blockDefinition = $xml->defs->addChild('rect');
        $blockDefinition->addAttribute('id', strval($options[self::WRITER_OPTION_BLOCK_ID]));
        $blockDefinition->addAttribute('width', $this->formatNumber($matrix->getBlockSize()));
        $blockDefinition->addAttribute('height', $this->formatNumber($matrix->getBlockSize()));
        $blockDefinition->addAttribute('fill', '#'.sprintf('%02x%02x%02x', $qrCode->getForegroundColor()->getRed(), $qrCode->getForegroundColor()->getGreen(), $qrCode->getForegroundColor()->getBlue()));
        $blockDefinition->addAttribute('fill-opacity', strval($qrCode->getForegroundColor()->getOpacity()));

        for ($rowIndex = 0; $rowIndex < $matrix->getBlockCount(); ++$rowIndex) {
            for ($columnIndex = 0; $columnIndex < $matrix->getBlockCount(); ++$columnIndex) {
                if (1 === $matrix->getBlockValue($rowIndex, $columnIndex)) {
                    $block = $xml->addChild('use');
                    $block->addAttribute('x', $this->formatNumber($matrix->getMarginLeft() + $matrix->getBlockSize() * $columnIndex));
                    $block->addAttribute('y', $this->formatNumber($matrix->getMarginLeft() + $matrix->getBlockSize() * $rowIndex));
                    $block->addAttribute('xlink:href', '#'.$options[self::WRITER_OPTION_BLOCK_ID], 'http://www.w3.org/1999/xlink');
                }
            }
        }
    }

    /** @param array<string, mixed> $options */
    private function addLogo(LogoInterface $logo, SvgResult $result, array $options): void
    {
        if ($logo->getPunchoutBackground()) {
            throw new \Exception('The SVG writer does not support logo punchout background');
        }

        $logoImageData = LogoImageData::createForLogo($logo);

        if (!isset($options[self::WRITER_OPTION_FORCE_XLINK_HREF])) {
            $options[self::WRITER_OPTION_FORCE_XLINK_HREF] = false;
        }

        $xml = $result->getXml();

        /** @var \SimpleXMLElement $xmlAttributes */
        $xmlAttributes = $xml->attributes();

        $x = intval($xmlAttributes->width) / 2 - $logoImageData->getWidth() / 2;
        $y = intval($xmlAttributes->height) / 2 - $logoImageData->getHeight() / 2;

        $imageDefinition = $xml->addChild('image');
        $imageDefinition->addAttribute('x', strval($x));
        $imageDefinition->addAttribute('y', strval($y));
        $imageDefinition->addAttribute('width', strval($logoImageData->getWidth()));
        $imageDefinition->addAttribute('height', strval($logoImageData->getHeight()));
        $imageDefinition->addAttribute('preserveAspectRatio', 'none');

        if ($options[self::WRITER_OPTION_FORCE_XLINK_HREF]) {
            $imageDefinition->addAttribute('xlink:href', $logoImageData->createDataUri(), 'http://www.w3.org/1999/xlink');
        } else {
            $imageDefinition->addAttribute('href', $logoImageData->createDataUri());
        }
    }

    private function formatNumber(float $number): string
    {
        $string = number_format($number, self::DECIMAL_PRECISION, '.', '');
        $string = rtrim($string, '0');

        return rtrim($string, '.');
=======
        $logoPath = $qrCode->getLogoPath();
        if (is_string($logoPath)) {
            $forceXlinkHref = false;
            if (isset($options['force_xlink_href']) && $options['force_xlink_href']) {
                $forceXlinkHref = true;
            }

            $this->addLogo($svg, $data['outer_width'], $data['outer_height'], $logoPath, $qrCode->getLogoWidth(), $qrCode->getLogoHeight(), $forceXlinkHref);
        }

        $xml = $svg->asXML();

        if (!is_string($xml)) {
            throw new GenerateImageException('Unable to save SVG XML');
        }

        if (isset($options['exclude_xml_declaration']) && $options['exclude_xml_declaration']) {
            $xml = str_replace("<?xml version=\"1.0\"?>\n", '', $xml);
        }

        return $xml;
    }

    private function addLogo(SimpleXMLElement $svg, int $imageWidth, int $imageHeight, string $logoPath, int $logoWidth = null, int $logoHeight = null, bool $forceXlinkHref = false): void
    {
        $mimeType = $this->getMimeType($logoPath);
        $imageData = file_get_contents($logoPath);

        if (!is_string($imageData)) {
            throw new GenerateImageException('Unable to read image data: check your logo path');
        }

        if ('image/svg+xml' === $mimeType && (null === $logoHeight || null === $logoWidth)) {
            throw new MissingLogoHeightException('SVG Logos require an explicit height set via setLogoSize($width, $height)');
        }

        if (null === $logoHeight || null === $logoWidth) {
            $logoImage = imagecreatefromstring(strval($imageData));

            if (!$logoImage) {
                throw new GenerateImageException('Unable to generate image: check your GD installation or logo path');
            }

            /** @var mixed $logoImage */
            $logoSourceWidth = imagesx($logoImage);
            $logoSourceHeight = imagesy($logoImage);

            if (PHP_VERSION_ID < 80000) {
                imagedestroy($logoImage);
            }

            if (null === $logoWidth) {
                $logoWidth = $logoSourceWidth;
            }

            if (null === $logoHeight) {
                $aspectRatio = $logoWidth / $logoSourceWidth;
                $logoHeight = intval($logoSourceHeight * $aspectRatio);
            }
        }

        $logoX = $imageWidth / 2 - $logoWidth / 2;
        $logoY = $imageHeight / 2 - $logoHeight / 2;

        $imageDefinition = $svg->addChild('image');
        $imageDefinition->addAttribute('x', strval($logoX));
        $imageDefinition->addAttribute('y', strval($logoY));
        $imageDefinition->addAttribute('width', strval($logoWidth));
        $imageDefinition->addAttribute('height', strval($logoHeight));
        $imageDefinition->addAttribute('preserveAspectRatio', 'none');

        // xlink:href is actually deprecated, but still required when placing the qr code in a pdf.
        // SimpleXML strips out the xlink part by using addAttribute(), so it must be set directly.
        if ($forceXlinkHref) {
            $imageDefinition['xlink:href'] = 'data:'.$mimeType.';base64,'.base64_encode($imageData);
        } else {
            $imageDefinition->addAttribute('href', 'data:'.$mimeType.';base64,'.base64_encode($imageData));
        }
    }

    private function getOpacity(int $alpha): float
    {
        $opacity = 1 - $alpha / 127;

        return $opacity;
    }

    public static function getContentType(): string
    {
        return 'image/svg+xml';
    }

    public static function getSupportedExtensions(): array
    {
        return ['svg'];
    }

    public function getName(): string
    {
        return 'svg';
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
    }
}
