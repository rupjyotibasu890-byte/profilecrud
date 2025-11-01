<?php
include 'dbconnect.php';
$feedback = "";

// When user submits codename
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_code'])) {
    $codename = trim($_POST['codename']);

    $stmt = $conn->prepare("SELECT * FROM profiles WHERE codename = ?");
    $stmt->bind_param("s", $codename);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();
    $stmt->close();
}

// When user submits profile info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $codename = trim($_POST['codename']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $check = $conn->prepare("SELECT * FROM profiles WHERE codename = ?");
    $check->bind_param("s", $codename);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $update = $conn->prepare("UPDATE profiles SET name = ?, email = ? WHERE codename = ?");
        $update->bind_param("sss", $name, $email, $codename);
        $update->execute();
        $feedback = $update->affected_rows > 0 ? "✅ Profile updated successfully!" : "No changes made.";
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO profiles (codename, name, email) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $codename, $name, $email);
        if ($insert->execute()) {
            $feedback = "🆕 New profile created successfully!";
        } else {
            $feedback = "Error creating profile: " . $conn->error;
        }
        $insert->close();
    }
    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile CRUD</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            margin: 0;
            padding: 0;
        }
        .container {
            width: 420px;
            margin: 70px auto;
            background: #fff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #00796b;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: 600;
            color: #333;
        }
        input[type="text"], input[type="email"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }
        button {
            padding: 10px;
            background: #00796b;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #004d40;
        }
        .feedback {
            text-align: center;
            color: #00695c;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>👤 Profile Update or Create</h2>

    <?php if (!isset($profile) && !isset($_POST['save_profile'])): ?>
        <!-- Ask for codename -->
        <form method="POST">
            <label>Enter your unique codename:</label>
            <input type="text" name="codename" required>
            <button type="submit" name="check_code">Continue</button>
        </form>

    <?php elseif (isset($profile)): ?>
        <!-- Edit existing profile -->
        <form method="POST">
            <input type="hidden" name="codename" value="<?= htmlspecialchars($profile['codename']) ?>">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($profile['name']) ?>" required>
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required>
            <button type="submit" name="save_profile">Update Profile</button>
        </form>

    <?php elseif (isset($_POST['check_code']) && empty($profile)): ?>
        <!-- New profile form -->
        <form method="POST">
            <input type="hidden" name="codename" value="<?= htmlspecialchars($_POST['codename']) ?>">
            <label>Codename: <?= htmlspecialchars($_POST['codename']) ?></label>
            <label>Name:</label>
            <input type="text" name="name" placeholder="Enter your name" required>
            <label>Email:</label>
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="save_profile">Create Profile</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($feedback)): ?>
        <p class="feedback"><?= htmlspecialchars($feedback) ?></p>
    <?php endif; ?>
</div>
</body>
</html>

