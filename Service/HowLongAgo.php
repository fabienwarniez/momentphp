<?php

namespace FabienWarniez\HowLongAgoBundle\Service;

use DateTime;

class HowLongAgo
{
    /**
     * @param DateTime $date The date to wordify.
     *
     * @return string
     */
    public function wordifyDate(DateTime $date)
    {
        return Translations::getTranslation('fr', 'past');
    }
}
