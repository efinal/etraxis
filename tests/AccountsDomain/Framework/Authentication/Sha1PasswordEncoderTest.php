<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2017 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\AccountsDomain\Framework\Authentication;

class Sha1PasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Sha1PasswordEncoder */
    private $encoder;

    protected function setUp()
    {
        parent::setUp();

        $this->encoder = new Sha1PasswordEncoder();
    }

    public function testEncodePassword()
    {
        self::assertEquals('mzMEbtOdGC462vqQRa1nh9S7wyE=', $this->encoder->encodePassword('legacy'));
    }

    public function testEncodePasswordMaxLength()
    {
        $raw = str_pad(null, Md5PasswordEncoder::MAX_PASSWORD_LENGTH, '*');

        try {
            $this->encoder->encodePassword($raw);
        }
        catch (\Exception $exception) {
            self::fail();
        }
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testEncodePasswordTooLong()
    {
        $raw = str_pad(null, Md5PasswordEncoder::MAX_PASSWORD_LENGTH + 1, '*');

        $this->encoder->encodePassword($raw);
    }

    public function testIsPasswordValid()
    {
        $encoded = 'mzMEbtOdGC462vqQRa1nh9S7wyE=';
        $valid   = 'legacy';
        $invalid = 'invalid';

        self::assertTrue($this->encoder->isPasswordValid($encoded, $valid));
        self::assertFalse($this->encoder->isPasswordValid($encoded, $invalid));
    }
}
