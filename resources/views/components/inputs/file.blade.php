@props(['id','name','label'=> null,'required' => false])

<div class="mb-4">
    @if($label)
    <label class="block text-amber-100" for="{{$id}}"
        >{{$label}}</label
    >
    @endif
    <span class="text-gray-300">
    <input
        id="{{$id}}"
        type="file"
        name="{{$name}}"
        class="w-full px-4 py-2 border rounded focus:outline-none  @error($name) border-red-500 @enderror"
        {{$required ? 'required' : ''}}
    />
    </span>
    @error($name)
    <p class="text-red-500 text-sm mt-1">{{$message}}</p>
    @enderror
</div>