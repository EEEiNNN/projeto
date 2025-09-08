<?php
declare(strict_types = 1);

namespace BaconQrCode\Renderer\Path;

final class Line implements OperationInterface
{
<<<<<<< HEAD
    public function __construct(private readonly float $x, private readonly float $y)
    {
=======
    /**
     * @var float
     */
    private $x;

    /**
     * @var float
     */
    private $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
    }

    public function getX() : float
    {
        return $this->x;
    }

    public function getY() : float
    {
        return $this->y;
    }

    /**
     * @return self
     */
    public function translate(float $x, float $y) : OperationInterface
    {
        return new self($this->x + $x, $this->y + $y);
    }
<<<<<<< HEAD

    /**
     * @return self
     */
    public function rotate(int $degrees) : OperationInterface
    {
        $radians = deg2rad($degrees);
        $sin = sin($radians);
        $cos = cos($radians);
        $xr = $this->x * $cos - $this->y * $sin;
        $yr = $this->x * $sin + $this->y * $cos;
        return new self($xr, $yr);
    }
=======
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
}
