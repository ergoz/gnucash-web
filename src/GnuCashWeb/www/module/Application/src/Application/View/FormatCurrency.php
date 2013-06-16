<?php

namespace Application\View;

use Zend\View\Helper\AbstractHelper;

/**
 * Class FormatCurrency
 * @package Application\View
 */
class FormatCurrency extends AbstractHelper
{
    /**
     * Handles formatting currency values.
     *
     * @todo Make this configurable
     * @todo Handle locale number formats
     * @param float $input
     * @return string
     */
    public function __invoke($input)
    {
        if ($input < 0) {
            $formatted = number_format($input * -1, 2);
            $formatted = '<span class="negative-number">($' . $formatted . ')</span>';
        } else {
            $formatted = number_format($input, 2);
            $formatted = '<span class="positive-number">$' . $formatted . '</span>';
        }

        return $formatted;
    }
}
