<?php

namespace App\Command;

use App\Exception\NotFindFilesException;
use DirectoryIterator;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Console\Helper\ProgressBar;

class GenerateNumbersCommand extends Command
{
    protected static $defaultName = 'Generate:Numbers';

    protected $parameterBag;

    private $md5Filename;
    private $cleanFilename;
    private $csvDirectory;
    private $relativePath;
    private $projectDir;

    public function __construct(ParameterBagInterface $parameterBag, $projectDir)
    {
        $this->parameterBag = $parameterBag;
        $this->md5Filename = 'md5.csv';
        $this->cleanFilename = 'abierto.csv';
        $this->csvDirectory = $this->parameterBag->get('csv_directory');
        $this->projectDir = $projectDir;
        $this->relativePath = str_replace($projectDir,'', $this->csvDirectory);
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Use this command to generate 1000 phone numbers randomly.')
            /*->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')*/
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws NotFindFilesException
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

            $this->createCSVFromArray($cleanNumbers, $this->cleanFilename,false );
            $this->createCSVFromArray($encryptedNumbers, $this->md5Filename, true, 70 );

            $io->newLine();

            $io->success('The files ' . $this->listFilesCreated() . ' are stored in ' . $this->relativePath);
        } else {
            $io->warning('The command has not been executed.');
        }

        return 0;
    }

    /**
     * @param array $array
     * @param string $filename
     * @param bool $randomize
     * @param int $maxNumber
     */
    private function createCSVFromArray(array $array, string $filename, bool $randomize, int $maxNumber = 0): void
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $randomize ? $data = $serializer->encode(array_rand(array_flip($array), $maxNumber), 'csv') : $data = $serializer->encode($array, 'csv');
        file_put_contents($this->csvDirectory . '/' . $filename, $data);
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
                $progressBar->advance(-1);
            }
            unset($phone);
        }
        $progressBar->finish();
        return $cleanNumbers;
    }

    /**
     * @return string
     * @throws NotFindFilesException
     */
    private function listFilesCreated(): string
    {
        $filesCreated = [];
        $dir = new DirectoryIterator($this->csvDirectory);
        foreach ($dir as $file)
        {
            if ((!$file->isDot()) && ($file->getExtension() === 'csv')) {
                $csv = $file->getFilename();
                $filesCreated[] = $csv;
            }
        }

        if (count($filesCreated) == 0)
            throw new NotFindFilesException($this->csvDirectory);

        return implode(', ', $filesCreated);
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
