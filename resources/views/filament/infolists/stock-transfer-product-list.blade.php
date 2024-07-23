<div>
    <table class="w-full  text-sm leading-6 mt-5 ">
        <thead class="border-y dark:border-white/5">
            <tr>
                <th class=" whitespace-nowrap font-medium text-left px-3 py-3">Codigo</th>
                <th class=" whitespace-nowrap font-medium text-left px-3 py-3">Nombre</th>
                <th class=" whitespace-nowrap font-medium text-left px-3 py-3">Cantidad</th>
                <th class=" whitespace-nowrap font-medium text-left px-3 py-3">Precio detal</th>
                <th class=" whitespace-nowrap font-medium text-left px-3 py-3">Precio mayorista</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-900/10 dark:divide-white/5 ">
            @foreach ($getState() as $product)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                    <td class=" px-3 py-2.5 ">
                        {{ $product->barcode }}
                    </td>
                    <td class=" px-3 py-2.5 ">
                        {{ $product->name }}
                    </td>
                    <td class=" px-3 py-2.5  text-right">
                        {{ $product->pivot->quantity }}
                    </td>
                    <td class=" px-3 py-2.5 ">
                        {{ Number::currency($product->price) }}
                    </td>
                    <td class=" px-3 py-2.5 ">
                        {{ Number::currency($product->price_wholesale) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
