<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "leave_requests";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// جلب الطلبات
$orders_sql = "
    SELECT id, student_name, request_type, status, created_at FROM (
        SELECT id, student_name, request_type, status, created_at FROM leaves
        UNION ALL
        SELECT id, student_name, request_type, status, created_at FROM student_requests
        UNION ALL
        SELECT id, name AS student_name, request_type, status, created_at FROM student_transfers
        UNION ALL
        SELECT id, student_name, request_type, status, created_at FROM tchfed
    ) AS combined
    ORDER BY created_at DESC
    LIMIT 10
";

$result = $conn->query($orders_sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الطلبات</title>
    <link rel="stylesheet" href="order.css"> <!-- ملف CSS -->
    
</head>
<body>

    <div class="container">
        <!-- الشريط الجانبي -->

        <aside class="sidebar">
            <div class="logo">MUC</div>
            <ul>
                <li><a href="dashboard.php">لوحة التحكم</a></li>
                <li><a href="orders.php">الطلبات</a></li>
                <li><a href="opshen.php">الإعدادات</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <!-- بيانات المدير -->
            <header class="admin-header">
                <div class="header-actions">
                    <button class="icon-btn mode-toggle"><i class="fas fa-moon"></i></button>
                    <button class="icon-btn"><i class="fas fa-bell"></i></button>
                </div>
                <div class="admin-info">
                    <img id="admin-image" src="default.png" alt="Admin">
                    <div class="admin-text">
                        <h3 id="admin-name">تحميل...</h3>
                        <span>مدير النظام</span>
                    </div>
                </div>
            </header>

        <!-- المحتوى الرئيسي -->
        <main class="main-content">
            <h2>جميع الطلبات</h2>
            <table>
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>اسم الطالب</th>
                        <th>نوع الطلب</th>
                        <th>الحالة</th>
                        <th>تاريخ التقديم</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['request_type']); ?></td>
                        <td>
                            <select class="status-select" data-id="<?php echo $row['id']; ?>">
                                <option value="في الانتظار" <?php echo ($row['status'] == 'في الانتظار') ? 'selected' : ''; ?>>في الانتظار</option>
                                <option value="لقد تم رؤيتها" <?php echo ($row['status'] == 'لقد تم رؤيتها') ? 'selected' : ''; ?>>لقد تم رؤيتها</option>
                                <option value="تم الموافقة على الطلب" <?php echo ($row['status'] == 'تم الموافقة على الطلب') ? 'selected' : ''; ?>>تم الموافقة على الطلب</option>
                                <option value="تم رفض الطلب" <?php echo ($row['status'] == 'تم رفض الطلب') ? 'selected' : ''; ?>>تم رفض الطلب</option>
                            </select>
                        </td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>

    <script>
document.addEventListener("DOMContentLoaded", function () {
    let actionContainer = document.createElement("div");
    actionContainer.id = "action-container";
    actionContainer.style.display = "none";
    actionContainer.style.position = "fixed";
    actionContainer.style.top = "50%";
    actionContainer.style.left = "50%";
    actionContainer.style.transform = "translate(-50%, -50%)";
    actionContainer.style.padding = "20px";
    actionContainer.style.background = "#fff";
    actionContainer.style.boxShadow = "0px 0px 15px rgba(0,0,0,0.2)";
    actionContainer.style.borderRadius = "8px";
    actionContainer.style.textAlign = "center";
    actionContainer.style.zIndex = "1000";

    // عناصر الحاوية
    let title = document.createElement("h3");
    title.textContent = "تأكيد تغيير الحالة";

    let authorityInput = document.createElement("input");
    authorityInput.type = "text";
    authorityInput.placeholder = "ادخل الجهة الطالبة للتأييد";
    authorityInput.style.margin = "10px 0";
    authorityInput.style.padding = "10px";
    authorityInput.style.width = "100%";
    authorityInput.style.display = "none";

    let sendButton = document.createElement("button");
    sendButton.textContent = "إرسال التأييد";
    sendButton.style.background = "#007bff";
    sendButton.style.color = "#fff";
    sendButton.style.border = "none";
    sendButton.style.padding = "10px 20px";
    sendButton.style.borderRadius = "5px";
    sendButton.style.cursor = "pointer";
    sendButton.style.display = "none";

    let confirmButton = document.createElement("button");
    confirmButton.textContent = "تأكيد التغيير";
    confirmButton.style.margin = "10px";
    confirmButton.style.padding = "10px 20px";
    confirmButton.style.background = "#28a745";
    confirmButton.style.color = "#fff";
    confirmButton.style.border = "none";
    confirmButton.style.cursor = "pointer";
    confirmButton.style.fontSize = "16px";
    confirmButton.style.borderRadius = "5px";

    let cancelButton = document.createElement("button");
    cancelButton.textContent = "إلغاء التغيير";
    cancelButton.style.margin = "10px";
    cancelButton.style.padding = "10px 20px";
    cancelButton.style.background = "#dc3545";
    cancelButton.style.color = "#fff";
    cancelButton.style.border = "none";
    cancelButton.style.cursor = "pointer";
    cancelButton.style.fontSize = "16px";
    cancelButton.style.borderRadius = "5px";

    actionContainer.appendChild(title);
    actionContainer.appendChild(authorityInput);
    actionContainer.appendChild(sendButton);
    actionContainer.appendChild(confirmButton);
    actionContainer.appendChild(cancelButton);
    document.body.appendChild(actionContainer);

    let changes = {};
    let currentStudent = "";
    let currentRequestId = "";

    document.querySelectorAll(".status-select").forEach(select => {
        select.addEventListener("change", function () {
            let requestId = this.getAttribute("data-id");
            let newStatus = this.value;
            let row = select.closest("tr");
            let studentName = row.children[1].textContent.trim();
            let requestType = row.children[2].textContent.trim();

            currentStudent = studentName;
            currentRequestId = requestId;

            changes[requestId] = newStatus;
            authorityInput.value = "";

            if (newStatus === "تم الموافقة على الطلب" && requestType === "تأييد معنون") {
                authorityInput.style.display = "block";
                sendButton.style.display = "inline-block";
            } else {
                authorityInput.style.display = "none";
                sendButton.style.display = "none";
            }

            actionContainer.style.display = "block";
        });

        select.setAttribute("data-original", select.value);
    });

    sendButton.addEventListener("click", function () {
        const authority = authorityInput.value.trim();
        if (!authority) {
            alert("الرجاء إدخال الجهة الطالبة.");
            return;
        }

        fetch("create_tayid_pdf.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `student_name=${encodeURIComponent(currentStudent)}&authority=${encodeURIComponent(authority)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.open(data.pdf_url, "_blank");
                fetch("send_tayid.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `student_name=${encodeURIComponent(currentStudent)}&authority=${encodeURIComponent(authority)}&pdf_path=${encodeURIComponent(data.pdf_url)}`
                })
                .then(res => res.json())
                .then(r => {
                    if (r.sent) {
                        alert("تم إرسال التأييد للطالب بنجاح.");
                    } else {
                        alert("فشل إرسال التأييد.");
                    }
                });
            } else {
                alert("فشل إنشاء التأييد.");
            }
        });
    });

    confirmButton.addEventListener("click", function () {
        if (Object.keys(changes).length === 0) return;

        let requests = Object.entries(changes)
            .map(([id, status]) => `id[]=${id}&status[]=${encodeURIComponent(status)}`)
            .join("&");

        fetch("update_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: requests
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error("خطأ أثناء تحديث الحالة:", error));
    });

    cancelButton.addEventListener("click", function () {
        document.querySelectorAll(".status-select").forEach(select => {
            select.value = select.getAttribute("data-original");
        });
        changes = {};
        actionContainer.style.display = "none";
    });
});

document.addEventListener("DOMContentLoaded", function () {
    fetch("admin_info.php")
        .then(response => response.json())
        .then(data => {
            if (data.name && data.id) {
                document.getElementById("admin-name").textContent = data.name;
                document.getElementById("admin-image").src = `get_image.php?id=${data.id}`;
            }
        })
        .catch(error => console.error("خطأ في تحميل بيانات المدير:", error));
});
</script>


</body>
</html>
