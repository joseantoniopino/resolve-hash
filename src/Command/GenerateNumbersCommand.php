<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Faker;

class GenerateNumbersCommand extends Command
{
    protected static $defaultName = 'Generate:Numbers';

    protected function configure()
    {
        $this
            ->setDescription('Use this command to generate 1000 phone numbers randomly.')
            /*->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')*/
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $faker = Faker\Factory::create('es_ES');
        
        $cleanNumbers = [];
        $io = new SymfonyStyle($input, $output);

        $io->title('Welcome to the automatic telephone number generator.');
        $io->text('This command will create two csv with 1000 random numbers each. One of them will keep the encrypted numbers in md5 and the other will keep them clean.');
        $confirm = $io->confirm('Do you want continue?');
        if ($confirm){

            for ($i=1; $i <= 1000; $i++){
                $phone = str_replace([' ', '-', '+34'],'',$faker->unique()->phoneNumber);
                if (!in_array($phone,$cleanNumbers)){
                    $cleanNumbers[] = $phone;
                } else {
                    $i--;
                }
                unset($phone);
            }

            $encryptedNumbers = array_map([$this,'addMD5'],$cleanNumbers);


            $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        } else {
            $io->warning('The command has not been executed.');
        }


        return 0;
    }

    private function addMD5($item)
    {
        return md5($item);
    }
}
