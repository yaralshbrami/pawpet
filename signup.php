<?php
session_start();
include 'db.php';

// Redirect to home if user is already logged in
if (isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) {
    echo "<script>
      document.addEventListener('DOMContentLoaded', function() {
        showCustomAlert('Email already registered. Please use a different email.', 'warning');
      });
    </script>";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $phone);
    if ($stmt->execute()) {
      $_SESSION['user_id'] = $stmt->insert_id;
      $_SESSION['user_name'] = $name;
      echo "<script>window.location.href='index.php';</script>";
      exit();
    } else {
      echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
          showCustomAlert('Registration failed. Please try again.', 'error');
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - PawPet</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="signup-page">
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
    <h2>Create Account</h2>
    <form method="POST" id="signupForm">
      <div class="form-grid">
        <div class="form-left">
          <label for="name">Full Name:</label>
          <input type="text" name="name" id="name" required placeholder="Enter your full name" minlength="2">
          
          <label for="email">Email Address:</label>
          <input type="email" name="email" id="email" required placeholder="Enter your email address">
        </div>
        
        <div class="form-right">
          <label for="phone">Phone Number:</label>
          <input type="tel" name="phone" id="phone" required placeholder="Enter your phone number" pattern="^(\+966|0)?[5][0-9]{8}$">
          
          <label for="password">Password:</label>
          <input type="password" name="password" id="password" required placeholder="Enter a strong password" minlength="8">
        </div>
      </div>
      
      <div class="password-strength" id="passwordStrength" style="display: none;">
        <div class="strength-bar">
          <div class="strength-fill"></div>
        </div>
        <span class="strength-text"></span>
      </div>
      
      <button type="submit" id="signupBtn">
        <span class="btn-text">Create Account</span>
      </button>
      
      <button type="button" onclick="window.location.href='login.php'">
        Already have an account? Login
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
    // Password strength checker
    function checkPasswordStrength(password) {
      let strength = 0;
      const checks = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        numbers: /\d/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
      };

      strength = Object.values(checks).filter(Boolean).length;

      const strengthIndicator = document.getElementById('passwordStrength');
      const strengthFill = strengthIndicator.querySelector('.strength-fill');
      const strengthText = strengthIndicator.querySelector('.strength-text');

      if (password.length > 0) {
        strengthIndicator.style.display = 'block';
        
        const percentage = (strength / 5) * 100;
        strengthFill.style.width = percentage + '%';
        
        if (strength <= 2) {
          strengthFill.style.background = '#f44336';
          strengthText.textContent = 'Weak';
          strengthText.style.color = '#f44336';
        } else if (strength <= 3) {
          strengthFill.style.background = '#ff9800';
          strengthText.textContent = 'Medium';
          strengthText.style.color = '#ff9800';
        } else if (strength <= 4) {
          strengthFill.style.background = '#2196f3';
          strengthText.textContent = 'Good';
          strengthText.style.color = '#2196f3';
        } else {
          strengthFill.style.background = '#4caf50';
          strengthText.textContent = 'Strong';
          strengthText.style.color = '#4caf50';
        }
      } else {
        strengthIndicator.style.display = 'none';
      }
    }

    // Form validation and loading states
    document.getElementById('signupForm').addEventListener('submit', function(e) {
      const button = document.getElementById('signupBtn');
      const btnText = button.querySelector('.btn-text');
      
      button.classList.add('loading');
      btnText.textContent = 'Creating Account...';
      
      // Reset after 3 seconds if no redirect happens
      setTimeout(() => {
        button.classList.remove('loading');
        btnText.textContent = 'Create Account';
      }, 3000);
    });

    // Password strength checking
    document.getElementById('password').addEventListener('input', function() {
      checkPasswordStrength(this.value);
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
        if (this.id !== 'password') {
          this.classList.remove('success', 'error');
        }
      });
    });
  </script>
  
  <style>
    .password-strength {
      margin-top: 5px;
      margin-bottom: 15px;
    }
    
    .strength-bar {
      width: 100%;
      height: 6px;
      background-color: #e0e0e0;
      border-radius: 3px;
      overflow: hidden;
      margin-bottom: 5px;
    }
    
    .strength-fill {
      height: 100%;
      width: 0%;
      transition: all 0.3s ease;
      border-radius: 3px;
    }
    
    .strength-text {
      font-size: 12px;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
  </style>
</body>
</html>