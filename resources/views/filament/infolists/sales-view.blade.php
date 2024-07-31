<div>
    <div class=" table-list-wrp">
        <table class="table-list">
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
                        <td class="whitespace-nowrap">
                            {{ Number::currency($product->pivot->price, locale: 'de') }}
                        </td>
                        <td align="center">
                            {{ $product->pivot->quantity }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ Number::currency($product->pivot->total, locale: 'de') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pt-8 flex justify-end ">
        <dl class="sm:max-w-sm w-80 font-medium text-sm space-y-5 ">
            <x-descripction-list title="Sub total" :description="Number::currency($getRecord()->subtotal)" />
            @if ($getRecord()->discount)
                <x-descripction-list :title="'Descuento ' . $getRecord()->discount['percent'] . '%'" :description="'-$ ' . Number::format($getRecord()->discount['amount'])" />
            @endif
            <x-descripction-list title="Envio" :description="Number::currency($getRecord()->delivery)" />
            <x-descripction-list title="Total" :description="Number::currency($getRecord()->total, in: 'USD', locale: 'de')" />
        </dl>
    </div>



</div>
