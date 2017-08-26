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

namespace eTraxis\SharedDomain\Framework\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

class UpdateSecretCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdated()
    {
        copy(__DIR__ . '/parameters.yml.dist', 'var/parameters.yml');

        $expected = "The 'secret' parameter was updated.\n";

        $command = new UpdateSecretCommand();

        $application = new Application();
        $application->add($command);

        $tester = new CommandTester($application->find('etraxis:secret'));
        $tester->execute(['parameters' => 'var/parameters.yml']);

        self::assertEquals($expected, $tester->getDisplay());

        $yaml = Yaml::parse(file_get_contents('var/parameters.yml'));

        self::assertTrue(isset($yaml['parameters']['secret']));
        self::assertNotEquals('ThisTokenIsNotSoSecretChangeIt', $yaml['parameters']['secret']);
        self::assertRegExp('/^[a-z0-9]{32}$/', $yaml['parameters']['secret']);

        unlink('var/parameters.yml');
    }

    public function testNotUpdated()
    {
        copy(__DIR__ . '/parameters.yml', 'var/parameters.yml');

        $expected = "The 'secret' parameter was not updated.\n";

        $command = new UpdateSecretCommand();

        $application = new Application();
        $application->add($command);

        $tester = new CommandTester($application->find('etraxis:secret'));
        $tester->execute(['parameters' => 'var/parameters.yml']);

        self::assertEquals($expected, $tester->getDisplay());

        $yaml = Yaml::parse(file_get_contents('var/parameters.yml'));

        self::assertTrue(isset($yaml['parameters']['secret']));
        self::assertEquals('d4d6565e3afd41d7fafdb61d0c438adff3a3137f', $yaml['parameters']['secret']);

        unlink('var/parameters.yml');
    }

    public function testForceUpdated()
    {
        copy(__DIR__ . '/parameters.yml', 'var/parameters.yml');

        $expected = "The 'secret' parameter was updated.\n";

        $command = new UpdateSecretCommand();

        $application = new Application();
        $application->add($command);

        $tester = new CommandTester($application->find('etraxis:secret'));
        $tester->execute(['parameters' => 'var/parameters.yml', '--force' => true]);

        self::assertEquals($expected, $tester->getDisplay());

        $yaml = Yaml::parse(file_get_contents('var/parameters.yml'));

        self::assertTrue(isset($yaml['parameters']['secret']));
        self::assertNotEquals('d4d6565e3afd41d7fafdb61d0c438adff3a3137f', $yaml['parameters']['secret']);
        self::assertRegExp('/^[a-z0-9]{32}$/', $yaml['parameters']['secret']);

        unlink('var/parameters.yml');
    }

    public function testMissingSecret()
    {
        copy(__DIR__ . '/parameters.yml.nosecret', 'var/parameters.yml');

        $expected = "The 'secret' parameter was updated.\n";

        $command = new UpdateSecretCommand();

        $application = new Application();
        $application->add($command);

        $tester = new CommandTester($application->find('etraxis:secret'));
        $tester->execute(['parameters' => 'var/parameters.yml', '--force' => true]);

        self::assertEquals($expected, $tester->getDisplay());

        $yaml = Yaml::parse(file_get_contents('var/parameters.yml'));

        self::assertTrue(isset($yaml['parameters']['secret']));
        self::assertRegExp('/^[a-z0-9]{32}$/', $yaml['parameters']['secret']);

        unlink('var/parameters.yml');
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage The 'var/parameters.yml' file was not found
     */
    public function testUnknownFile()
    {
        $command = new UpdateSecretCommand();

        $application = new Application();
        $application->add($command);

        $tester = new CommandTester($application->find('etraxis:secret'));
        $tester->execute(['parameters' => 'var/parameters.yml']);
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage The root key 'options' is missing
     */
    public function testUnknownKey()
    {
        copy(__DIR__ . '/parameters.yml', 'var/parameters.yml');

        $command = new UpdateSecretCommand();

        $application = new Application();
        $application->add($command);

        $tester = new CommandTester($application->find('etraxis:secret'));
        $tester->execute(['parameters' => 'var/parameters.yml', 'key' => 'options']);

        unlink('var/parameters.yml');
    }
}
