<?php

namespace App\Controller;

use App\Entity\Survey;
use App\Entity\Question;
use App\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(SurveyRepository $surveyRepository): Response
    {
        $surveys = $surveyRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'surveys' => $surveys,
        ]);
    }

    #[Route('/upload', name: 'app_admin_upload', methods: ['POST'])]
    public function upload(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $name  = $request->request->get('survey_name');
        $file  = $request->files->get('csv_file');

        if (!$file || !$name) {
            $this->addFlash('error', 'Please provide a name and CSV file.');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        // Generate a unique token for the survey URL
        $token = bin2hex(random_bytes(8));

        $survey = new Survey();
        $survey->setName($name);
        $survey->setToken($token);
        $survey->setIsActive(true);
        $survey->setCreatedAt(new \DateTimeImmutable());

        $em->persist($survey);

        // Read the CSV file
        $handle = fopen($file->getPathname(), 'r');
        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row))) continue;

            // CSV format: Question, CorrectAnswer, WrongOption1, WrongOption2...
            $questionText  = trim($row[0] ?? '');
            $correctAnswer = trim($row[1] ?? '');
            $wrongOptions  = [];

            for ($i = 2; $i < count($row); $i++) {
                if (!empty(trim($row[$i]))) {
                    $wrongOptions[] = trim($row[$i]);
                }
            }

            if (empty($questionText) || empty($correctAnswer)) continue;

            $question = new Question();
            $question->setQuestionText($questionText);
            $question->setCorrectAnswer($correctAnswer);
            $question->setWrongOptions($wrongOptions);
            $question->setSurvey($survey);

            $em->persist($question);
        }
        fclose($handle);

        $em->flush();

        $this->addFlash('success', "Survey '{$name}' created! URL: /survey/{$token}");
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/toggle/{id}', name: 'app_admin_toggle')]
    public function toggle(Survey $survey, EntityManagerInterface $em): Response
    {
        $survey->setIsActive(!$survey->getIsActive());
        $em->flush();

        return $this->redirectToRoute('app_admin_dashboard');
    }
    #[Route('/results/{id}', name: 'app_admin_results')]
public function results(int $id, SurveyRepository $surveyRepository): Response
{
    $survey = $surveyRepository->find($id);
    return $this->render('admin/results.html.twig', [
        'survey' => $survey,
    ]);
}

#[Route('/download/{id}', name: 'app_admin_download')]
public function download(int $id, SurveyRepository $surveyRepository): Response
{
    $survey = $surveyRepository->find($id);
    $rows[] = ['Question', 'User Answer'];
    foreach ($survey->getResponses() as $response) {
        foreach ($response->getAnswers() as $questionId => $answer) {
            $rows[] = [$questionId, $answer];
        }
    }
    $csv = '';
    foreach ($rows as $row) {
        $csv .= implode(',', $row) . "\n";
    }
    return new Response($csv, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="results.csv"',
    ]);
}
}