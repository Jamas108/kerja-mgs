<!DOCTYPE html>
<html>
<head>
    <title>Sertifikat Promosi - {{ $employeeName }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            font-family: Arial, sans-serif;
        }

        body {
            background-image: url("{{ public_path('images/sertifikatbg.png') }}");
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        .content-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            box-sizing: border-box;
            padding: 40px 60px;
        }

        .cert-number {
            font-size: 18px;
            text-align: center;
            margin-top: 220px;
        }

        .content {
            font-size: 18px;
            line-height: 1.6;
            text-align: center;
            margin-top: 35px;
        }

        .content .nama {
            font-size: 55px;
            font-weight: bold;
            margin: 20px 0;
            color: #0047AB;
            margin-right: 115px;
            padding-top: 25px;
        }

        .content p {
            max-width: 800px;
            text-align: center;
            margin: 10px auto;
            margin-left: 110px;
        }

        .signature {
            position: absolute;
            bottom: 120px;
            left: 47%;
            transform: translateX(-50%);
            text-align: center;
        }

        .signature img {
            max-height: 80px;
            margin-bottom: 5px;
            margin-left: 110px;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <div class="cert-number"></div>

        <div class="content">
            <div class="nama">{{ $employeeName }}</div>
            <p><strong> Divisi: {{ $divisionName }}</strong></p>
            <p>
                Sebagai bentuk apresiasi atas kinerja yang luar biasa, dedikasi, dan
                profesionalisme yang telah ditunjukkan selama {{ $yearsOfService }}.
                Kontribusi dan kerja keras Anda telah memberikan dampak positif bagi
                perusahaan serta menjadi teladan bagi rekan kerja lainnya.
            </p>
        </div>

        <div class="signature">
            <div>{{ $directorTitle }}</div>
            <img src="{{ $signatureImageUrl }}" alt="Signature">
            <div style="font-weight: bold;">{{ $directorName }}</div>
            <div style="font-size: 14px; color: #666;">{{ $currentDate }}</div>
        </div>
    </div>
</body>
</html>