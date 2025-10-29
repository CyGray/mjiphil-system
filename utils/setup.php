<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup | MJIPhil Construction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-center">Database Setup</h2>
                    </div>
                    <div class="card-body">
                        <?php
                        require_once 'setup_database.php';
                        $setup = new DatabaseSetup();
                        $setup->setupDatabase();
                        ?>
                        <div class="text-center mt-4">
                            <a href="../login.php" class="btn btn-primary">Go to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>