<?php

namespace App\Controller;

use App\Entity\Timelog;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'] ) ]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/savetime', name: 'savetime', methods: ['POST'] ) ]
    public function savetime(Request $request, EntityManagerInterface $em, ProjectRepository $projectRepository): Response
    {
        $timelog= new Timelog;
        $user = $this->getUser();
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
            $timelog->setStarttime(new \DateTime($parametersAsArray['starttime']));
            $timelog->setEndtime(new \DateTime($parametersAsArray['endtime']));
            $timelog->setUser($user);
            $project= $projectRepository->findOneById($parametersAsArray['projectID']);
            $timelog->setProject( $project);
            $timelog->setWorked(new \DateTime);
            $em->persist($timelog);
            $em->flush();
           
        }
        
        return new JsonResponse(['success' => 'Ok']);
    }
}
