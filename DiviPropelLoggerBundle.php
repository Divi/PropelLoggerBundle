<?php

namespace Divi\PropelLoggerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Sylvain Lorinet <sylvain.lorinet@gmail.com>
 */
class DiviPropelLoggerBundle extends Bundle
{
    public function getParent()
    {
        return 'PropelBundle';
    }
}
