@extends('layouts.app')

@section('title', 'LilBaby - Home')

@section('content')
<!-- Header -->
<div class="h-screen text-white bg-no-repeat bg-center bg-cover relative" style="background-image: url('{{ asset('images/babyimg.png') }}');">
    <div class="container mx-auto text-left p-8 rounded-lg absolute top-1/2 transform -translate-y-1/2">
        <h1 class="text-5xl font-bold">ESSENTIALS FOR LITTLE ONES</h1>
        <p class="mt-2 text-lg">
            Temukan berbagai produk kebutuhan bayi, mulai dari pakaian, mainan, hingga perlengkapan tidur, <br> 
            dirancang untuk kenyamanan dan kebahagiaan Si Kecil.
        </p>
        <a href="/produk">
            <button class="bg-transparent text-white font-bold mt-5 py-2 px-10 rounded border border-white hover:bg-[#87A2FF] transition">
                Order
            </button>
        </a>
    </div>
</div>


<div class="container mx-auto my-8 p-4">
    <!-- We Offer -->
    <section class="p-8 flex items-center justify-center flex-col">
        <h2 class="text-5xl font-bold text-[#87A2FF] mt-4 mb-4 text-center">What We Offer</h2>
        <p class="text-[#28527A] text-lg text-center">
            kami menyediakan berbagai produk berkualitas untuk bayi dan anak, mulai dari pakaian, mainan, <br>  
            mulai dari pakaian, mainan, hingga perlengkapan tidur.
        </p>
    </section>
    
    <!-- Image Section -->
    <section class="mb-20 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="flex flex-col justify-center items-center">
            <img src="{{ asset('images/baby1.png') }}" alt="Produk Kerajinan Kayu" class="rounded-lg w-60 h-auto">
            <h1 class="mt-3 mb-3 text-[#28527A] font-bold">Pakaian Bayi dan Anak</h1>
            <p class="text-[#28527A] text-lg text-center">
                Pilihan pakaian yang nyaman <br>
                dan stylish untuk Si Kecil.
            </p>
        </div>
        <div class="flex flex-col justify-center items-center">
            <img src="{{ asset('images/baby2.png') }}" alt="Produk Kerajinan Kayu" class="rounded-lg w-60 h-auto">
            <h1 class="mt-3 mb-3 text-[#28527A] font-bold">Mainan Edukatif</h1>
            <p class="text-[#28527A] text-lg text-center">
                Mainan yang mendukung tumbuh <br>
                kembang dan kreativitas anak.
            </p>
        </div>
        <div class="flex flex-col justify-center items-center">
            <img src="{{ asset('images/baby3.png') }}" alt="Produk Kerajinan Kayu" class="rounded-lg w-60 h-auto">
            <h1 class="mt-3 mb-3 text-[#28527A] font-bold">Perlengkapan Tidur</h1>
            <p class="text-[#28527A] text-lg text-center">
                Kasur, selimut, dan bantal dengan <br>
                 bahan lembut dan aman.
            </p>
        </div>
    </section>
</div>


<div class="mx-auto my-8 p-4">
    <!-- Why Choose Us -->
    <section>
        <div class="grid grid-cols-1 md:grid-cols-2 items-center">
            <!-- Deskripsi -->
            <div class="px-8 md:pl-32">
                <h2 class="text-5xl font-bold text-[#87A2FF] mb-8 text-center">Why Choose Us?</h2>
                <p class="text-[#28527A] text-lg text-justify">
                Di LilBaby, kami berkomitmen untuk memberikan yang terbaik bagi Anda dan Si Kecil. Dengan fokus pada kualitas, keamanan, dan kenyamanan, semua produk kami dirancang untuk memenuhi kebutuhan bayi dan anak dengan standar tinggi. Kami juga menyediakan pengalaman belanja yang mudah, harga terjangkau, serta pelayanan yang ramah dan profesional.
                </p>
            </div>
            <!-- Gambar -->
            <div class="flex justify-center items-center">
                <img src="{{ asset('images/babyimg2.png') }}" alt="Produk Berkualitas untuk Bayi" class="w-96 h-auto">
            </div>
        </div>
    </section>
</div>

<div class=" p-4 bg-[#C4D7FF]">
    <!-- Our Products -->
    <section class="p-8 flex items-center justify-center flex-col">
        <h2 class="text-5xl font-bold text-[#FEEE91] mt-4 mb-4 text-center">Our Products</h2>
    </section>
    
    <!-- Image Section -->
    <section class="mb-20 grid grid-cols-1 md:grid-cols-4 gap-2">
        <div class="flex flex-col justify-center items-center">
            <img src="{{ asset('images/p1.png') }}" alt="Produk Kerajinan Kayu" class="rounded-lg w-72 h-auto">
        </div>
        <div class="flex flex-col justify-center items-center">
            <img src="{{ asset('images/p2.png') }}" alt="Produk Kerajinan Kayu" class="rounded-lg w-72 h-auto">
        </div>
        <div class="flex flex-col justify-center items-center">
            <img src="{{ asset('images/p3.png') }}" alt="Produk Kerajinan Kayu" class="rounded-lg w-72 h-auto">
        </div>
        <div class="flex flex-col justify-center items-center">
            <img src="{{ asset('images/p4.png') }}" alt="Produk Kerajinan Kayu" class="rounded-lg w-72 h-auto">
        </div>
    </section>
    <div class="flex justify-center mt-5 mb-10">
        <a href="/produk">
            <button class="bg-transparent text-[#28527A] font-bold py-2 px-10 rounded border border-[#28527A] hover:bg-[#87A2FF] transition">
            Show all Product
            </button>
        </a>
    </div>
</div>

@endsection
