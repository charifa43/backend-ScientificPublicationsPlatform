<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scientific Publications Platform</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>
<body>
    <div id="root"></div>
    
    <!-- Votre bundle React (compilé dans public/js/app.js) -->
    <?php if(app()->environment('production')): ?>
        <script src="<?php echo e(mix('js/app.js')); ?>"></script>
    <?php else: ?>
        <script src="http://localhost:3000/static/js/bundle.js"></script>
    <?php endif; ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\ScientificPublicationsPlatform\resources\views/app.blade.php ENDPATH**/ ?>