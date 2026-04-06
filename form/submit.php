<?php

    // Get DB Details
    $pdo_details = get_db_details();

    // Connect to DB
    $pdo = connect_db($pdo_details);

    try {

        $name = $_POST['name'] ?? '';
        $guests = $_POST['guests'] ?? '';
        $diet = $_POST['diet'] ?? '';

        if(!empty($name) && !empty($guests) && !empty($diet)) {

            // Filter incoming data
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $guests = filter_input(INPUT_POST, 'guests', FILTER_SANITIZE_SPECIAL_CHARS);
            $diet = filter_input(INPUT_POST, 'diet', FILTER_SANITIZE_SPECIAL_CHARS);

            // Add to DB
            insert_rsvp($pdo, $name, $guests, $diet);

            // After adding rsvp, append it to a file as a backup
            $rsvp_data_file = "/path/to/private/data.txt";
            $rsvp_data = "\n--- $name || $guests || $diet ---\n";
            file_put_contents($rsvp_data_file, $rsvp_data, FILE_APPEND);

            header("Location: ../html/confirmation.html");
            exit;
        } else {
			error_log("\nError: Missing required fields.\n");
			http_response_code(500);
			header("Location: ../500.html");
			exit;
		}
    } catch (PDOException $e) {
        // Error Occurred

		error_log("\nDB Error: " . $e->getMessage() . "\n");
		http_response_code(500);
		header("Location: ../500.html");
		exit;
	}

	function get_db_details() {
        $config = require '/path/to/private/file.php';
	    $host = $config['host'];
	    $db   = $config['dbname'];
	    $user = $config['user'];
	    $pass = $config['pass'];

	    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
	    $options = [
		    PDO::ATTR_ERRMODE		=> PDO::ERRMODE_EXCEPTION, // Throw exceptions
		    PDO::ATTR_DEFAULT_FETCH_MODE 	=> PDO::FETCH_ASSOC,	  // Fetch associative arrays
		    PDO::ATTR_EMULATE_PREPARES 	=> false,		  // Use real prepared statements
	    ];

        $db_details = [
            'dsn'       => $dsn,
            'user'      => $user,
            'pass'      => $pass,
            'options'   => $options
        ];

        return $db_details;
	}

    function connect_db($db_details) {
        $dsn = $db_details['dsn'];
        $user = $db_details['user'];
        $pass = $db_details['pass'];
        $options = $db_details['options'];

        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    }

	function insert_rsvp($db, $name, $num, $diet) {
		$stmt = $db->prepare("
			INSERT INTO rsvp (name, num_attending, diet_restrictions)
			VALUES (:name, :num, :diet)
		");

		$stmt->execute([
			'name'	=> $name,
			'num'	=> $num,
			'diet'	=> $diet
		]);
	}