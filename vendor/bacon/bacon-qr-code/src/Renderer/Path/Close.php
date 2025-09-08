<?php
declare(strict_types = 1);

namespace BaconQrCode\Renderer\Path;

final class Close implements OperationInterface
{
<<<<<<< HEAD
    private static ?Close $instance = null;
=======
    /**
     * @var self|null
     */
    private static $instance;
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb

    private function __construct()
    {
    }

    public static function instance() : self
    {
        return self::$instance ?: self::$instance = new self();
    }

    /**
     * @return self
     */
    public function translate(float $x, float $y) : OperationInterface
    {
        return $this;
    }
<<<<<<< HEAD

    /**
     * @return self
     */
    public function rotate(int $degrees) : OperationInterface
    {
        return $this;
    }
=======
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
}
