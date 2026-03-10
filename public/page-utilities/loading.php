
<?php

/**
 * @author jimBoYz Ni ChOy!!!
 */

function infiniteLoading() {
    ?>
    <!-- Loading Modal -->
<div class="modal fade" id="loadingModal" aria-valuemax="3rem" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">

            <h5 class="mb-3">Please wait...</h5>

            <!-- Progress Bar -->
            <!-- Infinite Animation (Simple) -->
            <div class="progress w-100" id="progress1">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                    role="progressbar" style="width: 100%">
                </div>
            </div>

        </div>
    </div>
</div>
<?php
}
?>

<?php
function graduallyProgressBar() {
    ?>
    <!-- Loading Modal -->
<div class="modal fade" id="graduallyLoadingModal" aria-valuemax="3rem" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">

            <h5 class="mb-3">Please wait...</h5>

            <!-- Progress Bar -->
            <!-- Gradually Filling Progress Bar (Simulated) -->
            <div class="progress w-100" id="progress2">
                <div id="loadingBar" class="progress-bar bg-primary" role="progressbar" style="width: 0%">
                    0%
                </div>
            </div>

        </div>
    </div>
</div>

<?php
}
?>

<?php

function spinnerLoading($headerLoadingMessage="Please wait...", $loadingMessage="Loading...") {
    ?>
    <!-- Loading Modal -->
    <div class="modal fade" id="spinnerLoading" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">

                <!-- Spinner -->
                <div class="container">
                    <div class="spinner-border text-primary mb-3" id="spinner" role="status"
                        style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden"><?=$loadingMessage?></span>
                    </div>
                </div>

                <h5 class="mb-3"><?=$headerLoadingMessage?></h5>

            </div>
        </div>
    </div>
<?php
}
?>