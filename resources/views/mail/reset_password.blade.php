<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Şifrənizi yeniləyin</title>
</head>

<body>
    <h1>{{ config('app.name') }}</h1>
    <h4>Şifrənizi sıfırlamaq üçün aşağıdakı linkə daxil olun!</h4>
    <a href='{{ $link }}'>Şifrənizi yeniləyin</a>
</body>

</html>
