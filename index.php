<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProServices | Digital Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 0; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #764ba2; border: none; padding: 10px 30px; border-radius: 25px; }
        .btn-primary:hover { background-color: #5a3682; }
    </style>
</head>
<body>

<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Elevate Your Digital Presence</h1>
        <p class="lead">Select a service and let our experts handle the rest.</p>
    </div>
</section>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4">
                <h3 class="text-center mb-4">Request a Service</h3>
                <form id="orderForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="userName" placeholder="e.g. Ahmed Samy" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Select Service</label>
                        <select class="form-select" id="serviceType">
                            <option value="Web Development">Web Development</option>
                            <option value="DevOps Automation">DevOps Automation</option>
                            <option value="Cloud Migration">Cloud Migration</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
                <div id="resultArea" class="mt-4"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('userName').value;
        const service = document.getElementById('serviceType').value;
        
       
        const resultArea = document.getElementById('resultArea');
        resultArea.innerHTML = `
            <div class="alert alert-success animate__animated animate__fadeIn">
                <strong>Success!</strong> Hello ${name}, your request for ${service} has been received.
            </div>`;
    });
</script>

</body>
</html>