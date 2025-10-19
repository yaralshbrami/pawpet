<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PawPet - Home</title>
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

  <main class="home">
    <h1>Welcome to PawPet</h1>
    <p>Your companion is waiting for you! Browse and adopt pets in Riyadh.</p>

    <div class="tips-container">
      <h2 class="tips-title">Essential Pet Care Tips</h2>
      <section class="tips-section">
        <div class="tip-card">
          <h3>Provide Fresh Water Daily</h3>
          <p>Always ensure your pets have access to clean, fresh water to stay hydrated and healthy.</p>
        </div>
        <div class="tip-card">
          <h3>Regular Vet Visits</h3>
          <p>Schedule yearly checkups with a veterinarian to catch any health issues early and keep vaccinations up-to-date.</p>
        </div>
        <div class="tip-card">
          <h3>Proper Nutrition</h3>
          <p>Feed your pets a balanced diet that matches their age, size, and breed for optimal health and energy.</p>
        </div>
        <div class="tip-card">
          <h3>Daily Exercise</h3>
          <p>Give your pets plenty of exercise through walks, playtime, or training to maintain a healthy weight and happy mood.</p>
        </div>
        <div class="tip-card">
          <h3>Love and Attention</h3>
          <p>Spend quality time with your pets to strengthen your bond and provide emotional support.</p>
        </div>
      </section>
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