<html>
<body>
<h2>Upload CSV File</h2>
<form action="csv_processor.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file" accept=".csv" required>
    <input type="text" name="survey_name" placeholder="Survey Name" required>
    <input type="submit" value="Generate Questionnaire">
</form>
</body>

</html>