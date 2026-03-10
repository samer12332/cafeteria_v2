<?php
$pageTitle = $pageTitle ?? 'Cafeteria';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: "#fff7ed",
                            100: "#ffedd5",
                            200: "#fed7aa",
                            300: "#fdba74",
                            400: "#fb923c",
                            500: "#f97316",
                            600: "#ea580c",
                            700: "#c2410c",
                            800: "#9a3412",
                            900: "#7c2d12",
                        },
                    },
                    boxShadow: {
                        glow: "0 20px 45px rgba(234, 88, 12, 0.12)",
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-orange-100 font-['Sora',sans-serif] text-slate-900">
