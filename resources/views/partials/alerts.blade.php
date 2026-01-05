@if(session('success'))
<div class="alert alert-success">
    <span class="alert-icon">[OK]</span>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('rows_updated'))
<div class="alert alert-success">
    <span class="alert-icon">[OK]</span>
    <span>Scraping completed. {{ session('rows_updated') }} rows updated.</span>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning">
    <span class="alert-icon">[!]</span>
    <span>{{ session('warning') }}</span>
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    <span class="alert-icon">[X]</span>
    <span>{{ session('error') }}</span>
</div>
@endif

@if($errors->any())
<div class="alert alert-error">
    <span class="alert-icon">[X]</span>
    <ul style="margin:0;padding-left:20px">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

