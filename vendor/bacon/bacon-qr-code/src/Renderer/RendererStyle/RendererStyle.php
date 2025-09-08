<?php
declare(strict_types = 1);

namespace BaconQrCode\Renderer\RendererStyle;

use BaconQrCode\Renderer\Eye\EyeInterface;
use BaconQrCode\Renderer\Eye\ModuleEye;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Module\SquareModule;

final class RendererStyle
{
<<<<<<< HEAD
    private ModuleInterface $module;

    private EyeInterface|null $eye;

    private Fill $fill;

    public function __construct(
        private int $size,
        private int $margin = 4,
=======
    /**
     * @var int
     */
    private $size;

    /**
     * @var int
     */
    private $margin;

    /**
     * @var ModuleInterface
     */
    private $module;

    /**
     * @var EyeInterface|null
     */
    private $eye;

    /**
     * @var Fill
     */
    private $fill;

    public function __construct(
        int $size,
        int $margin = 4,
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
        ?ModuleInterface $module = null,
        ?EyeInterface $eye = null,
        ?Fill $fill = null
    ) {
<<<<<<< HEAD
=======
        $this->margin = $margin;
        $this->size = $size;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
        $this->module = $module ?: SquareModule::instance();
        $this->eye = $eye ?: new ModuleEye($this->module);
        $this->fill = $fill ?: Fill::default();
    }

    public function withSize(int $size) : self
    {
        $style = clone $this;
        $style->size = $size;
        return $style;
    }

    public function withMargin(int $margin) : self
    {
        $style = clone $this;
        $style->margin = $margin;
        return $style;
    }

    public function getSize() : int
    {
        return $this->size;
    }

    public function getMargin() : int
    {
        return $this->margin;
    }

    public function getModule() : ModuleInterface
    {
        return $this->module;
    }

    public function getEye() : EyeInterface
    {
        return $this->eye;
    }

    public function getFill() : Fill
    {
        return $this->fill;
    }
}
