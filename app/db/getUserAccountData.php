<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("connection.php");

function get_user_account_data() {
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
        $key_hash = $_POST["key"];
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $search = isset($_POST['search']) ? $connection->real_escape_string($_POST['search']) : '';
        $offset = ($page - 1) * $limit;

        // Count total records
        $countQuery = "SELECT COUNT(*) AS total 
                    FROM users u INNER JOIN master_key m ON u.id = m.user_id
                    WHERE (u.firstname_hash LIKE ? 
                    OR u.username_hash LIKE ? OR u.lastname_hash LIKE ?) AND m.key_hash = ?";

        $countResult = $connection->prepare($countQuery);

        // Add wildcards for LIKE search
        $searchParam0 = "%" . $search . "%";

        // Bind the parameters: 'ss' = 2 strings
        $countResult->bind_param("ssss", $searchParam0, $searchParam0, $searchParam0, $key_hash);

        // Execute the query
        $countResult->execute();

        // Get result
        $result = $countResult->get_result();
        $row = $result->fetch_assoc();

        $totalRows = $row['total'];
        $totalPages = ceil($totalRows / $limit);

        // Fetch limited data
        $sql = "SELECT * FROM users u INNER JOIN master_key m ON u.id = m.user_id
            WHERE (u.firstname_hash LIKE ? 
            OR u.username_hash LIKE ? OR u.lastname_hash LIKE ?) AND m.key_hash = ? ORDER BY u.id ASC
            LIMIT ? OFFSET ?";
        $stmt = $connection->prepare($sql);
        
        // Add wildcard characters for LIKE search
        $searchParam = "%" . $search . "%";

        // Bind parameters: 
        // 'ssii' means: string, string, integer, integer
        $stmt->bind_param("ssssii", $searchParam, $searchParam, $searchParam, $key_hash, $limit, $offset);

        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return["status"=>"success", "message"=>"Data accessed!", "data"=>$data, "total_pages"=>$totalPages, "success"=>true];
    } catch(Throwable $e) {
        return["status"=>"failed", "message"=>$e->getMessage(), "success"=>false];
    }

    finally {
        if ($countResult !== null) {
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