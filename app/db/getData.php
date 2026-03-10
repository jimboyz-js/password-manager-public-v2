<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("connection.php");

function get_data_handler() {
    // Protected by session
    if(!isset($_SESSION["is_login_success"])) {
        echo json_encode(["success" => false, "message" => "Unauthorized access!"]);
        exit;
    }

    $connection = database_connection();

    if(!$connection) {
        return ["success"=>false, "status"=>"failed", "message"=>"Unable to prepare database query."];
        exit;
    }

    $countResult = null;
    $stmt = null;

    try {
        // Pagination settings
        $limit = 7; // rows per page
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : '';
        $offset = ($page - 1) * $limit;

        // Count total records

        // Using Statement
        // $countQuery = "SELECT COUNT(*) AS total FROM accounts WHERE account_for LIKE '%$search%' OR username_hash LIKE '%$search%'";
        // $countResult = $connection->query($countQuery);
        // $totalRows = $countResult->fetch_assoc()['total'];
        // $totalPages = ceil($totalRows / $limit);

        // Using Prepared Statement
        // Prepare the SQL query with placeholders
        $countQuery = "SELECT COUNT(*) AS total 
                    FROM accounts 
                    WHERE title_hash LIKE ? 
                    OR username_hash LIKE ?";

        $countResult = $connection->prepare($countQuery);

        // Add wildcards for LIKE search
        $searchParam0 = "%" . $search . "%";

        // Bind the parameters: 'ss' = 2 strings
        $countResult->bind_param("ss", $searchParam0, $searchParam0);

        // Execute the query
        $countResult->execute();

        // Get result
        $result = $countResult->get_result();
        $row = $result->fetch_assoc();

        $totalRows = $row['total'];
        $totalPages = ceil($totalRows / $limit);

        // Fetch limited data
        $sql = "SELECT * FROM accounts 
            WHERE title_hash LIKE ? 
            OR username_hash LIKE ? 
            LIMIT ? OFFSET ?";
            
        $stmt = $connection->prepare($sql);

        // Add wildcard characters for LIKE search
        // Not totally because it hashed
        $searchParam = "%" . $search . "%";

        // Bind parameters: 
        // 'ssii' means: string, string, integer, integer
        $stmt->bind_param("ssii", $searchParam, $searchParam, $limit, $offset);

        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        // respond("success", "Data accessed!", ["data"=>$data, "total_pages"=>$totalPages, "success"=>true]);
        return["status"=>"success", "message"=>"Data accessed!", "data"=>$data, "total_pages"=>$totalPages, "success"=>true];
    } catch(Throwable $e) {
        return["status"=>"failed", "message"=>$e->getMessage(), "success"=>false];
    }

    finally {
        if($countResult !== null) {
            $countResult->close();
        }
        if($stmt !== null) {
            $stmt->close();
        }
        if($connection !== null) {
            $connection->close();
        }
    }
}