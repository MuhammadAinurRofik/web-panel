@extends('layouts.app')

@section('content')

<div class="container mx-auto mt-20 px-4 py-6">
    <h2 class="text-3xl font-bold mb-6">Keranjang Belanja</h2>

    @if (empty($cart))
        <p class="text-lg text-gray-600">Keranjang Anda kosong.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($cart as $id => $item)
                <div class="bg-white border border-gray-300 rounded-lg shadow-md bg-no-repeat bg-center bg-cover p-4 relative" style="background-image: url('{{ asset('images/cardimg2.png') }}');">
                    <!-- Tombol Hapus di sudut kanan atas -->
                    <form action="{{ route('cart.destroy', $id) }}" method="POST" class="absolute top-2 right-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-black hover:text-red-600">
                            <i class="fas fa-trash-alt"></i> <!-- Ikon Hapus -->
                        </button>
                    </form>

                    <h3 class="font-medium text-xl mb-2">{{ $item['name'] }}</h3>

                    <div class="mb-4 flex items-center space-x-2">
                        <span class="text-sm text-gray-600">Jumlah:</span>
                        <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center">
                            @csrf
                            @method('PUT')
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="border border-gray-300 p-2 rounded-lg w-16">
                            <button type="submit" class="p-2 text-black hover:text-blue-600">
                                <i class="fas fa-sync-alt"></i> <!-- Ikon Update -->
                            </button>
                        </form>
                    </div>

                    <div class="mb-4">
                        <span class="block text-sm text-gray-600">Harga:</span>
                        <p>Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>

                    <div class="mb-4">
                        <span class="block text-sm text-gray-600">Total:</span>
                        <p>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Tombol WhatsApp dan Total Harga -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            @php
                $whatsappMessage = 'Halo, saya ingin memesan barang berikut:%0A';
                foreach ($cart as $item) {
                    $whatsappMessage .= "- {$item['name']} x {$item['quantity']} = Rp " . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "%0A";
                }
                $whatsappMessage .= "%0ATotal: Rp " . number_format($total, 0, ',', '.');
                $whatsappLink = 'https://wa.me/6285157110104?text=' . $whatsappMessage;
            @endphp

            <!-- Tombol WhatsApp -->
            <a href="{{ $whatsappLink }}" target="_blank" 
            class="w-full sm:w-auto bg-green-500 text-white text-center px-4 py-2 rounded-lg shadow hover:bg-green-600">
                Hubungi Penjual
            </a>

            <!-- Total Harga -->
            <p class="text-lg font-bold text-black text-center sm:text-right">
                Total Harga: Rp {{ number_format($total, 0, ',', '.') }}
            </p>
        </div>
    @endif
</div>
@endsection
