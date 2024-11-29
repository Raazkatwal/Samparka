<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <h1 class="font-bold text-4xl tracking-wide">Click on a User to Chat:</h1>
                <div class="p-6 text-gray-900">
                    @foreach ($users as $user)
                    <p class="text-3xl my-4">
                            <a href="{{ route('chat', $user->id) }}" class="drag-none">{{ $user->name }} </a>
                    </p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
