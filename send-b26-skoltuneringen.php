<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Hämta formulärdata ---
    $school = htmlspecialchars($_POST['school']);
    $city = htmlspecialchars($_POST['city']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // --- Skapa mapp om den inte finns ---
    if (!is_dir('submissions')) {
        mkdir('submissions', 0777, true);
    }

    // --- Skapa unikt filnamn ---
    $filename = 'submissions/' . date("Y-m-d_H-i-s") . '.txt';

    // --- Skriv filen lokalt ---
    $content = "Skola: $school\nOrt: $city\nE-post: $email\nMeddelande:\n$message\n\n";
    file_put_contents($filename, $content);

    echo "<p style='color:#ffbf00; text-align:center;'>Tack! Din anmälan har sparats lokalt.</p>";

    // --- GitHub uppladdning ---
    $token = "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"; // Byt ut med ditt token
    $repo = "erikroininenea/b26schooltournament";      // Byt ut med ditt repo
    $branch = "main";              // Branchnamn

    $path_in_repo = "submissions/" . basename($filename);
    $apiUrl = "https://api.github.com/repos/$repo/contents/$path_in_repo";

    $data = [
        "message" => "Ny anmälan: " . date("Y-m-d H:i:s"),
        "content" => base64_encode(file_get_contents($filename)),
        "branch" => $branch
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n" .
                         "Authorization: token $token\r\n" .
                         "User-Agent: PHP\r\n",
            'method'  => 'PUT',
            'content' => json_encode($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = @file_get_contents($apiUrl, false, $context);

    if ($result === FALSE) { 
        echo "<p style='color:red; text-align:center;'>Fel vid uppladdning till GitHub.</p>"; 
    } else {
        echo "<p style='color:#ffbf00; text-align:center;'>Filen har laddats upp till GitHub!</p>";
    }
} else {
    echo "<p style='color:red; text-align:center;'>Formuläret skickades inte korrekt.</p>";
}
?>
