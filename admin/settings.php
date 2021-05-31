<?php
$errors = [];

function insertSettings($errors, $settings, $db) {
    if (count($errors) == 0) {

        foreach ($settings as $name => $value) {
            // Check if already exists
            $stmt = mysqli_prepare($db, "SELECT * FROM settings WHERE setting_name = ?");
            mysqli_stmt_bind_param($stmt, 's', $name);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);

            if (mysqli_num_rows($res) == 1) {
                $stmt = mysqli_prepare($db, "UPDATE settings SET value = ? WHERE setting_name = ?");
                mysqli_stmt_bind_param($stmt, 'ss', $value, $name);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                $stmt = mysqli_prepare($db, "INSERT INTO settings(setting_name, value) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, 'ss', $name, $value);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    }
}

/** Insert into DB - general **/
if (isset($_POST['generalBtn'])) {
    $siteName = $_POST['siteName'];
    $siteDesc = $_POST['siteDescription'];
    $currency = $_POST['currency'];

    $settings = ['site_name' => $siteName, 'site_description' => $siteDesc, 'currency' => $currency];

    if (strlen($siteName) > 255) { array_push($errors, "Site name is too long. Max 255 characters."); }

    if (strlen($siteDesc) > 255) { array_push($errors, "Site description is too long. Max 255 characters."); }

    if (strlen($currency) > 255) { array_push($errors, "Currency is too long. Max 255 characters."); }

    insertSettings($errors, $settings, $db);
}


/** Insert into DB - payment **/
if (isset($_POST['paymentBtn'])) {
    $paymentName = $_POST['paymentName'];
    $accountNumber = $_POST['accountNumber'];
    $bankName = $_POST['bankName'];
    $sortCode = $_POST['sortCode'];
    $iban = $_POST['iban'];
    $bicSwift = $_POST['bicSwift'];

    $settings = ['payment_name' => $paymentName, 'account_number' => $accountNumber, 'bank_name' => $bankName, 'sort_code' => $sortCode, 'iban' => $iban, 'bic_swift' => $bicSwift];

    if (strlen($paymentName) > 255) { array_push($errors, "Payment name is too long. Max 255 characters."); }
    if (strlen($accountNumber) > 255) { array_push($errors, "Account number is too long. Max 255 characters."); }
    if (strlen($bankName) > 255) { array_push($errors, "Bank name is too long. Max 255 characters."); }
    if (strlen($sortCode) > 255) { array_push($errors, "Sort code is too long. Max 255 characters."); }
    if (strlen($iban) > 255) { array_push($errors, "IBAN is too long. Max 255 characters."); }
    if (strlen($bicSwift) > 255) { array_push($errors, "BIC / Swift is too long. Max 255 characters."); }

    insertSettings($errors, $settings, $db);
}


/** Get values from DB **/
$stmt = mysqli_prepare($db, "SELECT * FROM settings");
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

$settings = [];
while ($resArr = mysqli_fetch_assoc($res)) {
    $settings[$resArr['setting_name']] = $resArr['value'];
}

?>

<div class="row">

    <h2>Settings</h2>

    <?php
    displayErrors($errors);
    ?>

    <ul class="nav nav-tabs" id="settingTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">General</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false">Contact</button>
        </li>
    </ul>


    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">

            <form action="index.php?source=settings" method="post" class="form-width-700 mt-3">
                <div class="mb-3">
                    <label for="siteName" class="form-label">Site name</label>
                    <input type="text" id="siteName" name="siteName" class="form-control" value="<?=$settings['site_name'] ?? ''?>" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="siteDescription" class="form-label">Site description</label>
                    <input type="text" id="siteDescription" name="siteDescription" class="form-control" aria-describedby="siteDescriptionDesc" value="<?=$settings['site_description'] ?? ''?>" maxlength="255">
                    <div id="siteDescriptionDesc" class="form-text">In a few words, explain what this site is about.</div>
                </div>

                <div class="mb-3">
                    <label for="currency" class="form-label">Currency</label>
                    <input type="text" id="currency" name="currency" class="form-control" aria-describedby="currencyDesc" value="<?=$settings['currency'] ?? ''?>" maxlength="255">
                    <div id="currencyDesc" class="form-text">Type in your currency. (e.g. $)</div>
                </div>

                <input type="submit" name="generalBtn" value="Submit" class="btn btn-primary">
            </form>

        </div>


        <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">

            <div class="callout callout-info alert-info">
                Payment methods needs to be added manually. Here you can only setup "Direct bank transfer" information. Only filled fields will be shown to users.
            </div>

            <form action="index.php?source=settings" method="post" class="form-width-700 mt-3">
                <div class="mb-3">
                    <label for="paymentName" class="form-label">Payment name</label>
                    <input type="text" id="paymentName" name="paymentName" aria-describedby="paymentNameDesc" class="form-control" value="<?=$settings['payment_name'] ?? ''?>" maxlength="255">
                    <div id="paymentNameDesc" class="form-text">Name displayed for users.</div>
                </div>

                <div class="mb-3">
                    <label for="accountNumber" class="form-label">Account number</label>
                    <input type="text" id="accountNumber" name="accountNumber" class="form-control" value="<?=$settings['account_number'] ?? ''?>" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="bankName" class="form-label">Bank name</label>
                    <input type="text" id="bankName" name="bankName" class="form-control" value="<?=$settings['bank_name'] ?? ''?>" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="sortCode" class="form-label">Sort code</label>
                    <input type="text" id="sortCode" name="sortCode" class="form-control" value="<?=$settings['sort_code'] ?? ''?>" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="iban" class="form-label">IBAN</label>
                    <input type="text" id="iban" name="iban" class="form-control" value="<?=$settings['iban'] ?? ''?>" maxlength="255">
                </div>

                <div class="mb-3">
                    <label for="bicSwift" class="form-label">BIC / Swift</label>
                    <input type="text" id="bicSwift" name="bicSwift" class="form-control" value="<?=$settings['bic_swift'] ?? ''?>" maxlength="255">
                </div>

                <input type="submit" name="paymentBtn" value="Submit" class="btn btn-primary">
            </form>

        </div>
    </div>
</div>


