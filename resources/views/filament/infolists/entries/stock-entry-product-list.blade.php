<div class="border border-white/10 rounded-md">

    <table class="w-full  text-sm leading-6">
        <thead>
            <tr>
                <th class="text-left px-3 py-3">Nombre</th>
                <th class="text-left px-3 py-3">Costo</th>
                <th class="text-left px-3 py-3">Precio de venta</th>
                <th class="text-left px-3 py-3">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($getState() as $product)
                <tr class="border-b dark:border-white/10">
                    <td class=" px-3 py-2 ">
                        {{ $product->name }}
                    </td>
                    <td class=" px-3 py-2 ">
                        {{ Number::currency($product->pivot->cost) }}
                    </td>
                    <td class=" px-3 py-2 ">
                        {{ Number::currency($product->price) }}
                    </td>
                    <td class=" px-3 py-2 ">
                        {{ $product->pivot->quantity }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
