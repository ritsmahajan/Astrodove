<?php
// api.php - Product Management API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Verify admin session
session_start();
if ($action !== 'login' && !isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$conn = getDBConnection();

// Handle different actions
switch ($action) {
    case 'login':
        handleLogin($conn);
        break;
    
    case 'logout':
        handleLogout();
        break;
    
    case 'add':
        addProduct($conn);
        break;
    
    case 'update':
        updateProduct($conn);
        break;
    
    case 'delete':
        deleteProduct($conn);
        break;
    
    case 'get':
        getProducts($conn);
        break;
    
    case 'get_single':
        getSingleProduct($conn);
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}

$conn->close();

// Functions

function handleLogin($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
}

function handleLogout() {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}

function addProduct($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $conn->real_escape_string($data['name']);
    $price = floatval($data['price']);
    $category = $conn->real_escape_string($data['category']);
    $badge = $conn->real_escape_string($data['badge'] ?? '');
    $description = $conn->real_escape_string($data['description']);
    $benefits = $conn->real_escape_string(json_encode($data['benefits'] ?? []));
    $image = $conn->real_escape_string($data['image']);
    
    $sql = "INSERT INTO products (name, price, category, badge, description, benefits, image) 
            VALUES ('$name', $price, '$category', '$badge', '$description', '$benefits', '$image')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode([
            'success' => true, 
            'message' => 'Product added successfully',
            'id' => $conn->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add product: ' . $conn->error]);
    }
}

function updateProduct($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($data['id']);
    $name = $conn->real_escape_string($data['name']);
    $price = floatval($data['price']);
    $category = $conn->real_escape_string($data['category']);
    $badge = $conn->real_escape_string($data['badge'] ?? '');
    $description = $conn->real_escape_string($data['description']);
    $benefits = $conn->real_escape_string(json_encode($data['benefits'] ?? []));
    $image = $conn->real_escape_string($data['image']);
    
    $sql = "UPDATE products SET 
            name='$name', 
            price=$price, 
            category='$category', 
            badge='$badge', 
            description='$description', 
            benefits='$benefits', 
            image='$image' 
            WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update product: ' . $conn->error]);
    }
}

function deleteProduct($conn) {
    $id = intval($_GET['id'] ?? 0);
    
    $sql = "DELETE FROM products WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete product: ' . $conn->error]);
    }
}

function getProducts($conn) {
    $sql = "SELECT * FROM products ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $products = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $row['benefits'] = json_decode($row['benefits']);
            $products[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'products' => $products]);
}

function getSingleProduct($conn) {
    $id = intval($_GET['id'] ?? 0);
    
    $sql = "SELECT * FROM products WHERE id=$id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $product['benefits'] = json_decode($product['benefits']);
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
}
?>
