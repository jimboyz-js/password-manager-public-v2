<?php
/**
 * @author jimBoYz Ni ChOy!!!
 */
include_once dirname(__DIR__).'/config.php';
include_once 'page-utilities/navbar.php';
include_once 'page-utilities/table.php';
include_once 'page-utilities/popup-modal.php';
include_once 'page-utilities/loading.php';

if(!isset($_SESSION["is_login_success"])) {
    header("Location: login.php");
    exit;
}

$_SESSION["page"] = "dashboard";

// If user tries to change it manually
if(isset($_SESSION["status"])) {// Optional check
    if (isset($_GET['status']) && $_GET['status'] !== $_SESSION['status']) {
        // Redirect back to the correct value
        header("Location: ?status=" . urlencode($_SESSION['status']) . "&id=".urlencode($_SESSION["id"])."&k=".urlencode($_SESSION["k"])."&page=".urlencode($_SESSION["page"]));
        exit;
    }
}

if(isset($_SESSION["id"])) {
    if (isset($_GET['id']) && $_GET['id'] !== $_SESSION['id']) {
        header("Location: ?status=" . urlencode($_SESSION['status']) . "&id=".urlencode($_SESSION["id"])."&k=".urlencode($_SESSION["k"])."&page=".urlencode($_SESSION["page"]));
        exit;
    }
}

if(isset($_SESSION["k"])) {
    if (isset($_GET['k']) && $_GET['k'] !== $_SESSION['k']) {
        header("Location: ?status=" . urlencode($_SESSION['status']) . "&id=".urlencode($_SESSION["id"])."&k=".urlencode($_SESSION["k"])."&page=".urlencode($_SESSION["page"]));
        exit;
    }
}

if(isset($_SESSION["page"])) {
    if (isset($_GET['page']) && $_GET['page'] !== $_SESSION['page']) {
        header("Location: ?status=" . urlencode($_SESSION['status']) . "&id=".urlencode($_SESSION["id"])."&k=".urlencode($_SESSION["k"])."&page=".urlencode($_SESSION["page"]));
        exit;
    }
}

if(isset($_SESSION["m_id"])) {
    if (isset($_GET['m_id']) && $_GET['m_id'] !== $_SESSION['m_id']) {
        header("Location: ?status=" . urlencode($_SESSION['status']) . "&id=".urlencode($_SESSION["id"])."&k=".urlencode($_SESSION["k"])."&page=".urlencode($_SESSION["page"])."&m_id=".urlencode($_SESSION["m_id"]));
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard | JS Password Manager v2.2.2</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="css/dashboard.css" />
  <link rel="stylesheet" href="css/custom-modal.css"/>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="icon" type="images/svg+xml" href="images/js-software-itsupport1 - favicon0.png">
</head>

<body>
  <?php navbar(); ?>
  <!-- JS-777 -->
  <div class="container-fluid">

    <div class="d-flex flex-row">

      <div class="ms-auto gap-2 mt-2 form-search">
        <input type="text" id="search" class="form-control search" placeholder="Search account or username" />
        <div class="btn-group" role="group">
          <button class="btn btn-primary" id="btn-search">
            Search
          </button>
          <button class="btn btn-success" title="Refresh" id="btn-refresh">
            <i class="fa-solid fa-arrows-rotate"></i>
          </button>
        </div>
      </div>
    </div>

    <?php table([
      "id"=>"ID",
      1=>"Username",
      2=>"Password",
      3=>"Note",
      4=>"Account",
      5=>"Added By",
      6=>"Date Added",
      7=>"Action",
    ]);?>
    <div class="text-center" id="pagination"></div>
    
    <?php customModal(); ?>
    <?php addEntryModal(); ?>
    <?php updateEntryModal(); ?>
    <?php sessionModal(); ?>
    <?php infiniteLoading() ?>
    <?php spinnerLoading() ?>

  </div>

  <div class="banner">
    <img src="images/js-software-itsupport1-final-02.png" width="210px" alt="jimBoYz Logo">
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="./script/dashboard.js"></script>
  <script type="module" src="./script/navbar.js"></script>
  <script type="module" src="./script/set-master-key.js"></script>
  <script src="./script/disable-dev-tools.js"></script>
</body>

</html>