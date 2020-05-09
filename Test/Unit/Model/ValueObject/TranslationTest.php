<?php

namespace Angeldm\Debug\Test\Unit\Model\ValueObject;

use Angeldm\Debug\Model\ValueObject\Translation;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    public function testObject()
    {
        $phrase = 'phrase';
        $translation = 'translation';
        $defined = true;

        $object = new Translation($phrase, $translation, $defined);

        $this->assertEquals($phrase, $object->getPhrase());
        $this->assertEquals($phrase, $object->getId());
        $this->assertEquals($translation, $object->getTranslation());
        $this->assertTrue($object->isDefined());
    }
}
