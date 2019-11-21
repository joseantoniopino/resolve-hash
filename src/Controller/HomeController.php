<?php

namespace App\Controller;

use App\Exception\FileNoExistException;
use App\Exception\NoFilesFoundException;
use App\Exception\NumberZeroIsNotAllowedException;
use App\Service\CsvService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     *
     * @return Response
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/list/{number}", name="list", requirements={"number"="\d+"})
     *
     * @param CsvService $csvService
     * @param int $number
     * @return Response
     * @throws NumberZeroIsNotAllowedException
     * @throws FileNoExistException
     * @throws NoFilesFoundException
     */
    public function list(CsvService $csvService, $number = 5)
    {
        // TODO: Hacer que pete si se pasa un número mayor a 70
        if ($number == 0)
            throw new NumberZeroIsNotAllowedException();

        $open = $csvService->createArrayFromCSV('abierto.csv', $number);
        $closed = $csvService->createArrayFromCSV('md5.csv');
        $result = [];
        // $resultTest = [];

        foreach ($open as $key => $phoneNumber)
        {
            if (in_array(md5($phoneNumber), $closed))
            {
                $result[$phoneNumber] = md5($phoneNumber);
            }
        }

        /*
         * También se podría haber hecho así. Pero lo de un foreach dentro de otro foreach siempre me ha parecido
         * un poco feo y trato de evitarlo, lo dejo comentado a modo de ejemplo ya que lo mismo parece que la forma
         * de arriba es hacer un poco trampa pues hasheo el $phoneNumber de nuevo en vez de rescatarlo de la fuente
         * de datos, el array.
         */

        /*foreach ($open as $o)
        {
            foreach ($closed as $c)
            {
                if (md5($o) == $c)
                    $resultTest[$o] = $c;
            }
        }
        dd($result, $resultTest);*/

        return $this->render('home/list.html.twig', ['phones' => $result]);
    }


}
