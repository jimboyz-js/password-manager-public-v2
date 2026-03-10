 <?php
/**
 * @author jimBoYz Ni ChOy!!!
 */

 function navbar() {
    ?>
    <!-- Navbar -->
     <!-- See navbar.js -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand" href="#"><img src="./images/js-software-itsupport1 - favicon0.png"
            alt="jimBoYz Ni ChOy!!!" width="40px"> Password Manager v2.2.2</a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item nav-link add-entry" title="Add Account" id="add-user-account">Add</li>
            <li class="nav-item">
            <a class="nav-link" id="page-link">Admin</a>
            </li>
            <li class="nav-item nav-link" id="logout">Logout
            </li>
        </ul>
        </div>
    </div>
</nav>
<?php
 }
 ?>