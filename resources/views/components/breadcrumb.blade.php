@props(['items' => []])

<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">


        @foreach($items as $index => $item)
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    
                    @if($loop->last)
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $item['label'] }}</span>
                    @else
                        <a href="{{ $item['url'] ?? '#' }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-[#1a6341] md:ml-2 transition-colors">
                            {{ $item['label'] }}
                        </a>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
