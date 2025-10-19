<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST; // استقبال البيانات كـ JSON
	if (isset($data['action']) && $data['action'] === 'add_animal') {
        $name = isset($data['name']) ? $data['name'] : '';
        $type = isset($data['type']) ? $data['type'] : '';
        $age = isset($data['age']) ? $data['age'] : '';
        $gender = isset($data['gender']) ? $data['gender'] : '';
        $description = isset($data['description']) ? $data['description'] : '';
        $image_url = isset($data['image_url']) ? $data['image_url'] : '';
        $user_id = isset($data['user_id']) ? $data['user_id'] : '';

        $errors = [];
        if (empty($name)) {
            $errors[] = "name is required";
        }
        if (empty($type)) {
            $errors[] = "type is required";
        }
        if (empty($age)) {
            $errors[] = "age is required";
        }
        if (empty($gender)) {
            $errors[] = "gender is required";
        }
        if (empty($description)) {
            $errors[] = "description is required";
        }
        if (empty($image_url)) {
            $errors[] = "image is required";
        }
        if (empty($user_id)) {
            $errors[] = "user info is required";
        }
		$stmt = $conn->prepare("INSERT INTO animals (name, type, age, gender, description, image_url, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssssi", $name, $type, $age, $gender, $description, $image_url, $user_id);
        
        if ($stmt->execute()) {
            
            // إرجاع نجاح مع معرف المستخدم
            http_response_code(201); // تم الإنشاء
            echo json_encode([
                "success" => true, 
                "message" => "User registered successfully",
            ]);
        } else {
            // إرجاع خطأ إذا فشل إنشاء المستخدم
            http_response_code(500);
            echo json_encode([
                "success" => false, 
                "errors" => ["Failed to register user: " . $stmt->error]
            ]);
        }
        
        $stmt->close();
	}else {
        // إرجاع خطأ إذا لم يتم تحديد الإجراء أو لم يكن 'signup'
        http_response_code(400);
        echo json_encode(["success" => false, "errors" => ["Invalid action"]]);
    }
}else {
    // إرجاع خطأ إذا لم يكن الطلب من نوع POST
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "errors" => ["Method not allowed"]]);
}

// إغلاق اتصال قاعدة البيانات
$conn->close();
?>