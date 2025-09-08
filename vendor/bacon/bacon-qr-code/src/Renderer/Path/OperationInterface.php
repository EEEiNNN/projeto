<?php
declare(strict_types = 1);

namespace BaconQrCode\Renderer\Path;

interface OperationInterface
{
    /**
     * Translates the operation's coordinates.
     */
    public function translate(float $x, float $y) : self;
<<<<<<< HEAD

    /**
     * Rotates the operation's coordinates.
     */
    public function rotate(int $degrees) : self;
=======
>>>>>>> 9a1505c21ac62ee06081b4c91de8bf496714d3eb
}
