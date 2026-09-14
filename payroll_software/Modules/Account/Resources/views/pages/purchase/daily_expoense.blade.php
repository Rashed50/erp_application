@extends('layouts.admin-master')
@section('title')Daily Expense @endsection
@section('content')
<div id="app">
        <daily-expense-component-manager :data="{{ json_encode($data) }}"></daily-expense-component-manager>
</div>


<script>
     // Test Vue instance
    
    console.log('Testing Vue...');
    console.log('Vue available?', typeof Vue !== 'undefined');

    // Test if element exists
    const element = document.getElementById('app');
    console.log('App element:', element);

    // Check for Vue apps on page
    if (window.__VUE_DEVTOOLS_GLOBAL_HOOK__) {
        console.log('Vue DevTools detected');
    }
    console.log('Data from controller:', @json($data));
</script>

@endsection
