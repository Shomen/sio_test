<?php

namespace App\Controller;

use App\Repository\TimelogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EvaluationController extends AbstractController
{
    #[Route('/evaluation', name: 'app_evaluation')]
    public function index(TimelogRepository $tr): Response
    {
        $data = $tr->findGroupByWorkedPerDay();
        $monthData = $tr->findGroupByWorkedPerMonth();

       // dd($data);
        return $this->render('evaluation/index.html.twig', [
            'data' => $data,
            'month' => $monthData,
        ]);
    }

    #[Route('/csvdownload/{type}', name: 'csvdownload', methods: ["GET"])]
    public function csvdownload(string $type, TimelogRepository $tr): Response
    {
        try {
            if ($type == 'day') {
                $data = $tr->findGroupByWorkedPerDay();
            } elseif ($type == 'month') {
                $data = $tr->findGroupByWorkedPerMonth();
            } else {
                $data = '';
                return $this->redirectToRoute('app_evolution', [], Response::HTTP_SEE_OTHER);
            }

            // normalization and encoding of $datas
            $encoders = [new CsvEncoder()];
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $csvContent = $serializer->serialize($data, 'csv');

            $response = new Response($csvContent);
            $response->headers->set('Content-Encoding', 'UTF-8');
            $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
            $response->headers->set('Content-Disposition', 'attachment; filename=worksheet.csv');
            return $response;

            //dd($data);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}
