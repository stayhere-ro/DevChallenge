@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="booking-hero">
                <span class="booking-hero__eyebrow">StayHere Salon</span>
                <h1 class="booking-hero__title">Book your appointment</h1>
                <p class="booking-hero__subtitle">
                    Choose your stylist, pick an available slot, and confirm in under a minute.
                    Slots refresh live while you browse.
                </p>
            </div>

            @livewire('booking-wizard')
        </div>
    </div>
</div>
@endsection
