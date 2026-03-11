<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['survey_name'])) {
    $survey_name = htmlspecialchars($_POST['survey_name']);
    echo "<h2>Survey Name: $survey_name</h2>";
}



if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    die("Please upload a CSV file via POST.");
}

$file = $_FILES['file'];
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if ($fileExtension !== 'csv') {
    die("Only CSV files are allowed.");
}

$handle = fopen($file['tmp_name'], "r");
$data = [];
$rowNumber = 0;

while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $rowNumber++;
    if (count($row) < 3) continue;
    if ($rowNumber == 1 && strtolower($row[0]) == 'name') continue;

    $data[] = [
        "question" => trim($row[0]),
        "options"  => array_filter([trim($row[1]), trim($row[2]), trim($row[3]), trim($row[4])])
    ];
}
fclose($handle);

// START HTML FORM
echo '<form action="submit_survey.php" method="POST">';
foreach ($data as $index => $q) {
    $qText = htmlspecialchars($q['question']);
    echo "<h3>" . ($index + 1) . ". $qText</h3>";
    
    foreach ($q['options'] as $optIndex => $option) {
        $optText = htmlspecialchars($option);
        // Using q_{index} as the name ensures each question has a unique group
        echo "<label>";
        echo "<input type='radio' name='question_$index' value='$optText' required> $optText";
        echo "</label><br>";
    }
}
echo '<br><button type="submit">Submit Responses</button>';
echo '</form>';
?>
