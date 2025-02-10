<x-layout>
    <section class="flex flex-col md:flex-row gap-6">
        <div class="bg-gray-800 p-8 rounded-lg shadow-md w-full md:w-1/2">
            <h3 class="text-3xl text-center text-white font-bold mb-4">
                Profile Info
            </h3>
            @if ($user->avatar)
                <div class="mt-2 flex justify-center">
                    <img src="{{ $user->avatar }}" alt="{{$user->name}}" class="w-32 h-32 object-cover rounded-full">
                </div>
            @endif
            <form
                method="POST"
                action="{{route('profile.update')}}"
                enctype="multipart/form-data"
            >
            @csrf
            @method('PUT')

                <x-inputs.text name="name" id="name" label="Name" value="{{$user->name}}" />
                <x-inputs.text name="email" id="email" label="Email" value="{{$user->email}}" />
                <x-inputs.file name="avatar" id="avatar" label="Upload avatar" />
                <button
                    type="submit"
                    class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 my-3 rounded focus:outline-none"
                >
                    Save
                </button>
            </form>
        </div>

        <div class="bg-gray-800 p-8 rounded-lg shadow-md w-full">
            <h3 class="text-3xl text-center text-white font-bold mb-4">
                My Job Listings
            </h3>
            
            @forelse ($jobs as $job)
                <div class="flex justify-between items-center border-b-2 border-gray-200 py-2">
                    <div>
                        <h3 class="text-xl text-amber-300 font-semibold">
                            {{$job->title}}
                        </h3>
                        <p class="text-amber-100">{{$job->job_type}}</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{route('jobs.edit', $job->id)}}?from=dashboard" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded text-sm">Edit</a>
                        <form method="POST" action="{{route('jobs.destroy', $job->id)}}?from=dashboard" onsubmit="return confirm('Are you sure that you want to delete this job?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
                      {{-- Applicants --}}
      <div class="mt-4 bg-gray-900 p-2">
        <h4 class="text-lg text-amber-300 font-semibold mb-2">Applicants</h4>
        @forelse($job->applicants as $applicant)
        <div class="py-2">
          <p class="text-amber-100">
            <strong>Name: </strong> {{$applicant->full_name}}
          </p>
          <p class="text-amber-100">
            <strong>Phone: </strong> {{$applicant->contact_phone}}
          </p>
          <p class="text-amber-100">
            <strong>Email: </strong> {{$applicant->contact_email}}
          </p>
          <p class="text-amber-100">
            <strong>Message: </strong> {{$applicant->message}}
          </p>
          <p class="text-amber-100 mt-2">
            <a href="{{ route('resume.download', $applicant->id) }}" 
               class="text-blue-500 hover:underline text-sm"
               target="_blank" 
               rel="noopener noreferrer">
                <i class="fas fa-download"></i> Download Resume
            </a>
        </p>
          {{-- Delete Applicant --}}
          <form method="POST" action="{{route('applicant.destroy', $applicant->id)}}"
            onsubmit="return confirm('Are you sure you want to delete this applicant?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
              <i class="fas fa-trash"></i> Delete Applicant
            </button>
          </form>
        </div>
        @empty
        <p class="text-gray-300 mb-5">No applicants for this job</p>
        @endforelse
      </div>
            @empty
                <p class="text-gray-300">You have no job listings</p>
            @endforelse

        </div>
    </section>
</x-layout>