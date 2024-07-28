<div>
    <table class="table-list mt-5 ">
        <thead class="">
            <tr>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Precio detal</th>
                <th>Precio mayorista</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($getState() as $product)
                <tr>
                    <td>
                        {{ $product->barcode }}
                    </td>
                    <td>
                        {{ $product->name }}
                    </td>
                    <td class=" px-3 py-2.5  text-right">
                        {{ $product->pivot->quantity }}
                    </td>
                    <td>
                        $ {{ Number::format($product->price) }}
                    </td>
                    <td>
                        $ {{ Number::format($product->price_wholesale) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
