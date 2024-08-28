@extends('layouts.fortune.auth')

@section('title')
    2FA Challenge
@endsection

@section('content')
<div class="flex h-screen bg-black">
    <div class="m-auto lg:w-1/4 md:w-1/2 sm:w-1/2">
        <form
            method="POST"
            action="{{ route('two-factor.recovery-code') }}"
            class="mx-auto"
            x-data="{
                disableSubmit: true,
                inputRecoveryCode: '{{ old('recovery_code') }}',
            }">

            @csrf

            <div class="container py-8">
                <img class="mx-auto" src="{{ asset('assets/images/logo-fortune.svg') }}" />
            </div>

            <div class="font-oswald p-10 bg-white">
                <h1 class="font-medium text-3xl">
                    Masukkan Recovery Code di bawah
                </h1>

                {{-- 2FA CHALLENGE --}}
                <label for="recovery_code" class="mt-6 mb-2 block">
                    Recovery Code
                </label>
                <input
                    class="appearance-none w-full py-3 px-3 leading-tight border-gray-600 focus:ring-black
                        focus:border-black pr-16 font-opensans
                        @error('recovery_code') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                    id="recovery_code"
                    name="recovery_code"
                    type="text"
                    x-model="inputRecoveryCode"
                    autocomplete="off"
                    autofocus
                    onKeyPress="if(this.value.length == 21) return false;"
                    @input="[inputRecoveryCode.trim().length != 0 ? disableSubmit = false : disableSubmit = true]"
                    />
                @error('recovery_code')
                    <span class="text-red-500 text-xs font-semibold font-opensans">{{ $message }}</span>
                @enderror

                <div class="mt-10">
                    <a class="font-medium text-blue-700 hover:underline" 
                    href="{{ route('two-factor.login') }}">Kembali ke halaman sebelumnya</a>
                    <br>
                    <x-button
                        class="mt-2"
                        type="submit"
                        buttonClass="black"
                        ::class="disableSubmit ? 'btn-disabled cursor-default' : ''"
                        x-bind:disabled="disableSubmit"
                        >KIRIM
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
