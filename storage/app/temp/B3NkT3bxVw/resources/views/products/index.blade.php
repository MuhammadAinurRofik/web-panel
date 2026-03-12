@extends('layouts.app')

@section('title', 'LilBaby - Produk')

@section('content')
<div class="h-screen text-right text-gray-400 bg-no-repeat bg-center bg-cover relative" style="background-image: url('{{ asset('images/babyimg3.jpg') }}');">
    <div class="container mx-auto text-right p-8 rounded-lg absolute top-1/2 transform -translate-y-1/2">
        <h1 class="text-5xl font-bold">PRODUCT US</h1>
        <!-- <h1 class="text-5xl font-bold">PERFECTLY DESIGNED</h1> -->
        <!-- <h1 class="text-5xl font-bold">FOR YOUR LITTLE ONE</h1> -->
        <p class="mt-2 text-lg">
            Temukan berbagai produk kebutuhan bayi, semuanya dirancang dengan baik <br> 
            untuk memastikan kenyamanan, keamanan, dan kebahagiaan Si Kecil.
        </p>
        <button class="bg-transparent text-gray-400 font-bold mt-5 py-2 px-10 rounded border border-gray-400 hover:bg-gray-300 transition" 
                onclick="document.getElementById('popup-pemesanan').classList.remove('hidden')">
            Cara Pemesanan
        </button>
    </div>
</div>

<div class="container mx-auto my-8">
    <h2 class="text-4xl font-bold text-[#87A2FF] mt-20 mb-10 text-center">Our Products</h2>

    <!-- Filter Kategori -->
    <div class="text-center mb-8">
    <!-- <h2 class="text-[#87A2FF] font-bold mb-4">Filter by Category:</h2> -->
        <div class="flex flex-wrap justify-center space-x-2 md:space-x-4">
            <!-- Tautan untuk semua kategori -->
            <a href="{{ route('products.index') }}" 
            class="{{ request('category') ? 'text-gray-500' : 'text-[#87A2FF] font-bold underline' }} block px-3 py-2 text-sm md:text-base hover:text-[#87A2FF] rounded">
                All Categories
            </a>

            <!-- Tautan untuk kategori Baju -->
            <a href="{{ route('products.index', ['category' => 'baju']) }}" 
            class="{{ request('category') == 'baju' ? 'text-[#87A2FF] font-bold underline' : 'text-gray-500' }} block px-3 py-2 text-sm md:text-base hover:text-[#87A2FF] rounded">
                Baju
            </a>

            <!-- Tautan untuk kategori Mainan -->
            <a href="{{ route('products.index', ['category' => 'mainan']) }}" 
            class="{{ request('category') == 'mainan' ? 'text-[#87A2FF] font-bold underline' : 'text-gray-500' }} block px-3 py-2 text-sm md:text-base hover:text-[#87A2FF] rounded">
                Mainan
            </a>

            <!-- Tautan untuk kategori Perlengkapan Tidur -->
            <a href="{{ route('products.index', ['category' => 'perlengkapan_tidur']) }}" 
            class="{{ request('category') == 'perlengkapan_tidur' ? 'text-[#87A2FF] font-bold underline' : 'text-gray-500' }} block px-3 py-2 text-sm md:text-base hover:text-[#87A2FF] rounded">
                Perlengkapan Tidur
            </a>

            <!-- Tautan untuk kategori Perlengkapan Lain -->
            <a href="{{ route('products.index', ['category' => 'perlengkapan_lain']) }}" 
            class="{{ request('category') == 'perlengkapan_lain' ? 'text-[#87A2FF] font-bold underline' : 'text-gray-500' }} block px-3 py-2 text-sm md:text-base hover:text-[#87A2FF] rounded">
                Perlengkapan Lainnya
            </a>
        </div>
    </div>



    <!-- Produk List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse ($products as $product)
        <div class="rounded-lg shadow-lg hover:shadow-md transition-shadow duration-300 p-4 flex flex-col justify-between">
            <!-- Gambar Produk -->
            <div class="relative w-full h-48 flex justify-center items-center">
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="rounded-lg object-contain h-full">
            </div>

            <!-- Informasi Produk -->
            <div class="mt-4 flex flex-col items-start">
                <h3 class="text-lg font-semibold text-[#87A2FF] truncate w-full">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($product->description, 50) }}</p>
            </div>

            <!-- Harga dan Tombol -->
            <div class="mt-4 flex items-center justify-between w-full">
                <!-- Harga -->
                <div class="text-xl font-bold text-[#87A2FF]">
                    Rp{{ number_format($product->price, 0, ',', '.') }}
                </div>

                <!-- Tombol Keranjang -->
                <form action="{{ route('cart.store') }}" method="POST" class="ml-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="p-2 text-[#87A2FF] hover:text-[#C4D7FF] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#87A2FF]">
                        <i class="fas fa-shopping-cart"></i> <!-- Ikon Keranjang -->
                    </button>
                </form>
            </div>
        </div>
        @empty
            <p class="text-center text-gray-600 col-span-full">Tidak ada produk dalam kategori ini.</p>
        @endforelse
    </div>
</div>

<style>
    .scrollbar-none {
    -ms-overflow-style: none; /* Internet Explorer 10+ */
    scrollbar-width: none; /* Firefox */
    }
    .scrollbar-none::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
</style>

<!-- Pop-up -->
<div id="popup-pemesanan" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg p-8 shadow-lg w-11/12 md:w-3/4 lg:w-1/2 max-h-[90vh] overflow-y-auto scrollbar-none">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold mb-6 text-center">Cara Pemesanan Produk</h1>

            <!-- Langkah-langkah Pemesanan -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Panduan Pemesanan</h2>
                <img src="{{ asset('images/order.png') }}" alt="Panduan Pemesanan" class="w-full h-auto mb-4 rounded-lg shadow-md">
            </div>

            <!-- Informasi Tambahan -->
            <div class="mt-6 bg-gray-50 p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4">Metode Pembayaran</h2>
                <p class="text-gray-700">
                    Kami mendukung pembayaran melalui:
                </p>
                <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                    <li>Transfer Bank (BCA, Mandiri, BRI, dll)</li>
                    <li>E-Wallet (OVO, GoPay, Dana)</li>
                    <li>QRIS</li>
                </ul>
            </div>
        </div>

        <!-- Tombol Tutup -->
        <button class="block bg-[#87A2FF] text-white font-bold py-2 px-6 rounded hover:bg-[#87A2FF] mt-4 mx-auto"
                onclick="document.getElementById('popup-pemesanan').classList.add('hidden')">
            Tutup
        </button>
    </div>
</div>
@endsection
