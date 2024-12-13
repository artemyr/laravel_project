@extends('layouts.auth')

@section('title', 'Восстановление пароля')

@section('content')
    <x-forms.auth-forms
        title="Забыли пароль"
        action=""
        method="POST"
    >

        <x-forms.text-input
            name="email"
            type="email"
            placeholder="E-mail"
            required="true"
            value="{{ old('email') }}"
            :isError="$errors->has('email')"
        />
        @error('email')
        <x-forms.error>
            {{ $massage }}
        </x-forms.error>
        @enderror

        <x-forms.text-input
            name="password"
            type="password"
            placeholder="Пароль"
            required="true"
            :isError="$errors->has('password')"
        />
        @error('password')
        <x-forms.error>
            {{ $massage }}
        </x-forms.error>
        @enderror

        <x-forms.text-input
            name="password_confirmation"
            type="password"
            placeholder="Повторите пароль"
            required="true"
            :isError="$errors->has('password_confirmation')"
        />
        @error('password_confirmation')
        <x-forms.error>
            {{ $massage }}
        </x-forms.error>
        @enderror

        <x-forms.primary-button>
            Обновить пароль
        </x-forms.primary-button>

        <x-slot:buttons></x-slot:buttons>
    </x-forms.auth-forms>
@endsection
