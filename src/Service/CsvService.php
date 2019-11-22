<?php


namespace App\Service;


use App\Exception\FileNoExistException;
use App\Exception\NoFilesFoundException;
use DirectoryIterator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CsvService
{
    protected $parameterBag;

    private $csvDirectory;
    private $relativePath;
    private $projectDir;
    private $serializer;

    public function __construct(ParameterBagInterface $parameterBag, $projectDir)
    {
        $this->parameterBag = $parameterBag;
        $this->csvDirectory = $this->parameterBag->get('csv_directory');
        $this->projectDir = $projectDir;
        $this->relativePath = str_replace($projectDir,'', $this->csvDirectory);
        $this->serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
    }

    /**
     * @param array $array
     * @param string $filename
     * @param bool $randomize
     * @param int $maxNumber
     */
    public function createCSVFromArray(array $array, string $filename, bool $randomize, int $maxNumber = 0): void
    {
        $data = $randomize ? $this->serializer->encode(array_rand(array_flip($array), $maxNumber), 'csv') : $this->serializer->encode($array, 'csv');
        file_put_contents($this->csvDirectory . '/' . $filename, $data);
    }

    /**
     * @param string $filename
     * @param int $number
     * @return array
     * @throws FileNoExistException
     * @throws NoFilesFoundException
     */
    public function createArrayFromCSV(string $filename, int $number = 70): array
    {
        if (!strstr($this->listFilesCreated(), $filename)) {
            throw new FileNoExistException($filename);
        } else {
            return array_slice($this->serializer->decode(file_get_contents($this->getCsvDirectory() . '/' . $filename), 'csv'), 0, $number);
        }
    }

    /**
     * @return string
     * @throws NoFilesFoundException
     */
    public function listFilesCreated(): string
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
            throw new NoFilesFoundException($this->csvDirectory);

        return implode(', ', $filesCreated);
    }

    /**
     * @return mixed
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * @return mixed
     */
    public function getCsvDirectory()
    {
        return $this->csvDirectory;
    }
}