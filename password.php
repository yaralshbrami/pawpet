<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];
  $user_id = $_SESSION['user_id'];

  if ($new_password !== $confirm_password) {
    echo "<script>
      document.addEventListener('DOMContentLoaded', function() {
        showCustomAlert('New passwords don\\'t match.', 'warning');
      });
    </script>";
  } else {
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (password_verify($current_password, $user['password'])) {
      $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
      $stmt->bind_param("si", $new_password_hash, $user_id);
      if ($stmt->execute()) {
        echo "<script>
          document.addEventListener('DOMContentLoaded', function() {
            showCustomAlert('Password changed successfully!', 'success');
            setTimeout(function(){ window.location.href='index.php'; }, 2000);
          });
        </script>";
      } else {
        echo "<script>
          document.addEventListener('DOMContentLoaded', function() {
            showCustomAlert('Failed to change password.', 'error');
          });
        </script>";
      }
    } else {
      echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
          showCustomAlert('Current password is incorrect.', 'error');
        });
      </script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password - PawPet</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo">🐾 PawPet</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="animals.php">Animals</a>
      <a href="add.php">Add Animal</a>
		<?php if (isset($_SESSION['user_name'])): ?>
		  <div class="user-dropdown">
			<span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
			<div class="user-dropdown-content">
			  <a href="password.php">Change Password</a>
			  <a href="logout.php">Logout</a>
			</div>
		  </div>
		<?php else: ?>
		  <a href="login.php">Login</a>
		<?php endif; ?>
    </nav>
  </header>

  <main class="password-page">
    <div class="form-page password-form">
      <h2>Change Password</h2>
      <form method="POST">
        <div class="password-grid">
          <label>Current Password: <input type="password" name="current_password" required></label>
          <label>New Password: <input type="password" name="new_password" required></label>
          <label>Confirm New Password: <input type="password" name="confirm_password" required></label>
        </div>
        <button type="submit">Change Password</button>
      </form>
    </div>
  </main>

  <footer>
    <section class="simple-info">
      <h3>About Us</h3>
      <p>PawPet helps pets find loving homes in Riyadh. We care about animals and support adoption for a better life.</p>
      <h3>Contact Us</h3>
      <p>Email: support@pawpet.com | Phone: +966 123 456 789</p>
    </section>
  </footer>

  <script src="script.js"></script>
</body>
</html>