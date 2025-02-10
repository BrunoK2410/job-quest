<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #333; }
        .container { border: 1px solid #ddd; padding: 15px; border-radius: 8px; }
        .section { margin-bottom: 10px; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>

    <h1>Job Details</h1>
    <div class="container">
        <div class="section">
            <span class="bold">Job Title:</span> {{ $job->title }}
        </div>
        <div class="section">
            <span class="bold">Description:</span> {{ $job->description }}
        </div>
        <div class="section">
            <span class="bold">Job Type:</span> {{ $job->job_type }}
        </div>
        <div class="section">
            <span class="bold">Remote:</span> {{ $job->remote ? 'Yes' : 'No' }}
        </div>
        <div class="section">
            <span class="bold">Salary:</span> ${{ number_format($job->salary) }}
        </div>
        <div class="section">
            <span class="bold">Location:</span> {{ $job->city }}, {{ $job->state }}
        </div>
        @if($job->tags)
        <div class="section">
            <span class="bold">Tags:</span> {{ ucwords(str_replace(',', ', ', $job->tags)) }}
        </div>
        @endif
        @if($job->requirements)
        <div class="section">
            <span class="bold">Requirements:</span> {{ $job->requirements }}
        </div>
        @endif
        @if($job->benefits)
        <div class="section">
            <span class="bold">Benefits:</span> {{ $job->benefits }}
        </div>
        @endif
    </div>

    <br>
    <footer>
        <p>Generated on {{ now()->format('d-m-Y') }}</p>
    </footer>

</body>
</html>