@extends('layouts.auth')

@section('content')
    @auth
        <form action="{{ route('logOut') }}" method="post">
            @csrf
            @method('DELETE')

            <button type="submit">Выйти</button>
        </form>
    @endauth
@endsection
