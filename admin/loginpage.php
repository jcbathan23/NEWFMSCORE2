<?php
session_start();

// Helper: emit a loading overlay animation then redirect
if (!function_exists('emit_login_loading_and_redirect')) {
    function emit_login_loading_and_redirect($message, $redirectUrl, $delayMs = 1200) {
        $messageEsc = htmlspecialchars($message, ENT_QUOTES);
        $redirectEsc = htmlspecialchars($redirectUrl, ENT_QUOTES);
        echo "<script>(function(){\n" .
            // Inject minimal styles + keyframes so overlay works without page CSS
            "var style=document.createElement('style');\n" .
            "style.textContent='@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}';\n" .
            "document.head.appendChild(style);\n" .
            // Build overlay
            "var overlay=document.createElement('div');\n" .
            "overlay.style.cssText='position:fixed;inset:0;        background: linear-gradient(180deg, rgba(44, 62, 80, 0.95) 0%, rgba(52, 73, 94, 0.98) 100%);backdrop-filter:blur(8px);display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:99999;';\n" .
            "var spinner=document.createElement('div');\n" .
            "spinner.style.cssText='width:56px;height:56px;border:3px solid rgba(0,198,255,.25);border-top-color:#00c6ff;border-radius:50%;animation:spin 1s linear infinite;margin-bottom:14px;';\n" .
            "var text=document.createElement('div');\n" .
            "text.textContent='" . $messageEsc . "';\n" .
            "text.style.cssText='color:#e2e8f0;font-weight:600;letter-spacing:.2px;margin-top:6px';\n" .
            "var sub=document.createElement('div');\n" .
            "sub.textContent='Preparing your dashboard...';\n" .
            "sub.style.cssText='color:#94a3b8;font-size:12px;margin-top:6px';\n" .
            "overlay.appendChild(spinner);overlay.appendChild(text);overlay.appendChild(sub);\n" .
            "document.addEventListener('DOMContentLoaded',function(){document.body.appendChild(overlay);});\n" .
            // Fallback if DOM already ready
            "if (document.readyState==='interactive' || document.readyState==='complete'){try{document.body.appendChild(overlay);}catch(e){document.addEventListener('DOMContentLoaded',function(){document.body.appendChild(overlay);});}}\n" .
            // Redirect after delay
            "setTimeout(function(){ window.location.href='" . $redirectEsc . "'; }, " . (int)$delayMs . ");\n" .
        "})();</script>";
    }
}

// Initialize variables
$email = $password = '';
$remember_me = false;
$login_error = $captcha_error = '';

// If redirected here due to access control
if (isset($_GET['denied'])) {
    $login_error = 'Access denied. Please log in first.';
}

// ✅ Generate CAPTCHA if not already set
if (!isset($_SESSION['captcha_code'])) {
    $_SESSION['captcha_code'] = rand(1000, 9999);
}

// ✅ Check for cookies
if(isset($_COOKIE['remember_email']) && isset($_COOKIE['remember_password'])){
    $email = $_COOKIE['remember_email'];
    $password = $_COOKIE['remember_password'];
    $remember_me = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember_me = isset($_POST['remember_me']);
    $captcha_input = trim($_POST['captcha']);

    // ✅ CAPTCHA validation
    if ($captcha_input != $_SESSION['captcha_code']) {
        $captcha_error = "Incorrect CAPTCHA! Please try again.";
        $_SESSION['captcha_code'] = rand(1000, 9999); // regenerate
    } else {
        include('../connect.php');

        if ($email && $password) {
            // 🔹 Admin check
            $stmt = $conn->prepare("SELECT email, password, account_type FROM admin_list WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if ($password === $row['password']) {
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['account_type'] = $row['account_type'];
                    // ✅ Remember Me cookies
                    if($remember_me){
                        setcookie('remember_email', $email, time() + (86400 * 30), "/");
                        setcookie('remember_password', $password, time() + (86400 * 30), "/");
                    } else {
                        setcookie('remember_email', '', time() - 3600, "/");
                        setcookie('remember_password', '', time() - 3600, "/");
                    }
                    emit_login_loading_and_redirect('Successful Login, Hello Admin!', 'dashboard.php');
                    exit();
                } else {
                    $login_error = "Incorrect Password!";
                }
                $stmt->close();
            } else {
                // 🔹 Service Provider check
                $stmt = $conn->prepare("
                    SELECT email, password, 3 AS account_type 
                    FROM active_service_provider 
                    WHERE email = ? 
                    UNION 
                    SELECT email, password, 3 AS account_type 
                    FROM pending_service_provider 
                    WHERE email = ?
                ");
                $stmt->bind_param("ss", $email, $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    if ($password === $row['password']) {
                        $_SESSION['email'] = $row['email'];
                        $_SESSION['account_type'] = 3;
                        if($remember_me){
                            setcookie('remember_email', $email, time() + (86400 * 30), "/");
                            setcookie('remember_password', $password, time() + (86400 * 30), "/");
                        } else {
                            setcookie('remember_email', '', time() - 3600, "/");
                            setcookie('remember_password', '', time() - 3600, "/");
                        }
                        emit_login_loading_and_redirect('Successful Login, Hello Service Provider!', 'provider_dashboard.php');
                        exit();
                    } else {
                        $login_error = "Incorrect Password!";
                    }
                } else {
                    // 🔹 Normal users check
                    $stmt = $conn->prepare("SELECT email, password, account_type FROM newaccounts WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        if ($password === $row['password']) {
                            $_SESSION['email'] = $row['email'];
                            $_SESSION['account_type'] = $row['account_type'];
                            if($remember_me){
                                setcookie('remember_email', $email, time() + (86400 * 30), "/");
                                setcookie('remember_password', $password, time() + (86400 * 30), "/");
                            } else {
                                setcookie('remember_email', '', time() - 3600, "/");
                                setcookie('remember_password', '', time() - 3600, "/");
                            }
                            emit_login_loading_and_redirect('Successful Login, Hello User!', 'user_dashboard.php');
                            exit();
                        } else {
                            $login_error = "Incorrect Password!";
                        }
                    } else {
                        $login_error = "Email is not registered!";
                    }
                    $stmt->close();
                }
                $stmt->close();
            }
        } else {
            if (!$email) $login_error = "Email is required!";
            if (!$password) $login_error = "Password is required!";
        }
        $conn->close();
    }
    // ✅ regenerate CAPTCHA after each attempt
    $_SESSION['captcha_code'] = rand(1000, 9999);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | SLATE</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Background - subtle dark teal gradient similar to mock */
body {
  background: linear-gradient(135deg, #0b2530 0%, #1f3541 100%);
  min-height: 100vh;
  display: flex;
  flex-direction: column; /* make column layout so footer can sit at bottom */
  padding: 20px;
}

/* Wrapper card */
.wrapper {
  display: flex;
  max-width: 1000px;
  width: 100%;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 12px 30px rgba(0,0,0,0.35);
  margin: auto; /* center wrapper within the page vertically and horizontally */
}

/* Left panel - blue gradient */
.left-container {
  flex: 2;
  background: linear-gradient(135deg, #0f5b7f 0%, #144b6b 100%);
  color: #e6f1f8;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  font-size: 1.8rem;
  font-weight: bold;
  padding: 20px;
  text-align: center;
}
.left-container img { width:60%; max-width:250px; height:auto; margin-bottom:20px; }
.social-links { display:flex; gap:20px; margin-top:30px; }
.social-links a { color:#e6f1f8; font-size:1.5rem; transition:opacity 0.3s; }
.social-links a:hover { opacity:0.8; }

/* Right panel - dark form card */
.login-card {
  flex: 1;
  background: #0f172a; /* dark navy */
  color: #e2e8f0;
  padding: 28px;
  animation: fadeIn 0.5s ease;
}
@keyframes fadeIn { from{opacity:0; transform:translateY(-10px);} to{opacity:1; transform:translateY(0);} }

/* Inputs themed dark */
.login-card .form-control {
  background: #1f2937;
  border: 1px solid #273449;
  color: #e5e7eb;
}
.login-card .form-control::placeholder { color: #94a3b8; }

/* Buttons - vibrant blue gradient */
.btn-primary {
  background: linear-gradient(90deg, #0ea5ea 0%, #2563eb 100%);
  border: none;
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
}
.btn-primary:hover { filter: brightness(1.05); }

/* Secondary small button */
.btn-secondary { background:#334155; border:none; }

button { border-radius: 10px; }

.loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(180deg, rgba(44, 62, 80, 0.95) 0%, rgba(52, 73, 94, 0.98) 100%);
      backdrop-filter: blur(20px);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      opacity: 0;
      visibility: hidden;
      transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .loading-overlay.show {
      opacity: 1;
      visibility: visible;
    }

    .loading-container {
      text-align: center;
      position: relative;
    }

    .loading-logo {
      width: 80px;
      height: 80px;
      margin-bottom: 2rem;
      animation: logoFloat 3s ease-in-out infinite;
    }

    .loading-spinner {
      width: 60px;
      height: 60px;
      border: 3px solid rgba(0, 198, 255, 0.2);
      border-top: 3px solid #00c6ff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 1.5rem;
      position: relative;
    }

    .loading-spinner::before {
      content: '';
      position: absolute;
      top: -3px;
      left: -3px;
      right: -3px;
      bottom: -3px;
      border: 3px solid transparent;
      border-top: 3px solid rgba(0, 198, 255, 0.4);
      border-radius: 50%;
      animation: spin 1.5s linear infinite reverse;
    }

    .loading-text {
      font-size: 1.2rem;
      font-weight: 600;
      color: #00c6ff;
      margin-bottom: 0.5rem;
      opacity: 0;
      animation: textFadeIn 0.5s ease-out 0.3s forwards;
    }

    .loading-subtext {
      font-size: 0.9rem;
      color: #b0bec5;
      opacity: 0;
      animation: textFadeIn 0.5s ease-out 0.6s forwards;
    }

    .loading-progress {
      width: 200px;
      height: 4px;
      background: rgba(0, 198, 255, 0.2);
      border-radius: 2px;
      margin: 1rem auto 0;
      overflow: hidden;
      position: relative;
    }

    .loading-progress-bar {
      height: 100%;
      background: linear-gradient(90deg, #00c6ff, #0072ff);
      border-radius: 2px;
      width: 0%;
      animation: progressFill 2s ease-in-out infinite;
    }

    .loading-dots {
      display: flex;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    .loading-dot {
      width: 8px;
      height: 8px;
      background: #00c6ff;
      border-radius: 50%;
      animation: dotPulse 1.4s ease-in-out infinite both;
    }

    .loading-dot:nth-child(2) {
      animation-delay: 0.2s;
    }

    .loading-dot:nth-child(3) {
      animation-delay: 0.4s;
    }

    /* Keyframes for loading overlay */
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    @keyframes logoFloat {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-6px); }
    }

    @keyframes textFadeIn {
      0% { opacity: 0; transform: translateY(6px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes progressFill {
      0% { width: 0%; }
      50% { width: 70%; }
      100% { width: 100%; }
    }

    @keyframes dotPulse {
      0%, 80%, 100% { transform: scale(0.8); opacity: 0.6; }
      40% { transform: scale(1.1); opacity: 1; }
    }

footer {
      text-align: center;
      padding: 20px;
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.875rem;
      backdrop-filter: blur(10px);
    }

    .footer-links {
      margin-top: 0.75rem;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .footer-link {
      color: rgba(255, 255, 255, 0.6);
      text-decoration: none;
      font-size: 0.8rem;
      padding: 0.25rem 0.75rem;
      border-radius: 1rem;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
    }

    .footer-link:hover {
      color: rgba(255, 255, 255, 0.9);
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .footer-divider {
      color: rgba(255, 255, 255, 0.3);
      margin: 0 0.5rem;
    }
</style>
</head>
<body>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-container">
      <img src="logo.png" alt="SLATE Logo" class="loading-logo">
      <div class="loading-spinner"></div>
      <div class="loading-text" id="loadingText">Loading...</div>
      <div class="loading-subtext" id="loadingSubtext">Please wait while we prepare your login</div>
      <div class="loading-progress">
        <div class="loading-progress-bar"></div>
      </div>
      <div class="loading-dots">
        <div class="loading-dot"></div>
        <div class="loading-dot"></div>
        <div class="loading-dot"></div>
      </div>
    </div>
  </div>

<div class="wrapper">
    <!-- Left Container -->
    <div class="left-container">
        <div>
            <img src="logo.png" alt="Logo">
            <div>FREIGHT MANAGEMENT SYSTEM</div>
        </div>
        <div class="social-links">
            <a href="https://facebook.com" target="_blank"><i class="bi bi-facebook"></i></a>
            <a href="mailto:yourcompany@gmail.com"><i class="bi bi-envelope-fill"></i></a>
            <a href="https://instagram.com" target="_blank"><i class="bi bi-instagram"></i></a>
        </div>
    </div>

    <!-- Right Login Card -->
    <div class="login-card">
        <h2 class="text-center mb-4">LOG IN</h2>

        <?php if (!empty($login_error)) : ?>
            <div class="alert alert-danger"><?php echo $login_error; ?></div>
        <?php endif; ?>
        <?php if (!empty($captcha_error)) : ?>
            <div class="alert alert-warning"><?php echo $captcha_error; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="text" class="form-control" name="email" placeholder="Enter Email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" class="form-control" name="password" placeholder="Enter Password" value="<?php echo htmlspecialchars($password); ?>" required>
            </div>

            <!-- CAPTCHA with refresh -->
            <div class="mb-3 d-flex align-items-center gap-2">
                <label class="mb-0">CAPTCHA: <strong id="captcha-code"><?php echo $_SESSION['captcha_code']; ?></strong></label>
                <button type="button" class="btn btn-secondary btn-sm" onclick="refreshCaptcha()">⟳</button>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="captcha" placeholder="Enter CAPTCHA" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me" <?php echo ($remember_me) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="remember_me">Remember Me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>

<footer>
    <div>&copy; <span id="currentYear"></span> SLATE Freight Management System. All rights reserved.</div>
    <div class="footer-links">
      <a href="terms.php" class="footer-link">
        <i class="bi bi-file-text"></i>
        Terms & Conditions
      </a>
      <span class="footer-divider">•</span>
      <a href="policy.php" class="footer-link">
        <i class="bi bi-shield-check"></i>
        Privacy Policy
      </a>
    </div>
  </footer>


<script>
function refreshCaptcha() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'refresh_captcha.php', true);
    xhr.onload = function() {
        if (this.status === 200) {
            document.getElementById('captcha-code').textContent = this.responseText;
        }
    }
    xhr.send();
}

// Show loading overlay on form submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action=""][method="POST"]') || document.querySelector('form');
    const overlay = document.getElementById('loadingOverlay');
    const submitBtn = document.querySelector('button[type="submit"]');

    // Show overlay immediately on initial page render
    if (overlay) {
        overlay.classList.add('show');
        // Hide overlay after all resources are fully loaded
        window.addEventListener('load', function() {
            setTimeout(function(){
                overlay.classList.remove('show');
            }, 300);
        });
    }

    // Keep overlay on during form submission
    if (form && overlay) {
        form.addEventListener('submit', function() {
            overlay.classList.add('show');
            if (submitBtn) submitBtn.disabled = true;
        });
    }
});
</script>

</body>
</html>
