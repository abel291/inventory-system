<div>

    <table class="table-list mt-10">
        <thead>
            <tr>
                <th>Codigo</th>
                <th class="">Nombre</th>
                <th>Costo</th>
                <th>Cantidad</th>
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
                    <td>
                        $ {{ Number::format($product->pivot->cost) }}
                    </td>
                    {{-- <td >
                        {{ Number::currency($product->price) }}
                    </td> --}}
                    <td>
                        {{ $product->pivot->quantity }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
