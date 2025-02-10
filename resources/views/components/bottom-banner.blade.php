@props(['heading'=>'Looking to hire?','subheading'=>'Post your job listing now and find the perfect candidate.'])

<section class="container mx-auto my-6">
    <div class="bg-gray-900 text-amber-100 rounded p-4 flex items-center justify-between flex-col md:flex-row gap-4">
        <div>
            <h2 class="text-xl font-semibold text-amber-300">{{$heading}}</h2>
            <p class="text-gray-400 text-lg mt-2">
                {{$subheading}}
            </p>
        </div>
        <x-button-link url="/jobs/create" icon="edit" class="bg-amber-500 hover:bg-amber-600 text-gray-900">Create Job</x-button-link>
    </div>
</section>