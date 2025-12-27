fetch('get_requests.php')
    .then(response => {
        // التحقق من أن الاستجابة كانت صحيحة
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text(); // جلب الاستجابة كنص عادي
    })
    .then(html => {
        // إدراج البيانات المستلمة في الصفحة
        document.getElementById('requests-list').innerHTML = html;
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        document.getElementById('requests-list').innerHTML = 'حدث خطأ أثناء تحميل البيانات.';
    });
