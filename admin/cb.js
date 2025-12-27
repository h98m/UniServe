document.addEventListener("DOMContentLoaded", function () {
    const modeToggle = document.querySelector(".mode-toggle");
    const body = document.body;
    const icon = modeToggle.querySelector("i");

    modeToggle.addEventListener("click", function () {
        body.classList.toggle("dark-mode");

        if (body.classList.contains("dark-mode")) {
            icon.classList.replace("fa-moon", "fa-sun");
        } else {
            icon.classList.replace("fa-sun", "fa-moon");
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    fetch("admin_info.php")
        .then(response => response.json())
        .then(data => {
            if (data.name && data.id) {
                document.getElementById("admin-name").textContent = data.name;
                document.getElementById("admin-image").src = `get_image.php?id=${data.id}`;
            } else {
                console.error("بيانات غير مكتملة:", data);
            }
        })
        .catch(error => console.error("خطأ في تحميل بيانات المدير:", error));
});
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("load-orders").addEventListener("click", function (event) {
        event.preventDefault(); // منع إعادة تحميل الصفحة

        fetch("orders.php") // تحميل صفحة الطلبات
            .then(response => response.text())
            .then(data => {
                document.querySelector(".main-content").innerHTML = data;
            })
            .catch(error => console.error("حدث خطأ أثناء تحميل الطلبات:", error));
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll(".load-page");
    const contentArea = document.getElementById("content-area");

    if (!contentArea) {
        console.error("العنصر #content-area غير موجود في الصفحة!");
        return;
    }

    links.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault(); // منع إعادة تحميل الصفحة بالكامل

            const pageUrl = this.getAttribute("href");

            fetch(pageUrl)
                .then(response => {
                    if (!response.ok) throw new Error("خطأ في تحميل الصفحة");
                    return response.text();
                })
                .then(data => {
                    contentArea.innerHTML = data;
                    window.history.pushState(null, "", pageUrl); // تحديث رابط الصفحة بدون تحميلها
                })
                .catch(error => console.error("حدث خطأ أثناء تحميل الصفحة:", error));
        });
    });

    // معالجة الرجوع للخلف في المتصفح
    window.addEventListener("popstate", function () {
        const currentPage = window.location.pathname;
        fetch(currentPage)
            .then(response => response.text())
            .then(data => {
                contentArea.innerHTML = data;
            })
            .catch(error => console.error("حدث خطأ أثناء تحميل الصفحة:", error));
    });
});


setInterval(updateDashboard, 5000); // تحديث كل 5 ثوانٍ
