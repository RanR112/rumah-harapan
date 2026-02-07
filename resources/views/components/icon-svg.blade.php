@props([
    'name',
    'class' => '',
    'width' => '20',
    'height' => '20'
])

@if(file_exists(public_path('images/icons/' . $name . '.svg')))
    @php
        $svgContent = file_get_contents(public_path('images/icons/' . $name . '.svg'));
        // Ubah width/height
        $svgContent = preg_replace('/width="\d+"/', 'width="' . $width . '"', $svgContent);
        $svgContent = preg_replace('/height="\d+"/', 'height="' . $height . '"', $svgContent);
        // Tambahkan class
        $svgContent = preg_replace('/<svg/', '<svg class="' . $class . '"', $svgContent, 1);
    @endphp
    {!! $svgContent !!}
@else
    <!-- Fallback jika SVG tidak ditemukan -->
    <span class="text-gray-500">[{{ $name }}]</span>
@endif