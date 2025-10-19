<?php
session_start();
include 'db.php';

// Redirect to home if user is already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($user = $res->fetch_assoc()) {
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['user_name'] = $user['name'];
      echo "<script>window.location.href='index.php';</script>";
      exit();
    } else {
      echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
          showCustomAlert('Wrong password.', 'error');
        });
      </script>";
    }
  } else {
    echo "<script>
      document.addEventListener('DOMContentLoaded', function() {
        showCustomAlert('User not found.', 'error');
      });
    </script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - PawPet</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
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

  <main class="form-main">
    <div class="form-page">
    <h2>Login</h2>
    <form method="POST" id="loginForm">
      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required placeholder="Enter your email">
      
      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required placeholder="Enter your password">
      
      <button type="submit" id="loginBtn">
        <span class="btn-text">Login</span>
      </button>
      
      <button type="button" onclick="window.location.href='signup.php'">
        Don't have an account? Sign Up
      </button>
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
  <script>
    // Add form validation and loading states
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const button = document.getElementById('loginBtn');
      const btnText = button.querySelector('.btn-text');
      
      button.classList.add('loading');
      btnText.textContent = 'Logging in...';
      
      // Reset after 3 seconds if no redirect happens
      setTimeout(() => {
        button.classList.remove('loading');
        btnText.textContent = 'Login';
      }, 3000);
    });

    // Add input validation feedback
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
      input.addEventListener('blur', function() {
        if (this.value && this.checkValidity()) {
          this.classList.add('success');
          this.classList.remove('error');
        } else if (this.value) {
          this.classList.add('error');
          this.classList.remove('success');
        }
      });

      input.addEventListener('input', function() {
        this.classList.remove('success', 'error');
      });
    });
  </script>
</body>
</html>