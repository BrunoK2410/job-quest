@if($paginator->hasPages())
<nav class="flex justify-center" role="navigation">
    @if($paginator->onFirstPage())
    <span class="px-4 py-2 bg-gray-700 text-gray-400 rounded-l-lg">Previous</span>
    @else
    <a href="{{$paginator->previousPageUrl()}}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-gray-900 rounded-l-lg">
        Previous
    </a>
    @endif

    @foreach ($elements as $element)
    @if (is_string($element))
    <span class="px-4 py-2 bg-gray-700 text-gray-400">{{ $element }}</span>
    @endif
    @if (is_array($element))
    @foreach ($element as $page => $url)
    @if ($page == $paginator->currentPage())
    <span class="px-4 py-2 bg-amber-500 text-gray-900">{{ $page }}</span>
    @else
    <a href="{{ $url }}" class="px-4 py-2 bg-gray-800 text-amber-100 hover:bg-amber-500 hover:text-gray-900">{{ $page }}</a>
    @endif
    @endforeach
    @endif
    @endforeach

    @if($paginator->hasMorePages())
    <a href="{{$paginator->nextPageUrl()}}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-gray-900 rounded-r-lg">
        Next
    </a>
    @else
    <span class="px-4 py-2 bg-gray-700 text-gray-400 rounded-r-lg">Next</span>
    @endif
</nav>
@endif
