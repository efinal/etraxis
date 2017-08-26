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

use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Console command to update the 'secret' parameter in the parameters file.
 */
class UpdateSecretCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('etraxis:secret')
            ->setDescription('Updates the \'secret\' parameter in the parameters file')
            ->addArgument('parameters', InputArgument::OPTIONAL, 'Path to the parameters file', 'app/config/parameters.yml')
            ->addArgument('key', InputArgument::OPTIONAL, 'Root key in the parameters file', 'parameters')
            ->addOption('force', 'f', null, 'Update the \'secret\' even if it\'s already updated');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parameters = $input->getArgument('parameters');
        $key        = $input->getArgument('key');
        $force      = $input->getOption('force');

        if (!file_exists($parameters)) {
            throw new \ErrorException("Warning: The '{$parameters}' file was not found", 0, E_ERROR, __FILE__, __LINE__);
        }

        $yaml = Yaml::parse(file_get_contents($parameters));

        if (!isset($yaml[$key])) {
            throw new \ErrorException("Warning: The root key '{$key}' is missing", 0, E_ERROR, __FILE__, __LINE__);
        }

        if ($force || $yaml[$key]['secret'] === 'ThisTokenIsNotSoSecretChangeIt') {
            $yaml[$key]['secret'] = Uuid::uuid4()->getHex();
            $output->writeln('<info>The \'secret\' parameter was updated.</info>');
        }
        else {
            $output->writeln('<comment>The \'secret\' parameter was not updated.</comment>');
        }

        file_put_contents($parameters, Yaml::dump($yaml));
    }
}
