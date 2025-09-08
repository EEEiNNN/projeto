<?php
declare(strict_types = 1);

namespace BaconQrCode\Encoder;

use SplFixedArray;

/**
 * Block pair.
 */
final class BlockPair
{
    /**
<<<<<<< HEAD
     * Creates a new block pair.
     *
     * @param SplFixedArray<int> $dataBytes Data bytes in the block.
     * @param SplFixedArray<int> $errorCorrectionBytes Error correction bytes in the block.
     */
    public function __construct(
        private readonly SplFixedArray $dataBytes,
        private readonly SplFixedArray $errorCorrectionBytes
    ) {
=======
     * Data bytes in the block.
     *
     * @var SplFixedArray<int>
     */
    private $dataBytes;

    /**
     * Error correction bytes in the block.
     *
     * @var SplFixedArray<int>
     */
    private $errorCorrectionBytes;

    /**
     * Creates a new block pair.
     *
     * @param SplFixedArray<int> $data
     * @param SplFixedArray<int> $errorCorrection
     */
    public function __construct(SplFixedArray $data, SplFixedArray $errorCorrection)
    {
        $this->dataBytes = $data;
        $this->errorCorrectionBytes = $errorCorrection;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
    }

    /**
     * Gets the data bytes.
     *
     * @return SplFixedArray<int>
     */
    public function getDataBytes() : SplFixedArray
    {
        return $this->dataBytes;
    }

    /**
     * Gets the error correction bytes.
     *
     * @return SplFixedArray<int>
     */
    public function getErrorCorrectionBytes() : SplFixedArray
    {
        return $this->errorCorrectionBytes;
    }
}
