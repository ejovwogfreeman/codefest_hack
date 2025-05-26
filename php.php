<?php
include('./config/session.php');
include('./config/db.php'); // Include DB connection

$errors = [];
$success = '';

// Fetching bank names from the database
$banks_query = "SELECT * FROM banks";
$banks_result = mysqli_query($conn, $banks_query);

$user_id = isset($_SESSION['user']) ? $_SESSION['user']['user_id'] : '';

if (!$banks_result) {
    die("Database query failed: " . mysqli_error($conn));
}

function sanitize_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bank_name = sanitize_input($_POST['bank_name'] ?? '');
    $account_number = sanitize_input($_POST['account_number'] ?? '');

    // Validations
    if (empty($bank_name)) {
        $errors['bank_name'] = "Bank name is required.";
    }

    if (empty($account_number)) {
        $errors['account_number'] = "Account number is required.";
    } elseif (!is_numeric($account_number)) {
        $errors['account_number'] = "Account number must be numeric.";
    }

    if (empty($errors)) {
        // First check if the account exists
        $query = "SELECT * FROM users WHERE account_number = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $account_number, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) !== 0) {
            $errors['account_number'] = "You can't transfer money to yourself.";
        } else {

            $_SESSION['transfer_to'] = ['to_bank_name' => $bank_name, 'to_account_number' => $account_number];

            header('Location: https://wtrfb.com/send_money');
        }

        mysqli_stmt_close($stmt);
    }

    $_SESSION['form_errors'] = $errors;
} else {
    unset($_SESSION['form_errors']);
    unset($_SESSION['success']);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Wintrust Financial Bank - Personal and Business Banking Solutions</title>
    <meta name="description" content="Discover banking solutions with Wintrust Financial Bank. Enjoy premier checking accounts with interest rate bonuses, personal and business loans, wealth management, and more.">
    <meta name="keywords" content="Wintrust Financial Bank, Personal Banking, Business Banking, Premier Checking, Interest Rate Bonus, Wealth Management, Loans, Mortgage, SBA Lending, Treasury Management">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet" />

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- Libraries Stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="lib/animate/animate.min.css" rel="stylesheet" />
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet" />
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="./css/bootstrap.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="./css/style.css" rel="stylesheet" />

    <!-- favicon -->
    <link rel="shortcut icon" href="./img/favicon.png" type="image/x-icon">

    <style>
        .form-container {
            width: 400px;
            margin: auto;
        }

        @media screen and (max-width: 600px) {
            .form-container {
                width: 100%;
                margin: auto;
            }
        }
    </style>

</head>

<body>
    <!-- Spinner Start -->
    <div
        id="spinner"
        class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div
            class="spinner-border text-primary"
            style="width: 3rem; height: 3rem"
            role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    <!-- Register Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="text-center mt-5">
                <div class="form-container">
                    <img class="mb-4" src="./img/logo.png" alt="" width="200px">
                    <div id="google_translate_element"></div>
                    <h3 class="mt-4">Secured E-Banking</h3>
                    <p>Provide reviever's bank name and account number [Easy, Secure & Safe]</p>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show text-start" role="alert">
                            <?= $_SESSION['success']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show text-start" role="alert">
                            <?= $_SESSION['error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <form method="POST" action="" class="text-start">
                        <div class="mb-3">
                            <div class="form-floating">
                                <select class="form-control <?php echo isset($_SESSION['form_errors']['bank_name']) ? 'is-invalid' : ''; ?>" id="bank_name" name="bank_name">
                                    <option value="">Select Bank</option>
                                    <?php while ($row = mysqli_fetch_assoc($banks_result)): ?>
                                        <option value="<?= $row['bank_name']; ?>" <?php echo isset($_POST['bank_name']) && $_POST['bank_name'] == $row['bank_name'] ? 'selected' : ''; ?>>
                                            <?= $row['bank_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <label for="bank_name">Bank Name</label>
                                <?php if (isset($_SESSION['form_errors']['bank_name'])): ?>
                                    <small class="text-danger"><?php echo $_SESSION['form_errors']['bank_name']; ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control <?php echo isset($_SESSION['form_errors']['account_number']) ? 'is-invalid' : ''; ?>" id="account_number" name="account_number" placeholder="Enter account number" value="<?php echo isset($_POST['account_number']) ? $_POST['account_number'] : ''; ?>">
                                <label for="account_number">Account Number</label>
                                <?php if (isset($_SESSION['form_errors']['account_number'])): ?>
                                    <small class="text-danger"><?php echo $_SESSION['form_errors']['account_number']; ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 mb-3" type="submit" style="border-radius: 3px;">Next <i class="fas fa-angle-double-right"></i></button>
                    </form>

                    <div class="text-start">
                        <a href="https://wtrfb.com/dashboard"><i class="fas fa-angle-double-left"></i> Back</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Copyright Start -->
    <div class="container-fluid py-4">
        <div class="container">
            <div class="text-center">
                <span class="text-body">
                    &copy; Wintrust Financial Bank, All right reserved.
                </span>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"> </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>

    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en'
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- Template Javascript -->
    <script src="./js/main.js"></script>
</body>

</html>