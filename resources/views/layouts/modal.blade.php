<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="modal-container">
        <div class="modal-content">
            <span class="close-btn" onclick="closeAdopterProfile()">&times;</span>
            @yield('content')
        </div>
    </div>

    <style>
        .modal-container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px 10px 0 0;
            z-index: 100;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            position: relative;
            box-shadow: 0px -2px 10px rgba(0, 0, 0, 0.2);
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
        }
    </style>

    <script>
        function closeAdopterProfile() {
            document.querySelector(".modal-container").style.display = "none";
        }
    </script>
</body>
</html>
