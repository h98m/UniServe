<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات المستخدم</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            direction: rtl;
            display: flex;
            height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #004aad;
            color: white;
            height: 100vh;
            padding: 20px;
            position: fixed;
            right: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .sidebar .logo {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            width: 100%;
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            font-size: 16px;
            padding: 15px;
            transition: background 0.3s;
        }

        .sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .wrapper {
            margin-right: 260px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: calc(100% - 260px);
        }
        
        .container {
            width: 50%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: block;
            margin: 0 auto 15px;
        }
        
        .user-info label {
            font-weight: bold;
            display: block;
            text-align: right;
            margin: 15px 0px 5px;
        }
        
        .user-info input {
            width: 98%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
            background: #f9f9f9;
            text-align: right;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">MUC</div>
        <ul>
            <li><a href="dashboard.php">لوحة التحكم</a></li>
            <li><a href="orders.php">الطلبات</a></li>
            <li><a href="opshen.php">الإعدادات</a></li>
        </ul>
    </aside>
    <div class="wrapper">
        <div class="container">
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "admin";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("<p style='color: red;'>فشل الاتصال بقاعدة البيانات: " . $conn->connect_error . "</p>");
            }

            $search_username = "gclm";
            $sql = "SELECT id, username, password, phone, name, photo FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $search_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<img class="profile-img" src="data:image/jpeg;base64,' . base64_encode($row["photo"]) . '" alt="صورة المستخدم">';
                    echo '<div class="user-info">';
                    echo '<label>الاسم الكامل:</label>';
                    echo '<input type="text" value="' . $row["name"] . '" readonly>';
                    echo '<label>اسم المستخدم:</label>';
                    echo '<input type="text" value="' . $row["username"] . '" readonly>';
                    echo '<label>كلمة المرور:</label>';
                    echo '<input type="text" value="' . $row["password"] . '" readonly>';
                    echo '<label>رقم الهاتف:</label>';
                    echo '<input type="text" value="+' . $row["phone"] . '" readonly>';
                    echo '</div>';
                }
            } else {
                echo "<p>لا توجد بيانات للمستخدم المطلوب.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
