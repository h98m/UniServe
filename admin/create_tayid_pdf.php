<?php
require('fpdf/fpdf.php'); // تأكد أن مكتبة FPDF موجودة في مجلد "fpdf/"

$student_name = $_POST['student_name'] ?? '';
$authority = $_POST['authority'] ?? '';
$date = date('Y-m-d');

$text = "نحن نؤيد بأن الطالب $student_name يدرس في كلية المنصور الجامعة – قسم هندسة الحاسوب، وقد طلب تأييدًا موجهًا إلى جهة: $authority.\n\nتاريخ الإصدار: $date";

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 14);
$pdf->SetRightMargin(10);
$pdf->SetLeftMargin(10);
$pdf->MultiCell(0, 10, iconv('UTF-8', 'windows-1256', $text));

$folder = 'tayid_files/';
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$filename = $folder . 'tayid_' . time() . '.pdf';
$pdf->Output('F', $filename);

echo json_encode(['success' => true, 'pdf_url' => $filename]);
