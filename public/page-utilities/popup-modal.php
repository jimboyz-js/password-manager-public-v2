<!-- Bootstrap Popup Modal -->
 <?php
/**
 * @author jimBoYz Ni ChOy!!!
 */
 function customModal() {
?>
<div class="modal in" id="customAlertModal" aria-hidden="true" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header" id="customModalHeader">
        <h5 class="modal-title" id="customModalTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="customModalBody"></div>
        <div class="modal-footer" id="customModalFooter">
        <!-- Buttons added dynamically -->
        <!-- See custom-modal.js -->
        </div>
    </div>
    </div>
</div>
 <?php
 }
 ?>

<!-- Session Modal -->
 <?php
 function sessionModal() {
    ?>
    <!-- Remove bootstrap session modal -->
<!-- <div class="modal fade s-dialog" id="session-modal-js" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
    <div class="modal-content" style="height: 300px;">
        <div class="d-flex flex-column justify-content-center align-items-center my-3">
        <img src="images/js-software-itsupport1 - favicon0.png" width="50px" alt="JS-Logo">
        <h5 class="modal-title mt-2">Session Expired</h5>
        </div>
        <div class="modal-body text-center" id="s-desc-msg"></div>
        <div class="modal-footer">
        <button type="button" id="s-btn-ok" class="btn btn-primary w-100" data-bs-dismiss="modal">Ok</button>
        </div>
    </div>
    </div>
</div> -->

<!-- Custom Session Expired Modal -->
<div id="sessionExpiredModalCustom" class="custom-session-modal">
  <div class="custom-session-modal-content" style="max-width: 500px;">
    <div class="d-flex flex-column justify-content-center align-items-center my-3">
        <img src="images/js-software-itsupport1 - favicon0.png" width="50px" alt="JS-Logo">
        <h5 class="modal-title mt-2">Session Expired</h5>
        </div>
    <!-- <p id="sessionExpiredMessage">Your session has expired due to inactivity.</p> -->
     <div id="sessionExpiredMessage" class="mb-2">Your session has expired due to inactivity.</div>
        <hr>
    <button id="sessionExpiredOkBtn" class="btn btn-primary w-100">OK</button>
  </div>
</div>
<?php
 }
?>

<?php
function addEntryModal() {
    ?>
<!-- Add Data Modal -->
<div class="modal fade" id="add-user-modal" aria-labelledby="add-user-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="add-user-modal-label">Add Entry</h5>
                <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="add-user-form" novalidate>
                <!-- prevent default HTML5 validation popup -->
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" autocomplete="off" required />
                        <div class="invalid-feedback" id="error-username"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="off" required />
                        <div class="con">
                            <div class="pass-con">
                                <input type="checkbox" name="show-password" class="show-pass" id="show-password" />
                                <label for="show-password" class="show-pass">Show password</label>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="error-password"></div>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="confirm" class="form-label">Confirm <span class="text text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm" name="confirm" autocomplete="off" required/>
                        <div class="invalid-feedback" id="error-confirm"></div>
                    </div> -->
                    <div class="mb-3">
                        <label for="account_for" class="form-label">Account For <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="account_for" name="account_for" required />
                        <div class="invalid-feedback" id="error-account_for"></div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <input type="text" class="form-control" id="note" name="note" />
                        <div class="invalid-feedback" id="error-note"></div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between align-items-center">
                    <div class="add-feedback" id="add-feedback"></div>
                    <div class="btn-components">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" name="add-btn" class="btn btn-primary" id="btn-add-update">
                        Add
                    </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php } ?>

<!-- To be remove -->
<?php
function updateEntryModal() {
    ?>
<!-- Update Data Modal -->
 <!-- I intentionally duplicate this section (add and update) unlike previous version to avoid DOM issue -->
<div class="modal fade" id="update-user-modal" aria-labelledby="update-user-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="update-user-modal-label">Update Entry</h5>
                <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="update-user-form" novalidate>
                <!-- prevent default HTML5 validation popup -->
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username <span class="text text-danger">*</span></label>
                        <input type="text" class="form-control" id="update-username" name="username" autocomplete="off" required />
                        <div class="invalid-feedback" id="error-update-username"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text text-danger">*</span></label>
                        <input type="password" class="form-control" id="update-password" name="password" autocomplete="off" required />
                        <div class="con">
                            <div class="pass-con">
                                <input type="checkbox" name="show-password" class="show-pass" id="update-show-password" />
                                <label for="show-password" class="show-pass">Show password</label>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="error-update-password"></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm" class="form-label">Confirm <span class="text text-danger">*</span></label>
                        <input type="password" class="form-control" id="update-confirm" name="confirm" autocomplete="off" required/>
                        <div class="invalid-feedback" id="error-update-confirm"></div>
                    </div>
                    <div class="mb-3">
                        <label for="account_for" class="form-label">Account For <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="update-account_for" name="account_for" required />
                        <div class="invalid-feedback" id="error-update-account_for"></div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note</label>
                        <input type="text" class="form-control" id="update-note" name="note" />
                        <div class="invalid-feedback" id="error-update-note"></div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between align-items-center">
                    <div class="add-feedback" id="update-feedback"></div>
                    <div class="btn-components">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" name="add-btn" class="btn btn-primary" id="btn-update">
                        Update
                    </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php } ?>

<?php
function addUserAccountModal() {
    ?>
<!-- Add and Update User Data Modal -->
<div class="modal fade" id="add-user-account-modal" aria-labelledby="add-user-account-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="add-user-account-modal-label">Add User Account</h5>
                <button type="button" class="btn-close" title="Close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form class="add-user-account-form" id="signupForm" novalidate>
                <!-- prevent default HTML5 validation popup -->
                <div class="modal-body">
                    <div class="mb-2">
                        <label for="firstname" class="form-label text-dark">Firstname</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" required>
                    </div>
                    <div class="mb-2">
                        <label for="lastname" class="form-label text-dark">Lastname</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" required>
                    </div>
                    <div class="mb-2">
                        <label for="username" class="form-label text-dark">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback" id="error-username"></div>
                    </div>
                    <div class="mb-2">
                        <label for="master-key" class="form-label text-dark" id="label-key">Key</label>
                        <!-- If you do not specify the key field then the default login key use to encrypt the data. -->
                         <!-- If you leave this field empty the login key will use to encrypt this data. -->
                        <img src="images/tips.png" width="10px" class="icon ic_bc_docs" alt="Images light bulb" id="icon" title="If you leave this field empty, the login key will be used to encrypt this data." style="cursor: pointer;">
                        <input type="text" class="form-control" id="master-key" name="master-key">
                        <div class="invalid-feedback" id="error-master-key"></div>
                    </div>
                    <div class="mb-2">
                        <label for="password" class="form-label text-dark" id="label-password">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            autocomplete="off">
                        <div class="invalid-feedback" id="error-password"></div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label text-dark">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm-password"
                            autocomplete="off">
                        <div class="invalid-feedback" id="error-confirm-password"></div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between align-items-center">
                    <div class="add-feedback" id="add-feedback"></div>
                    <div class="btn-components">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" name="add-btn" class="btn btn-primary" id="btn-add-update">
                            Add
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php } ?>