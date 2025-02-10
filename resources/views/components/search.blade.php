<form class="block mx-5 space-y-2 md:space-y-0 md:flex md:items-center md:justify-center md:gap-2 lg:mx-auto" method="GET" action="{{route('jobs.search')}}">
    <input
        type="text"
        name="keywords"
        placeholder="Keywords"
        class="w-full md:w-72 px-4 py-3 focus:outline-none border border-gray-300 rounded-md"
        value="{{request('keywords')}}"
    />
    <input
        type="text"
        name="location"
        placeholder="Location"
        class="w-full md:w-72 px-4 py-3 focus:outline-none border border-gray-300 rounded-md"
        value="{{request('location')}}"
    />
    <button
    class="w-full md:w-auto bg-amber-500 hover:bg-amber-600 text-gray-900 px-4 py-3 focus:outline-none rounded-md flex items-center justify-center"    >
        <i class="fa fa-search mr-2"></i> Search
    </button>
</form>


