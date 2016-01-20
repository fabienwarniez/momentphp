<?php

use FabienWarniez\HowLongAgoBundle\Service\Translations;

class TranslationsTest extends \PHPUnit_Framework_TestCase
{
    public function test_getTranslation()
    {
        $this->assertEquals('il y a %s', Translations::getTranslation('fr', 'past'));
        $this->assertEquals('dans %s', Translations::getTranslation('fr', 'future'));

        $this->assertEquals('%s ago', Translations::getTranslation('en-ca', 'past'));
        $this->assertEquals('in %s', Translations::getTranslation('en-ca', 'future'));
    }
}
