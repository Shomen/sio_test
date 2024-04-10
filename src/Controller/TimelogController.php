<?php

namespace App\Controller;

use App\Entity\Timelog;
use App\Form\TimelogType;
use App\Repository\TimelogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/timelog')]
class TimelogController extends AbstractController
{
    #[Route('/', name: 'app_timelog_index', methods: ['GET'])]
    public function index(TimelogRepository $timelogRepository): Response
    {
        return $this->render('timelog/index.html.twig', [
            'timelogs' => $timelogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_timelog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $timelog = new Timelog();
        $form = $this->createForm(TimelogType::class, $timelog);
        $form->handleRequest($request);
        $user= $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $timelog->setUser($user);
            $entityManager->persist($timelog);
            $entityManager->flush();

            return $this->redirectToRoute('app_timelog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('timelog/new.html.twig', [
            'timelog' => $timelog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_timelog_show', methods: ['GET'])]
    public function show(Timelog $timelog): Response
    {
        return $this->render('timelog/show.html.twig', [
            'timelog' => $timelog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_timelog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Timelog $timelog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TimelogType::class, $timelog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_timelog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('timelog/edit.html.twig', [
            'timelog' => $timelog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_timelog_delete', methods: ['POST'])]
    public function delete(Request $request, Timelog $timelog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timelog->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($timelog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_timelog_index', [], Response::HTTP_SEE_OTHER);
    }
}
