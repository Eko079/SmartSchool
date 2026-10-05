{{-- Flash mobile: sukses / error jujur, tanpa dummy --}}
@if(session('success'))
    <div class="m-flash m-flash-ok" role="alert">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="m-flash m-flash-err" role="alert">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="m-flash m-flash-err" role="alert">
        <ul class="m-flash-list">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
