<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title><?= $siteName ?></title>
</head>
<body class="has-navbar-fixed-top">
    <!-- Add your content here -->
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete').forEach(function (button) {
                button.addEventListener('click', function () {
                    const notification = this.parentElement;
                    if (notification) {
                        notification.remove();
                    }
                });
            });
        });
    </script>
</body>
</html>



