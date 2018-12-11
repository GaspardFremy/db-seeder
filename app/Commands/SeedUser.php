<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;

use Faker;
use PDO;

class seedUser extends Command
{
    protected function configure()
    {
        $this->setName('seed:user')
            ->setDescription("populate users table with fake data")
            ->setHelp('populate users table with fake data')
            ->addOption('count', 'c', InputOption::VALUE_REQUIRED, 'How many users you want to create', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
            ProgressBar::setFormatDefinition('minimal', 'Progress: %percent%%');
            $progressBar = new ProgressBar($output, $input->getOption('count'));
            $progressBar->setFormat(sprintf('%s item: <info>%%item%%</info>',
            $progressBar->getFormatDefinition('debug')));
            $progressBar->setBarCharacter('<fg=green>⚬</>');
            $progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
            $progressBar->setProgressCharacter("<fg=green>➤</>");
            $progressBar->setFormat(
                "<fg=green;>%status:-45s%</>\n%current%/%max% [%bar%] %percent:3s%%\n🏁  %estimated:-20s%  %memory:20s%"
            );
            $progressBar->start();


            $pdo = new PDO('mysql:host=' . getenv('DATABASE_SERVER') . ';dbname=' . getenv('DATABASE_NAME'), getenv('DATABASE_USER'), getenv('DATABASE_PASSWORD'));
            $faker = Faker\Factory::create(fr_FR);


            for ($i = 0; $i < $input->getOption('count'); $i++) {

                $first_name = $faker->firstName;
                $last_name = $faker->lastName;
                $email = $faker->freeEmail;

                $pdo->query("INSERT INTO users (first_name, last_name, email) VALUES ('{$first_name}', '{$last_name}', '{$email}')");

                if ($i < ($input->getOption('count')/100)  ) {
                    $progressBar->setMessage("Starting...", 'status');
                } elseif ($i < $input->getOption('count') && $i != ($input->getOption('count')-1) ) {
                    $progressBar->setMessage("processing", 'status');
                } elseif ($i === ($input->getOption('count')-1)) {
                    $progressBar->setMessage("done", 'status');
                } else {
                    $progressBar->setMessage("wtf", 'status');
                }

                $progressBar->setMessage($i, 'item');
                $progressBar->advance();
            }
            $progressBar->finish();
        }
    }
