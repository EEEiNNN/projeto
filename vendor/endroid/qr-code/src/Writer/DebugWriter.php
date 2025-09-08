<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace Endroid\QrCode\Writer;

use Endroid\QrCode\Bacon\MatrixFactory;
use Endroid\QrCode\Label\LabelInterface;
use Endroid\QrCode\Logo\LogoInterface;
use Endroid\QrCode\QrCodeInterface;
use Endroid\QrCode\Writer\Result\DebugResult;
use Endroid\QrCode\Writer\Result\ResultInterface;

final readonly class DebugWriter implements WriterInterface, ValidatingWriterInterface
{
    public function write(QrCodeInterface $qrCode, ?LogoInterface $logo = null, ?LabelInterface $label = null, array $options = []): ResultInterface
    {
        $matrixFactory = new MatrixFactory();
        $matrix = $matrixFactory->create($qrCode);

        return new DebugResult($matrix, $qrCode, $logo, $label, $options);
    }

    public function validateResult(ResultInterface $result, string $expectedData): void
    {
        if (!$result instanceof DebugResult) {
            throw new \Exception('Unable to write logo: instance of DebugResult expected');
        }

        $result->setValidateResult(true);
=======
/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Writer;

use Endroid\QrCode\QrCodeInterface;
use Exception;
use ReflectionClass;

class DebugWriter extends AbstractWriter
{
    public function writeString(QrCodeInterface $qrCode): string
    {
        $data = [];
        $skip = ['getData'];

        $reflectionClass = new ReflectionClass($qrCode);
        foreach ($reflectionClass->getMethods() as $method) {
            $methodName = $method->getShortName();
            if (0 === strpos($methodName, 'get') && 0 == $method->getNumberOfParameters() && !in_array($methodName, $skip)) {
                $value = $qrCode->{$methodName}();
                if (is_array($value) && !is_object(current($value))) {
                    $value = '['.implode(', ', $value).']';
                } elseif (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                } elseif (is_string($value)) {
                    $value = '"'.$value.'"';
                } elseif (is_null($value)) {
                    $value = 'null';
                }
                try {
                    $data[] = $methodName.': '.$value;
                } catch (Exception $exception) {
                }
            }
        }

        $string = implode(" \n", $data);

        return $string;
    }

    public static function getContentType(): string
    {
        return 'text/plain';
    }

    public function getName(): string
    {
        return 'debug';
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
    }
}
