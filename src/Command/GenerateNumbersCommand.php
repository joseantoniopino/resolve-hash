<?php

namespace App\Command;

use App\Exception\NoFilesFoundException;
use App\Service\CsvService;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

class GenerateNumbersCommand extends Command
{
    protected static $defaultName = 'Generate:Numbers';

    protected $parameterBag;
    private $md5Filename;
    private $cleanFilename;
    private $csvService;

    public function __construct(CsvService $csvService)
    {
        $this->md5Filename = 'md5.csv';
        $this->cleanFilename = 'abierto.csv';
        $this->csvService = $csvService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Use this command to generate 1000 phone numbers randomly.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws NoFilesFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $faker = Factory::create('es_ES');
        $io = new SymfonyStyle($input, $output);

        $io->title('Welcome to the automatic telephone number generator.');
        $io->text('This command will create two csv with 1000 random numbers each. One of them will keep the encrypted numbers in md5 and the other will keep them clean.');
        $confirm = $io->confirm('Do you want continue?');
        if ($confirm){
            $cleanNumbers = $this->createNumbersRandomly($faker, $output);
            $encryptedNumbers = array_map([$this,'addMD5'],$cleanNumbers);

            $this->csvService->createCSVFromArray($cleanNumbers, $this->cleanFilename,false );
            $this->csvService->createCSVFromArray($encryptedNumbers, $this->md5Filename, true, 70 );

            $io->newLine();

            $io->success('The files ' . $this->csvService->listFilesCreated() . ' are stored in ' . $this->csvService->getRelativePath());
        } else {
            $io->warning('The command has not been executed.');
        }

        return 0;
    }

    /**
     * @param Generator $faker
     * @param $output
     * @return array
     */
    private function createNumbersRandomly(Generator $faker, OutputInterface $output): array
    {
        $progressBar = new ProgressBar($output, 1000);
        $cleanNumbers = [];
        $progressBar->start();
        for ($i=1; $i <= 1000; $i++){
            $phone = str_replace([' ', '-', '+34'],'',$faker->unique()->phoneNumber);
            if (!in_array($phone,$cleanNumbers)){
                $cleanNumbers[] = $phone;
                $progressBar->advance();
            } else {
                $i--;
            }
            unset($phone);
        }
        $progressBar->finish();
        return $cleanNumbers;
    }

    /**
     * @param $item
     * @return string
     */
    private function addMD5($item): string
    {
        return md5($item);
    }
}
