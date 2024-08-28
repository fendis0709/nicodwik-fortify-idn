@extends('layouts.fortune.auth')

@section('content')
<div class="flex h-screen bg-black">
    <div class="m-auto lg:w-1/3 md:w-1/2 sm:w-1/2">

        <div class="container py-8">
            <img class="mx-auto" src="{{ asset('assets/images/logo-fortune.svg') }}" />
        </div>

        <div class="font-oswald p-8 bg-white">
            @session('success')
                <x-alert class="text-base" type="publish" text="{{ session('success') }}" />
            @endsession

            @if (count($errors) > 0)
                @foreach ($errors->all() as $error)
                    <x-alert class="text-base" type="inactive" text="{!! $error !!}" />
                @endforeach
            @endif

            <br>
            <h1 class="font-medium text-3xl">
                Mohon lakukan langkah dibawah secara berurutan:
            </h1>
            <br>
            <ol>
                <li><p class="text-base mb-2">1. Masuk ke Google Playstore atau Appstore dan download aplikasi ”Google Authenticator”</p></li>
                <li><p class="text-base mb-2">2. Login ke aplikasi tersebut menggunakan email idntimes yang digunakan untuk login di CMS</p></li>
                <li><p class="text-base mb-2">3. Klik tombol “Add Code” dan lanjutkan dengan “Scan a QR Code”</p></li>
                <li><p class="text-base mb-2">4. Masuk ke email dan cari email dengan judul “[FORTUNE] INVITATION TO SCAN QR CODE”</p></li>
                <li><p class="text-base mb-2">5. Buka email tersebut dan scan QR Code menggunakan google authenticator sampai muncul random number</p></li>
                <li><p class="text-base mb-2">6. Klik tombol “Verify” di email"</p></li>
                <li><p class="text-base mb-2">7. Lanjutkan dengan klik tombol “Ke Halaman Selanjutnya” di bawah</p></li>
            </ol>
            {{-- COMPONENT BUTTON --}}

            <div class="text-center mt-10">
                <form 
                    action="{{ route('two-factor.resend-email') }}"
                    method="post"
                    @session('resendEmailTimer')
                        x-data="{
                            seconds: {{ session()->get('resendEmailTimer') }},
                            interval: null,
                            disableButton: false,
                            init(seconds) {
                                this.seconds = seconds;
                                this.disableButton = true;
                                this.interval = setInterval(() => {
                                    this.seconds--
                                }, 1000);
                            },
                            getCurrentTimer() {
                                if (this.seconds <= 0) {
                                    clearInterval(this.interval)
                                    this.disableButton = false;
                                }
                                return this.seconds > 0 
                                    ? this.seconds
                                    : ''
                            }
                        }" 
                        x-init="init({{ session()->get('resendEmailTimer') }})"
                    @endsession
                    >
                    @csrf
                    <button 
                        type="submit"
                        @if (session()->has('resendEmailTimer'))
                            x-bind:disabled="disableButton"
                           :class="! disableButton ? 'font-medium text-blue-700 hover:underline' : 'text-blue-400 hover:no-underline cursor-not-allowed'"
                        @else
                            class="font-medium text-blue-700 hover:underline"
                        @endif
                        >
                        KIRIM ULANG EMAIL
                    </button>
                    @session('resendEmailTimer')
                        <span class="text-black-300" x-text="getCurrentTimer()"></span>
                    @endsession
                </form>
                <form
                    action="{{ route('two-factor.proceed') }}" 
                    method="post"
                    >
                    @csrf
                    <x-button
                        class="mt-2"
                        type="submit"
                        buttonClass="black"
                        >
                        KE HALAMAN SELANJUTNYA
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection