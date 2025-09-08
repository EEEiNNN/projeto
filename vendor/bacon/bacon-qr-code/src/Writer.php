<?php
declare(strict_types = 1);

namespace BaconQrCode;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Common\Version;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCode\Renderer\RendererInterface;

/**
 * QR code writer.
 */
final class Writer
{
    /**
<<<<<<< HEAD
     * Creates a new writer with a specific renderer.
     */
    public function __construct(private readonly RendererInterface $renderer)
    {
=======
     * Renderer instance.
     *
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Creates a new writer with a specific renderer.
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
    }

    /**
     * Writes QR code and returns it as string.
     *
     * Content is a string which *should* be encoded in UTF-8, in case there are
     * non ASCII-characters present.
     *
     * @throws InvalidArgumentException if the content is empty
     */
    public function writeString(
        string $content,
<<<<<<< HEAD
        string $encoding = Encoder::DEFAULT_BYTE_MODE_ENCODING,
=======
        string $encoding = Encoder::DEFAULT_BYTE_MODE_ECODING,
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
        ?ErrorCorrectionLevel $ecLevel = null,
        ?Version $forcedVersion = null
    ) : string {
        if (strlen($content) === 0) {
            throw new InvalidArgumentException('Found empty contents');
        }

        if (null === $ecLevel) {
            $ecLevel = ErrorCorrectionLevel::L();
        }

        return $this->renderer->render(Encoder::encode($content, $ecLevel, $encoding, $forcedVersion));
    }

    /**
     * Writes QR code to a file.
     *
     * @see Writer::writeString()
     */
    public function writeFile(
        string $content,
        string $filename,
<<<<<<< HEAD
        string $encoding = Encoder::DEFAULT_BYTE_MODE_ENCODING,
=======
        string $encoding = Encoder::DEFAULT_BYTE_MODE_ECODING,
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
        ?ErrorCorrectionLevel $ecLevel = null,
        ?Version $forcedVersion = null
    ) : void {
        file_put_contents($filename, $this->writeString($content, $encoding, $ecLevel, $forcedVersion));
    }
}
