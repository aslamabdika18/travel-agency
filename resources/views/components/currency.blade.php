@props(['amount', 'withSymbol' => true, 'class' => ''])

<span {{ $attributes->merge(['class' => $class]) }}>
    {{ formatRupiah($amount ?? 0, $withSymbol) }}
</span>