<?php
session_start();
include 'db.php';

$animals = $conn->query("
  SELECT animals.*, users.phone 
  FROM animals 
  JOIN users ON animals.user_id = users.user_id 
  ORDER BY animals.animal_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Available Animals - PawPet</title>
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

  <main class="animals">
    <?php while ($a = $animals->fetch_assoc()): ?>
    <div class="animal-card">
      <img src="<?= htmlspecialchars($a['image_url']) ?>" alt="Animal" onclick="zoomImage('<?= htmlspecialchars($a['image_url']) ?>')">
      <div class="buttons">
        <button onclick="toggleDetails(this)">Details</button>
        <button onclick="toggleAdoption(this)">Adopt</button>
      </div>
      <div class="details">
        <p>Name: <?= htmlspecialchars($a['name']) ?></p>
        <p>Type: <?= htmlspecialchars($a['type']) ?></p>
        <p>Age: <?= htmlspecialchars($a['age']) ?></p>
        <p>Gender: <?= htmlspecialchars($a['gender']) ?></p>
        <p>Note: <?= htmlspecialchars($a['description']) ?></p>
      </div>
      <div class="adopt">
        <p><strong>Contact Phone:</strong> <?= htmlspecialchars($a['phone']) ?></p>
        <p>Contact the pet owner directly to proceed with the adoption.</p>
      </div>
    </div>
    <?php endwhile; ?>
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