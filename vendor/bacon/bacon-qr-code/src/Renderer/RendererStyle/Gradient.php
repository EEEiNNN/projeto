<?php
declare(strict_types = 1);

namespace BaconQrCode\Renderer\RendererStyle;

use BaconQrCode\Renderer\Color\ColorInterface;

final class Gradient
{
<<<<<<< HEAD
    public function __construct(
        private readonly ColorInterface $startColor,
        private readonly ColorInterface $endColor,
        private readonly GradientType   $type
    ) {
=======
    /**
     * @var ColorInterface
     */
    private $startColor;

    /**
     * @var ColorInterface
     */
    private $endColor;

    /**
     * @var GradientType
     */
    private $type;

    public function __construct(ColorInterface $startColor, ColorInterface $endColor, GradientType $type)
    {
        $this->startColor = $startColor;
        $this->endColor = $endColor;
        $this->type = $type;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
    }

    public function getStartColor() : ColorInterface
    {
        return $this->startColor;
    }

    public function getEndColor() : ColorInterface
    {
        return $this->endColor;
    }

    public function getType() : GradientType
    {
        return $this->type;
    }
}
