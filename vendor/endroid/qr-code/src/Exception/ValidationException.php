<?php

declare(strict_types=1);

<<<<<<< HEAD
namespace Endroid\QrCode\Exception;

final class ValidationException extends \Exception
{
    public static function createForUnsupportedWriter(string $writerClass): self
    {
        return new self(sprintf('Unable to validate the result: "%s" does not support validation', $writerClass));
    }

    public static function createForMissingPackage(string $packageName): self
    {
        return new self(sprintf('Please install "%s" or disable image validation', $packageName));
    }

    public static function createForInvalidData(string $expectedData, string $actualData): self
    {
        return new self('The validation reader read "'.$actualData.'" instead of "'.$expectedData.'". Adjust your parameters to increase readability or disable validation.');
    }
=======
/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\QrCode\Exception;

class ValidationException extends QrCodeException
{
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
}
