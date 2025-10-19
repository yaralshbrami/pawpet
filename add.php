<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$message = '';
$message_type = '';

// معالجة إضافة حيوان جديد مباشرة (بدون API)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_animal'])) {
    $upload_dir = 'Uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $file_extension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = 'animal_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
    $upload_path = $upload_dir . $filename;

    $postData = [
        'action' => 'add_animal',
        'name' => $_POST['name'],
        'type' => $_POST['type'],
        'age' => $_POST['age'],
        'gender' => $_POST['gender'],
        'description' => $_POST['description'],
        'image_url' => $upload_path,
        'user_id' => $_SESSION['user_id']
    ];
    $api_url = 'http://localhost/PawPet/add-api.php';
    // إرسال البيانات كـ JSON
    $ch = curl_init($api_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_TIMEOUT => 10
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // معالجة الاستجابة
    $result = json_decode($response, true);
    if ($httpCode === 201 && $result['success']) {
        $message = 'Animal added successfully!';
        $message_type = 'success';
        $_POST = array();
        move_uploaded_file($_FILES['img']['tmp_name'], $upload_path);
    } else {
        $message = isset($result['errors']) ? implode(', ', $result['errors']) : 'Error adding animal';
        $message_type = 'error';
        if (file_exists($upload_path)) {
            unlink($upload_path);
        }
    }
}

// معالجة تحديث الحيوان
$is_editing = false;
$edit_animal = null;

if (isset($_GET['edit_id'])) {
    $animal_id = $_GET['edit_id'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT * FROM animals WHERE animal_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $animal_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $is_editing = true;
        $edit_animal = $result->fetch_assoc();
    }
}

// معالجة تحديث الحيوان
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_animal'])) {
    $animal_id = $_POST['animal_id'];
    $name = trim($_POST['name']);
    $type = trim($_POST['type']);
    $age = trim($_POST['age']);
    $gender = trim($_POST['gender']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    // التحقق من ملكية الحيوان
    $stmt = $conn->prepare("SELECT * FROM animals WHERE animal_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $animal_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $image_url = $_POST['existing_image'];
        
        // التحقق من وجود صورة جديدة
        if (isset($_FILES["img"]) && $_FILES["img"]["error"] === 0) {
            $upload_dir = 'Uploads/';
            $file_extension = strtolower(pathinfo($_FILES["img"]["name"], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_extension, $allowed_extensions)) {
                $filename = "animal_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
                $upload_path = $upload_dir . $filename;

                if (move_uploaded_file($_FILES["img"]["tmp_name"], $upload_path)) {
                    // حذف الصورة القديمة
                    if (file_exists($_POST['existing_image'])) {
                        unlink($_POST['existing_image']);
                    }
                    $image_url = $upload_path;
                }
            }
        }

        $stmt = $conn->prepare("UPDATE animals SET name = ?, type = ?, age = ?, gender = ?, description = ?, image_url = ? WHERE animal_id = ?");
        $stmt->bind_param("ssssssi", $name, $type, $age, $gender, $description, $image_url, $animal_id);
        
        if ($stmt->execute()) {
            $message = 'Animal updated successfully!';
            $message_type = 'success';
            // Removed header("Location: add.php"); to show the message on the same page
            $is_editing = false; // Reset edit mode to show the "Add Animal" form
        } else {
            $message = 'Failed to update animal';
            $message_type = 'error';
        }
    }
}

// معالجة حذف الحيوان
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $animal_id = $_POST['delete_id'];
    $user_id = $_SESSION['user_id'];
    
    // الحصول على مسار الصورة لحذفها
    $stmt = $conn->prepare("SELECT image_url FROM animals WHERE animal_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $animal_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // حذف الحيوان من قاعدة البيانات
        $stmt = $conn->prepare("DELETE FROM animals WHERE animal_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $animal_id, $user_id);
        
        if ($stmt->execute()) {
            // حذف الصورة
            if (file_exists($row['image_url'])) {
                unlink($row['image_url']);
            }
            $message = 'Animal deleted successfully';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete animal';
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $is_editing ? 'Edit Animal' : 'Add Animal'; ?> - PawPet</title>
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

  <main class="form-page add-animal">
    <h2><?php echo $is_editing ? 'Edit Animal' : 'Add a New Animal'; ?></h2>
    
    <?php if ($message): ?>
        <div id="messageBox" style="color: <?php echo $message_type == 'success' ? 'green' : 'red'; ?>; text-align: center; margin-bottom: 20px; padding: 10px; border: 1px solid; border-radius: 5px; background-color: <?php echo $message_type == 'success' ? '#d4edda' : '#f8d7da'; ?>;">
            <?php echo $message_type == 'success' ? '✅' : '❌'; ?> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
      <?php if ($is_editing): ?>
        <input type="hidden" name="animal_id" value="<?php echo $edit_animal['animal_id']; ?>">
        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_animal['image_url']); ?>">
      <?php endif; ?>
      
      <div class="form-grid-3">
        <div class="form-left">
          <label>Name: 
            <input type="text" name="name" required value="<?php echo $is_editing ? htmlspecialchars($edit_animal['name']) : (isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''); ?>">
          </label>
          
          <label>Type: 
            <input type="text" name="type" required value="<?php echo $is_editing ? htmlspecialchars($edit_animal['type']) : (isset($_POST['type']) ? htmlspecialchars($_POST['type']) : ''); ?>">
          </label>
          
          <div class="form-row">
            <label>Age: 
              <input type="text" name="age" required value="<?php echo $is_editing ? htmlspecialchars($edit_animal['age']) : (isset($_POST['age']) ? htmlspecialchars($_POST['age']) : ''); ?>">
            </label>
            
            <label>Gender:
              <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?php echo ($is_editing && $edit_animal['gender'] == 'Male') || (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($is_editing && $edit_animal['gender'] == 'Female') || (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
              </select>
            </label>
          </div>
        </div>
        
        <div class="form-center">
          <label>Image: 
            <input type="file" name="img" <?php echo !$is_editing ? 'required' : ''; ?> accept="image/*" onchange="previewImage(this)">
          </label>
          
          <label>Description:
            <textarea name="description" required placeholder="Describe the animal..."><?php echo $is_editing ? htmlspecialchars($edit_animal['description']) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''); ?></textarea>
          </label>
        </div>
        
        <div class="form-right">
          <div class="image-preview-container">
            <?php if ($is_editing): ?>
              <img id="imagePreview" src="<?php echo htmlspecialchars($edit_animal['image_url']); ?>" alt="Current Image" class="image-preview">
            <?php else: ?>
              <img id="imagePreview" src="#" alt="Image Preview" class="image-preview" style="display: none;">
              <div id="imagePlaceholder" class="image-placeholder">
                <span>📷</span>
                <p>Image preview will appear here</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <?php if ($is_editing): ?>
        <div class="button-group">
          <button type="submit" name="update_animal">Update Animal</button>
          <button type="button" class="cancel-btn" onclick="window.location.href='add.php'">Cancel</button>
        </div>
      <?php else: ?>
        <button type="submit" name="add_animal">Add Animal</button>
      <?php endif; ?>
    </form>
  </main>

  <main class="tips-container">
    <h2 class="tips-title">Your Added Animals</h2>
    <section class="animals">
      <?php
      $user_id = $_SESSION['user_id'];
      $stmt = $conn->prepare("SELECT * FROM animals WHERE user_id = ? ORDER BY animal_id DESC");
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      
      if ($result->num_rows > 0):
        while ($animal = $result->fetch_assoc()):
      ?>
        <div class="animal-card">
          <img src="<?php echo htmlspecialchars($animal['image_url']); ?>" alt="Animal" onclick="zoomImage('<?php echo htmlspecialchars($animal['image_url']); ?>')">
          <p><strong>Name:</strong> <?php echo htmlspecialchars($animal['name']); ?></p>
          <p><strong>Type:</strong> <?php echo htmlspecialchars($animal['type']); ?></p>
          <p><strong>Age:</strong> <?php echo htmlspecialchars($animal['age']); ?></p>
          <p><strong>Gender:</strong> <?php echo htmlspecialchars($animal['gender']); ?></p>
          
          <div class="animal-actions">
            <a href="add.php?edit_id=<?php echo $animal['animal_id']; ?>" class="toggle-update-btn">Edit</a>
            <button type="button" class="delete-btn" onclick="confirmDelete(<?php echo $animal['animal_id']; ?>)">Delete</button>
          </div>
        </div>
      <?php 
        endwhile;
      else:
      ?>
        <p style="text-align: center; color: #666;">No animals added yet.</p>
      <?php endif; ?>
    </section>
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
    // دالة حذف الحيوان
    function confirmDelete(animalId) {
      showCustomConfirm('Are you sure you want to delete this animal?', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_id';
        input.value = animalId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
      });
    }

    // دالة إخفاء الرسالة بعد ثانيتين
    function hideMessage() {
      const messageBox = document.getElementById('messageBox');
      if (messageBox) {
        setTimeout(() => {
          messageBox.style.display = 'none';
        }, 2000); // 2000 ميللي ثانية = 2 ثانية
      }
    }

    // استدعاء الدالة عند تحميل الصفحة
    window.onload = hideMessage;
  </script>
</body>
</html>