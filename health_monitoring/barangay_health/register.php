 <?php
session_start();

if (isset($_SESSION['staff_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'barangay_health_db');
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    } else {
        $username  = $conn->real_escape_string(trim($_POST['username']));
        $fullname  = $conn->real_escape_string(trim($_POST['fullname']));
        $position  = $conn->real_escape_string(trim($_POST['position']));
        $contact   = $conn->real_escape_string(trim($_POST['contactnumber']));
        $password  = $_POST['password'];
        $confirm   = $_POST['confirm_password'];

        if (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match. Please try again.';
        } else {
            $check = $conn->query("SELECT staffid FROM staff WHERE username = '$username'");
            if ($check && $check->num_rows > 0) {
                $error = 'Username already exists. Please choose another.';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $conn->query("INSERT INTO staff (username, password, fullname, position, contactnumber) VALUES ('$username','$hashed','$fullname','$position','$contact')");
                if ($conn->affected_rows > 0) {
                    $success = 'Account created! You can now log in.';
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register — Barangay Health Center</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --teal:#0d9488;--teal-d:#0f766e;--teal-lt:#ccfbf1;
  --navy:#0f2744;--navy-m:#1e3a5f;
  --bg:#f0fafa;--white:#fff;
  --border:#d1faf4;--muted:#64748b;
  --red:#ef4444;--green:#22c55e;
  --shadow:0 4px 24px rgba(13,148,136,.13);
  --shadow-lg:0 12px 40px rgba(13,148,136,.18);
  --r:14px;--r-s:8px;
}
body{font-family:'DM Sans',sans-serif;background:var(--bg);min-height:100vh;display:flex;align-items:stretch;}

.auth-side{
  width:42%;background:linear-gradient(150deg,var(--navy) 0%,var(--teal-d) 100%);
  display:flex;flex-direction:column;justify-content:center;padding:4rem 3.5rem;
  position:relative;overflow:hidden;
}
.auth-side::before{content:'';position:absolute;bottom:-100px;right:-100px;width:320px;height:320px;border-radius:50%;background:rgba(13,148,136,.12);}
.auth-side::after{content:'';position:absolute;top:-60px;left:-60px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);}
.side-logo{width:56px;height:56px;background:rgba(255,255,255,.12);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin-bottom:2rem;border:1.5px solid rgba(255,255,255,.2);}
.auth-side h1{font-family:'Outfit',sans-serif;font-size:1.9rem;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-.5px;margin-bottom:1rem;}
.auth-side p{color:rgba(255,255,255,.65);font-size:.9rem;line-height:1.7;max-width:300px;}
.features{margin-top:2.5rem;display:flex;flex-direction:column;gap:.75rem;}
.feat{display:flex;align-items:center;gap:.75rem;color:rgba(255,255,255,.8);font-size:.88rem;}
.feat-icon{width:32px;height:32px;background:rgba(13,148,136,.3);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}

.auth-main{flex:1;display:flex;align-items:center;justify-content:center;padding:2.5rem 2rem;overflow-y:auto;}
.auth-box{width:100%;max-width:460px;}
.auth-box h2{font-family:'Outfit',sans-serif;font-size:1.6rem;font-weight:800;color:var(--navy);margin-bottom:.4rem;letter-spacing:-.4px;}
.auth-box .sub{color:var(--muted);font-size:.88rem;margin-bottom:1.75rem;}

.auth-card{background:var(--white);border-radius:var(--r);box-shadow:var(--shadow-lg);padding:2rem;border:1px solid var(--border);}

.alert-err{display:flex;align-items:center;gap:.6rem;padding:.9rem 1.1rem;border-radius:var(--r-s);margin-bottom:1.25rem;font-size:.88rem;font-weight:500;background:#fff1f2;color:#be123c;border-left:4px solid #f43f5e;}
.alert-ok{display:flex;align-items:center;gap:.6rem;padding:.9rem 1.1rem;border-radius:var(--r-s);margin-bottom:1.25rem;font-size:.88rem;font-weight:500;background:#f0fdf4;color:#15803d;border-left:4px solid #22c55e;}
.alert-ok a{color:inherit;font-weight:700;}

label{display:block;margin-bottom:.4rem;font-size:.84rem;font-weight:600;color:var(--navy);}
.star{color:var(--red);}
.input-wrap{position:relative;}
.input-wrap .ico{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);font-size:.9rem;pointer-events:none;}
input,select{width:100%;padding:.7rem 1rem .7rem 2.55rem;border:1.5px solid var(--border);border-radius:var(--r-s);font-family:'DM Sans',sans-serif;font-size:.9rem;color:var(--navy);background:#fafffe;transition:border-color .2s,box-shadow .2s;-webkit-appearance:none;}
select{padding-left:1rem;}
input:focus,select:focus{outline:none;border-color:var(--teal);box-shadow:0 0 0 3px rgba(13,148,136,.12);background:#fff;}
input::placeholder{color:#a1b5b3;}
.fg{margin-bottom:1rem;}
.row-2{display:grid;grid-template-columns:1fr 1fr;gap:.85rem;}

.btn{display:flex;align-items:center;justify-content:center;gap:.4rem;width:100%;padding:.78rem 1.5rem;border:none;border-radius:var(--r-s);font-family:'DM Sans',sans-serif;font-size:.95rem;font-weight:700;cursor:pointer;background:var(--teal);color:#fff;transition:all .18s;margin-top:.5rem;}
.btn:hover{background:var(--teal-d);box-shadow:0 4px 14px rgba(13,148,136,.35);transform:translateY(-1px);}

.auth-link{text-align:center;margin-top:1.5rem;font-size:.88rem;color:var(--muted);}
.auth-link a{color:var(--teal);font-weight:700;text-decoration:none;}
.auth-link a:hover{text-decoration:underline;}
.copy{text-align:center;margin-top:1.5rem;font-size:.76rem;color:var(--muted);}

@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.auth-card{animation:fadeUp .35s ease both;}
@media(max-width:800px){.auth-side{display:none;}.auth-main{padding:2rem 1.25rem;}}
</style>
</head>
<body>

<div class="auth-side">
  <div class="side-logo">🏥</div>
  <h1>Join the Health Team</h1>
  <p>Create your staff account to start managing patient records, appointments, and health monitoring.</p>
  <div class="features">
    <div class="feat"><div class="feat-icon">🔐</div><span>Secure staff authentication</span></div>
    <div class="feat"><div class="feat-icon">✏️</div><span>Add and update patient data</span></div>
    <div class="feat"><div class="feat-icon">📋</div><span>Access all health records</span></div>
    <div class="feat"><div class="feat-icon">🏷️</div><span>Role-based staff profiles</span></div>
  </div>
</div>

<div class="auth-main">
  <div class="auth-box">
    <h2>Create Account ✨</h2>
    <p class="sub">Fill in your details to register as health center staff</p>

    <?php if ($error): ?>
      <div class="alert-err">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert-ok">✅ <?= htmlspecialchars($success) ?> <a href="login.php">Login now →</a></div>
    <?php endif; ?>

    <div class="auth-card">
      <form method="POST">

        <div class="fg">
          <label>Full Name <span class="star">*</span></label>
          <div class="input-wrap">
            <span class="ico">👤</span>
            <input type="text" name="fullname" placeholder="e.g. Maria Santos" required>
          </div>
        </div>

        <div class="row-2">
          <div class="fg">
            <label>Username <span class="star">*</span></label>
            <div class="input-wrap">
              <span class="ico">@</span>
              <input type="text" name="username" placeholder="Choose username" required style="padding-left:2.4rem;">
            </div>
          </div>
          <div class="fg">
            <label>Position / Role</label>
            <select name="position">
              <option value="">Select position</option>
              <option value="Doctor">Doctor</option>
              <option value="Nurse">Nurse</option>
              <option value="Midwife">Midwife</option>
              <option value="Barangay Health Worker">BHW</option>
              <option value="Admin Staff">Admin Staff</option>
            </select>
          </div>
        </div>

        <div class="fg">
          <label>Contact Number</label>
          <div class="input-wrap">
            <span class="ico">📞</span>
            <input type="text" name="contactnumber" placeholder="e.g. 09xx-xxx-xxxx">
          </div>
        </div>

        <div class="row-2">
          <div class="fg">
            <label>Password <span class="star">*</span></label>
            <div class="input-wrap">
              <span class="ico">🔒</span>
              <input type="password" name="password" placeholder="Min. 6 characters" required>
            </div>
          </div>
          <div class="fg">
            <label>Confirm Password <span class="star">*</span></label>
            <div class="input-wrap">
              <span class="ico">🔑</span>
              <input type="password" name="confirm_password" placeholder="Repeat password" required>
            </div>
          </div>
        </div>

        <button type="submit" class="btn">Create Account →</button>
      </form>
    </div>

    <div class="auth-link">Already have an account? <a href="login.php">Sign in here</a></div>
    <p class="copy">&copy; <?= date('Y') ?> Barangay Health Center Management System</p>
  </div>
</div>

</body>
</html>
