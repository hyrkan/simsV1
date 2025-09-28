<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Admission Form - {{ config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            background: #fefefe;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admission-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin: 2rem auto;
            max-width: 1200px;
        }
        
        .header-section {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
            text-align: center;
        }
        
        .header-section h1 {
            margin: 0;
            font-weight: 700;
            font-size: 2.5rem;
        }
        
        .header-section p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="admission-container">
            <div class="header-section">
                <h1><i class="fas fa-graduation-cap me-3"></i>Student Admission Form</h1>
                <p>Complete all steps to submit your admission application</p>
            </div>
            
            <div id="app">
                <admission-stepper></admission-stepper>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>