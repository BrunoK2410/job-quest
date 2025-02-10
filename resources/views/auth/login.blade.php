<x-layout>
  <div class="bg-gray-900  rounded-lg shadow-md w-full md:max-w-xl mx-auto mt-12 p-8 py-12">
      <h2 class="text-4xl text-center font-bold text-amber-300 mb-4">Login</h2>
      <form method="POST" action="{{route('login.authenticate')}}">
          @csrf
          <x-inputs.text name="email" placeholder="Email Address" id="email" />
          <x-inputs.text name="password" placeholder="Password" type="password" id="password" />
          <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-gray-900 px-4 py-2 rounded focus:outline-none"> Login </button>
          <p class="mt-4 text-gray-400"> Don't have an account? <a class="text-amber-300" href="{{route('register')}}">Register</a>
          </p>
      </form>
  </div>
</x-layout>