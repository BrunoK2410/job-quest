<x-layout>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <section class="md:col-span-3">
            <div class="rounded-lg shadow-md bg-gray-800 text-amber-100 p-3">
                <div class="flex justify-between items-center">
                    <a
                        class="block p-4 text-amber-500 font-bold"
                        href="{{route('jobs.index')}}"
                    >
                        <i class="fa fa-arrow-alt-circle-left"></i>
                        Back To Listings
                    </a>
                    <div class="flex items-center space-x-3 p-4">
                        <a href="{{ route('jobs.pdf', $job->id) }}" class="bg-red-500 hover:bg-red-600 w-[32px] h-[32px] text-white rounded inline-flex items-center justify-center">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @can('update',$job)
                        <a
                            href="{{route('jobs.edit', $job->id)}}"
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded"
                            >Edit</a
                        >
                        <form method="POST" action="{{route('jobs.destroy',$job->id)}}" onsubmit="return confirm('Are you sure that you want to delete this job?')">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded"
                            >
                                Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                <div class="p-4">
                    <h2 class="text-xl font-bold">
                        {{$job->title}}
                    </h2>
                    <p class="text-gray-300 text-lg mt-2">
                        {{$job->description}}
                    </p>
                    <ul class="my-4 bg-amber-100 p-4 text-gray-900 rounded-md">
                        <li class="mb-2"><strong>Job Type:</strong> {{$job->job_type}}</li>
                        <li class="mb-2"><strong>Remote:</strong> {{$job->remote ? 'Yes' : 'No'}}</li>
                        <li class="mb-2"><strong>Salary:</strong> {{number_format($job->salary)}}</li>
                        <li class="mb-2"><strong>Site Location:</strong> {{$job->city}}, {{$job->state}}</li>
                        @if($job->tags)
                        <li class="mb-2"><strong>Tags:</strong> {{ucwords(str_replace(',',', ',$job->tags))}}</li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="container mx-auto p-4">
                @if($job->requirements || $job->benefits)
                <div class="rounded-lg shadow-md bg-gray-800 text-amber-100 p-4">
                    <h3 class="text-lg font-bold mb-2">Job Requirements</h3>
                    <p>{{$job->requirements}}</p>
                    <h3 class="text-lg font-bold mt-4 mb-2">Benefits</h3>
                    <p>{{$job->benefits}}</p>
                </div>
                @endif
                @auth
                <p class="my-5">
                  Put "Job Application" as the subject of your email
                  and attach your resume.
                </p>
                <div x-data="{ open: false }" id="applicant-form">
                  <button @click="open = true" class="block w-full text-center px-5 py-2.5 shadow-sm rounded border text-base font-medium cursor-pointer text-white bg-amber-500 hover:bg-amber-600">
                    Apply Now
                  </button>
                  <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
                    <div @click.away="open = false" class="bg-gray-800 text-white p-6 rounded-lg shadow-md w-full max-w-md max-h-[90vh] overflow-y-auto">
                      <h3 class="text-lg font-semibold mb-4">Apply For {{$job->title}}</h3>
                      <form method="POST" action="{{route('applicant.store', $job->id)}}" enctype="multipart/form-data">
                        @csrf
                        <x-inputs.text id="full_name" name="full_name" label="Full Name" :required="true" />
                        <x-inputs.text id="contact_phone" name="contact_phone" label="Contact Phone" />
                        <x-inputs.text id="contact_email" name="contact_email" label="Contact Email" :required="true" />
                        <x-inputs.text-area id="message" name="message" label="Message" />
                        <x-inputs.text id="location" name="location" label="Location" />
                        <x-inputs.file id="resume" name="resume" label="Upload Your Resume (pdf)" :required="true" />
                        <div class="flex justify-between items-center">
                            <button @click="open = false" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded-md">Cancel</button>
                            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-md">Submit Application</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                @endauth
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <div id="map"></div>
            </div>
        </section>
        <aside class="bg-gray-900 text-amber-100 rounded-lg shadow-md p-3">
            <h3 class="text-xl text-center mb-4 font-bold">Company Info</h3>
            @if($job->company_logo)
            <img src="/storage/{{$job->company_logo}}" alt="{{$job->company_name}}" class="w-full rounded-lg mb-4 m-auto" />
            @endif
            <h4 class="text-lg font-bold">{{$job->company_name}}</h4>
            @if($job->company_description)
            <p class="text-gray-300 text-lg my-3">{{$job->company_description}}</p>
            @endif
            @if($job->company_website)
            <a href="{{$job->company_website}}" target="_blank" class="text-blue-400">Visit Website</a>
            @endif
        </aside>
    </div>
</x-layout>

<link
  href="https://api.mapbox.com/mapbox-gl-js/v2.7.0/mapbox-gl.css"
  rel="stylesheet"
/>
<script src="https://api.mapbox.com/mapbox-gl-js/v2.7.0/mapbox-gl.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Your Mapbox access token
    mapboxgl.accessToken = "{{ env('MAPBOX_API_KEY') }}";

    // Initialize the map
    const map = new mapboxgl.Map({
      container: 'map', // ID of the container element
      style: 'mapbox://styles/mapbox/streets-v11', // Map style
      center: [-74.5, 40], // Default center
      zoom: 9, // Default zoom level
    });

    // Get address from Laravel view
    const city = '{{ $job->city }}';
    const state = '{{ $job->state }}';
    const address = city + ', ' + state;

    // Geocode the address
    fetch(`/geocode?address=${encodeURIComponent(address)}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.features.length > 0) {
          const [longitude, latitude] = data.features[0].center;

          // Center the map and add a marker
          map.setCenter([longitude, latitude]);
          map.setZoom(14);

          new mapboxgl.Marker().setLngLat([longitude, latitude]).addTo(map);
        } else {
          console.error('No results found for the address.');
        }
      })
      .catch((error) => console.error('Error geocoding address:', error));
  });
</script>