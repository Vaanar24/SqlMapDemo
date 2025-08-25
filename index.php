<?php
$host = "localhost";
$db   = "demo";
$user = "root";
$pass = "";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
$function = $_POST['function'] ?? '';
$search   = $_POST['search'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SQLMap Demo (PHP + MySQL)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-center">SQLMap Demo (PHP + MySQL)</h2>
        <form method="post" class="mb-4">
            <div class="mb-3">
                <label for="function" class="form-label">Choose Function:</label>
                <select name="function" id="function" class="form-select" onchange="toggleInputs(this.value)">
                    <option value="">-- Select --</option>
                    <option value="vulnerable_search" <?= $function==='vulnerable_search'?'selected':'' ?>>Search (Vulnerable)</option>
                    <option value="secure_search" <?= $function==='secure_search'?'selected':'' ?>>Search (Secure)</option>
                    <option value="login" <?= $function==='login'?'selected':'' ?>>Login (Vulnerable)</option>
                </select>
            </div>

            <div id="searchBox" class="mb-3" style="display:none;">
                <label class="form-label">Enter Search Term:</label>
                <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div id="loginBox" class="mb-3" style="display:none;">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control mb-2" value="<?= htmlspecialchars($username) ?>">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" value="<?= htmlspecialchars($password) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <div class="mt-4">
        <?php
        if ($function === "vulnerable_search" && $search) {
            echo "<h5 class='text-danger'>Vulnerable Search Results:</h5>";
            $sql = "SELECT * FROM users WHERE username LIKE '%$search%'";
            try {
                $results = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if ($results) {
                    echo "<table class='table table-bordered'><tr><th>ID</th><th>Username</th><th>Email</th></tr>";
                    foreach ($results as $row) {
                        echo "<tr>
                                <td>".htmlspecialchars($row['id'])."</td>
                                <td>".htmlspecialchars($row['username'])."</td>
                                <td>".htmlspecialchars($row['email'])."</td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<div class='alert alert-warning'>No results found.</div>";
                }
                echo "<div class='alert alert-danger'>Vulnerable: Direct string concatenation allows SQL Injection!</div>";
                echo "<p><b>Try:</b> <code>' OR '1'='1</code></p>";
            } catch (PDOException $e) {
                echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
            }
        }

        if ($function === "secure_search" && $search) {
            echo "<h5 class='text-success'>Secure Search Results:</h5>";
            $sql = "SELECT * FROM users WHERE username LIKE :search";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':search' => "%$search%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                echo "<table class='table table-striped'><tr><th>ID</th><th>Username</th><th>Email</th></tr>";
                foreach ($results as $row) {
                    echo "<tr>
                            <td>".htmlspecialchars($row['id'])."</td>
                            <td>".htmlspecialchars($row['username'])."</td>
                            <td>".htmlspecialchars($row['email'])."</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<div class='alert alert-warning'>No results found.</div>";
            }
            echo "<div class='alert alert-success'>Secure: Prepared statements prevent SQL Injection.</div>";
        }

        if ($function === "login" && $username && $password) {
            echo "<h5 class='text-danger'>Login Attempt:</h5>";
            $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
            $results = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            if ($results) {
                echo "<div class='alert alert-success'>Login Successful! Welcome, ".htmlspecialchars($username).".</div>";
            } else {
                echo "<div class='alert alert-danger'>Login Failed! Invalid credentials.</div>";
            }
            echo "<p class='text-danger'><b>Vulnerable:</b> Raw SQL allows SQL Injection.</p>";
            echo "<p><b>Try:</b> username: <code>' OR '1'='1</code>, password: <code>anything</code></p>";
        }
        ?>
        </div>
    </div>
</div>

<script>
function toggleInputs(val) {
    document.getElementById('searchBox').style.display = (val === 'vulnerable_search' || val === 'secure_search') ? 'block' : 'none';
    document.getElementById('loginBox').style.display = (val === 'login') ? 'block' : 'none';
}
toggleInputs("<?= $function ?>");
</script>
</body>
</html>