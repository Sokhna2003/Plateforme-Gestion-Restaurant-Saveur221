<?php

declare(strict_types=1);

$message = flash_messages();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? '') ?> - Saveur 221</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            brown: '#5C2E1A',
                            orange: '#D95F02',
                            cream: '#FFF9F3',
                        }
                    },
                    fontFamily: {
                        serif: ['Fraunces', 'serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-brand-cream text-brand-brown font-sans">

<div class="min-h-screen flex">

    <!-- Colonne gauche : image -->
    <div class="hidden lg:flex lg:w-1/2 relative bg-brand-brown">
        <img src="<?= $base ?>/assets/images/connexion.jpg"
             alt="Restaurant Saveur 221"
             class="absolute inset-0 w-full h-full object-cover opacity-60">
        <div class="relative z-10 flex flex-col justify-end p-12 text-white">
            <span class="w-10 h-10 rounded-full bg-brand-orange flex items-center justify-center font-serif font-bold text-sm mb-4">S</span>
            <h2 class="font-serif text-3xl font-bold mb-2">SAVEUR 221</h2>
            <p class="text-white/80 text-sm max-w-xs">L'authentique gastronomie sénégalaise, directement chez vous.</p>
        </div>
    </div>

    <!-- Colonne droite : formulaire -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">

            <?php if ($message): ?>
                <div class="mb-6 px-4 py-3 rounded-xl text-sm
                    <?= $message['type'] === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200' ?>">
                    <?= e($message['message']) ?>
                </div>
            <?php endif; ?>

            <?= $content ?>

        </div>
    </div>

</div>

</body>
</html>
