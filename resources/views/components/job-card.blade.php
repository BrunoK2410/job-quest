@props(['job'])

<div class="rounded-lg shadow-md bg-gray-800 text-amber-100 p-5 flex flex-col justify-between h-full">
    <div class="flex items-center gap-4">
        @if($job->company_logo)
        <div class="w-12 h-12 flex-shrink-0">
            <img src="/storage/{{$job->company_logo}}" alt="{{$job->company_name}}" class="rounded-full object-cover w-full h-full">
        </div>
        @endif
        <div>
            <h2 class="text-xl font-semibold text-amber-300">{{$job->title}}</h2>
            <p class="text-sm text-gray-400">{{$job->job_type}}</p>
        </div>
    </div>

    <p class="text-gray-300 text-sm mt-2 line-clamp-2">
        {{Str::limit($job->description, 120)}}
    </p>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <span class="bg-gray-900 px-3 py-1 text-xs rounded-md shadow-sm">
            💰 <strong>Salary:</strong> ${{ number_format($job->salary) }}
        </span>
        <span class="bg-gray-900 px-3 py-1 text-xs rounded-md shadow-sm">
            📍 <strong>Location:</strong> {{$job->city}}, {{$job->state}}
        </span>
        @if($job->remote)
        <span class="bg-green-600 text-white px-3 py-1 text-xs rounded-md">Remote</span>
        @else
        <span class="bg-red-700 text-white px-3 py-1 text-xs rounded-md">On-site</span>
        @endif
    </div>

    @if($job->tags)
    <div class="mt-2 flex flex-wrap gap-2">
        @foreach(explode(',', $job->tags) as $tag)
        <span class="bg-amber-500 text-gray-900 px-2 py-1 text-xs rounded-md">{{ ucwords(trim($tag)) }}</span>
        @endforeach
    </div>
    @endif

    <a href="{{route('jobs.show', $job->id)}}" 
       class="mt-4 block text-center px-5 py-2 rounded shadow-sm border border-amber-500 hover:border-amber-600 text-base font-medium text-gray-900 bg-amber-500 hover:bg-amber-600 transition duration-200">
        View Job Details
    </a>
</div>