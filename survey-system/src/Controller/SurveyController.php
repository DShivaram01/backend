<?php

namespace App\Controller;

use App\Entity\Response;
use App\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

class SurveyController extends AbstractController
{
    #[Route('/survey/{token}', name: 'app_survey')]
    public function show(string $token, SurveyRepository $surveyRepository): HttpResponse
    {
        $survey = $surveyRepository->findOneBy(['token' => $token]);

        if (!$survey) {
            throw $this->createNotFoundException('Survey not found.');
        }

        if (!$survey->isIsActive()) {
            return $this->render('survey/inactive.html.twig');
        }

        return $this->render('survey/show.html.twig', [
            'survey' => $survey,
        ]);
    }

    #[Route('/survey/{token}/submit', name: 'app_survey_submit', methods: ['POST'])]
    public function submit(
        string $token,
        Request $request,
        SurveyRepository $surveyRepository,
        EntityManagerInterface $em
    ): HttpResponse {
        $survey = $surveyRepository->findOneBy(['token' => $token]);

        if (!$survey || !$survey->isIsActive()) {
            throw $this->createNotFoundException('Survey not found or inactive.');
        }

        $answers = $request->request->all('answers');

        $response = new Response();
        $response->setSurvey($survey);
        $response->setAnswers($answers);
        $response->setSubmittedAt(new \DateTimeImmutable());

        $em->persist($response);
        $em->flush();

        return $this->render('survey/thankyou.html.twig');
    }
}