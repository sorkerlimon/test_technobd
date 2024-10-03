<?php

// Load environment variables from .env file
function loadEnv($filePath)
{
    if (!file_exists($filePath)) {
        throw new Exception(".env file not found");
    }

    // Read the lines from the .env file
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignore comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Split the line into key and value
        list($name, $value) = explode('=', $line, 2);
        
        // Trim any whitespace
        $name = trim($name);
        $value = trim($value);

        // Assign the variable to the $_ENV array
        $_ENV[$name] = $value;
    }
}

// Load the .env file
loadEnv(__DIR__ . '/.env');

// Access environment variables
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$port = $_ENV['DB_PORT'];

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination variables
$limit = 10; // Number of entries per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Offset for the query

// SQL query to select data from the table with pagination
$sql = "SELECT id, name, email FROM technobd_db LIMIT $limit OFFSET $offset"; 
$result = $conn->query($sql);

// Count total rows for pagination
$totalResult = $conn->query("SELECT COUNT(*) as total FROM technobd_db");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit); // Total number of pages

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechnoBd Limited</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Adjusted for responsiveness */
            margin: 0;
            padding: 20px; /* Added padding for smaller screens */
        }
        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 20px; /* Reduced padding for better responsiveness */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 100%; /* Allow full width */
            max-width: 800px; /* Limit max width */
        }
        h1 {
            font-size: 2em; /* Reduced size for better mobile visibility */
            color: #007bff;
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        .filter-container {
            margin-bottom: 20px;
        }
        .filter-container input {
            padding: 10px;
            margin: 0 5px; /* Adjusted margin */
            border: 1px solid #007bff;
            border-radius: 5px;
            outline: none;
            width: calc(50% - 12px); /* Responsive width */
            box-sizing: border-box; /* Include padding in width */
        }
        table {
            margin: 0 auto;
            width: 100%;
            border-collapse: collapse;
            background-color: #f8f9fa;
            font-size: 0.9em; /* Adjusted font size for better fit */
        }
        th, td {
            padding: 12px; /* Reduced padding for better mobile visibility */
            text-align: center;
            border: 1px solid #ddd;
            transition: background-color 0.3s;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        td {
            background-color: #fdfdfd;
            color: #333;
        }
        tr:hover td {
            background-color: #f1f1f1;
        }
        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }
        p {
            color: #dc3545; /* Red for error messages */
        }
        .pagination {
            margin-top: 20px;
            display: flex; /* Added flex for alignment */
            justify-content: center; /* Centered pagination */
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }
        .pagination a {
            padding: 8px 12px;
            margin: 5px; /* Uniform margin */
            border: 1px solid #007bff;
            border-radius: 5px;
            color: #007bff;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .pagination a:hover {
            background-color: #007bff;
            color: white;
        }

        /* Media queries for responsiveness */
        @media (max-width: 600px) {
            h1 {
                font-size: 1.5em; /* Smaller title for mobile */
            }
            .filter-container input {
                width: 100%; /* Full width for inputs */
                margin-bottom: 10px; /* Space between inputs */
            }
            table {
                font-size: 0.8em; /* Smaller font for mobile */
            }
            th, td {
                padding: 10px; /* Further reduced padding */
            }
        }
    </style>
    <script>
        function filterTable() {
            const nameInput = document.getElementById('nameFilter').value.toLowerCase();
            const emailInput = document.getElementById('emailFilter').value.toLowerCase();
            const table = document.getElementById('dataTable');
            const rows = table.getElementsByTagName('tr');

            // Loop through all table rows, except the first (header)
            for (let i = 1; i < rows.length; i++) {
                const nameCell = rows[i].getElementsByTagName('td')[1];
                const emailCell = rows[i].getElementsByTagName('td')[2];
                
                if (nameCell && emailCell) {
                    const nameText = nameCell.textContent || nameCell.innerText;
                    const emailText = emailCell.textContent || emailCell.innerText;

                    // Display rows that match the filter criteria
                    rows[i].style.display = (nameText.toLowerCase().indexOf(nameInput) > -1 && emailText.toLowerCase().indexOf(emailInput) > -1) ? "" : "none";
                }
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h1>TechnoBd Limited</h1>

    <div class="filter-container">
        <input type="text" id="nameFilter" placeholder="Filter by Name" onkeyup="filterTable()">
        <input type="text" id="emailFilter" placeholder="Filter by Email" onkeyup="filterTable()">
    </div>

    <?php
    if ($result->num_rows > 0) {
        echo "<table id='dataTable'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
        
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td><td>" . $row["email"] . "</td></tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No results found.</p>";
    }

    // Pagination links
    echo "<div class='pagination'>";
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='?page=$i'>$i</a>";
    }
    echo "</div>";

    // Close connection
    $conn->close();
    ?>
</div>

</body>
</html>
