<div>
    <table class="table-list mt-5">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($getState() as $product)
                <tr>
                    <td>
                        {{ $product->name }}
                    </td>
                    <td>
                        $ {{ Number::format($product->pivot->price, 2) }}
                    </td>
                    <td align="center">
                        {{ $product->pivot->quantity }}
                    </td>
                    <td>
                        $ {{ Number::format($product->pivot->total, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pt-10 flex justify-end border-t border-neutral-200 dark:border-white/20">
        <dl class="sm:max-w-sm w-80 font-medium text-sm space-y-5 ">
            <x-descripction-list title="Sub total" :description="'$ ' . Number::format($getRecord()->subtotal, 2)" />
            @if ($getRecord()->discount)
                <x-descripction-list title="Descuento" :description="'-$ ' . Number::format($getRecord()->discount['amount'], 2)" />
            @endif
            <x-descripction-list title="Envio" :description="'$ ' . Number::format($getRecord()->delivery, 2)" />
            <x-descripction-list title="Total" :description="'$ ' . Number::format($getRecord()->total, 2)" />
        </dl>
    </div>



</div>
