<?php
declare(strict_types = 1);

namespace BaconQrCode\Renderer\Eye;

use BaconQrCode\Renderer\Path\Path;

/**
 * Combines the style of two different eyes.
 */
final class CompositeEye implements EyeInterface
{
<<<<<<< HEAD
    public function __construct(private readonly EyeInterface $externalEye, private readonly EyeInterface $internalEye)
    {
=======
    /**
     * @var EyeInterface
     */
    private $externalEye;

    /**
     * @var EyeInterface
     */
    private $internalEye;

    public function __construct(EyeInterface $externalEye, EyeInterface $internalEye)
    {
        $this->externalEye = $externalEye;
        $this->internalEye = $internalEye;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
    }

    public function getExternalPath() : Path
    {
        return $this->externalEye->getExternalPath();
    }

    public function getInternalPath() : Path
    {
        return $this->internalEye->getInternalPath();
    }
}
